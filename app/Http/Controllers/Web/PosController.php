<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        // Load available menus for POS
        $menus = Menu::where('is_available', true)->get();

        return view('pos.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'table_number' => 'nullable|integer',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
            'pay' => 'nullable|in:0,1',
        ]);

        $total = 0;
        // Create order and details
        $order = Order::create([
            'table_number' => $data['table_number'] ?? null,
            'total_price' => 0,
            'status' => 'pending',
        ]);

        foreach ($data['items'] as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            OrderDetail::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => $item['qty'],
                'price' => $menu->price,
            ]);
            $total += $menu->price * $item['qty'];
        }

        $order->total_price = $total;
        $order->save();

        // If pay flag set, pay immediately (deduct stock)
        if (isset($data['pay']) && $data['pay'] == '1') {
            try {
                $order->pay();
                return redirect()->route('pos.show', $order->id)->with('success', 'Order paid and stock updated');
            } catch (\Exception $e) {
                return redirect()->route('pos.show', $order->id)->with('error', $e->getMessage());
            }
        }

        return redirect()->route('pos.show', $order->id)->with('success', 'Order created');
    }

    /**
     * Checkout (immediate payment) from POS cart
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'nullable|integer',
            'cart' => 'required|json',
        ]);

        $items = json_decode($validated['cart'], true);
        if (!is_array($items) || empty($items)) {
            return redirect()->back()->with('error', 'Cart kosong');
        }

        $normalized = [];
        foreach ($items as $idx => $it) {
            if (!is_array($it)) {
                return redirect()->back()->with('error', "Item tidak valid pada index {$idx}");
            }
            $menuId = $it['menu_id'] ?? null;
            $qty = isset($it['qty']) ? (int)$it['qty'] : 0;

            if (!$menuId || $qty <= 0) {
                return redirect()->back()->with('error', "Item tidak lengkap pada index {$idx}");
            }

            $menu = Menu::find($menuId);
            if (!$menu) {
                return redirect()->back()->with('error', "Menu tidak ditemukan (id: {$menuId})");
            }

            $normalized[] = [
                'menu' => $menu,
                'menu_id' => (int) $menuId,
                'qty' => $qty,
            ];
        }

        // New behavior: validate and deduct menu stock only
        try {
            $order = \DB::transaction(function () use ($validated, $normalized) {
                // Validate menu stock availability
                $menuIds = array_column($normalized, 'menu_id');
                $menus = \App\Models\Menu::whereIn('id', $menuIds)->lockForUpdate()->get()->keyBy('id');

                // Build aggregated menu qty
                $agg = [];
                foreach ($normalized as $it) {
                    $mid = $it['menu_id'];
                    $agg[$mid] = ($agg[$mid] ?? 0) + $it['qty'];
                }

                foreach ($agg as $mid => $needQty) {
                    $menu = $menus->get($mid);
                    if (! $menu) {
                        throw new \Exception("Menu tidak ditemukan (id: {$mid})");
                    }
                    if ((int)$menu->stock < (int)$needQty) {
                        throw new \Exception("Stok menu tidak cukup untuk {$menu->name}. Dibutuhkan: {$needQty}, tersedia: {$menu->stock}");
                    }
                }

                // Create order
                $order = Order::create([
                    'table_number' => $validated['table_number'] ?? null,
                    'total_price' => 0,
                    'status' => 'pending',
                ]);

                $total = 0;
                foreach ($normalized as $it) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'menu_id' => $it['menu_id'],
                        'qty' => $it['qty'],
                        'price' => $it['menu']->price,
                    ]);
                    $total = bcadd((string)$total, bcmul((string)$it['menu']->price, (string)$it['qty'], 2), 2);
                }

                // Deduct menu stock
                foreach ($agg as $mid => $needQty) {
                    $menu = $menus->get($mid);
                    $menu->stock = (int)$menu->stock - (int)$needQty;
                    $menu->save();
                    if ($menu->stock <= 0) {
                        $menu->is_available = false;
                        $menu->save();
                    }
                }

                $order->total_price = $total;
                $order->payment_status = 'paid';
                $order->status = 'pending';
                $order->save();

                return $order;
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('pos.receipt', $order->id)->with('success', 'Pembayaran berhasil dan stok menu diperbarui');
    }

    public function show(Order $order)
    {
        $order->load('details.menu');
        return view('pos.show', compact('order'));
    }

    /**
     * Show printable receipt for an order
     */
    public function receipt(Order $order)
    {
        $order->load('details.menu');
        return view('pos.receipt', compact('order'));
    }

    public function pay(Order $order)
    {
        try {
            $order->pay();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('pos.show', $order->id)->with('success', 'Order paid and stock updated');
    }

    public function confirmPayment(Order $order)
    {
        if ($order->payment_method !== 'qris') {
            return redirect()->back()->with('error', 'Konfirmasi pembayaran hanya untuk QRIS');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Pembayaran sudah dikonfirmasi');
        }

        $order->payment_status = 'paid';
        $order->save();

        return redirect()->back()->with('success', 'Pembayaran QRIS berhasil dikonfirmasi');
    }
}
