<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)->with('details.menu')->orderByDesc('created_at')->get();
        return response()->json($orders->map(function($o){
            return [
                'id' => $o->id,
                'date' => $o->created_at->toDateTimeString(),
                'total_price' => $o->total_price,
                'status' => $o->status,
            ];
        }));
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $order->load('details.menu');
        return response()->json($order);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'required|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:cash,qris',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($data, $request) {
                // aggregate menu quantities
                $agg = [];
                foreach ($data['items'] as $it) {
                    $mid = $it['menu_id'];
                    $agg[$mid] = ($agg[$mid] ?? 0) + (int)$it['qty'];
                }

                // lock menus
                $menus = Menu::whereIn('id', array_keys($agg))->lockForUpdate()->get()->keyBy('id');

                // validate stock
                foreach ($agg as $mid => $needQty) {
                    $m = $menus->get($mid);
                    if (! $m) throw new \Exception('Menu not found');
                    if ((int)$m->stock < (int)$needQty) {
                        throw new \DomainException('stok menu tidak cukup');
                    }
                }

                $order = Order::create([
                    'user_id' => optional($request->user())->id,
                    'customer_name' => $data['customer_name'],
                    'table_number' => $data['table_number'],
                    'notes' => $data['notes'] ?? null,
                    'total_price' => 0,
                    'payment_method' => $data['payment_method'],
                    'payment_status' => $data['payment_method'] === 'cash' ? 'unpaid' : 'pending',
                    'status' => 'waiting',
                ]);

                $total = 0;
                foreach ($data['items'] as $it) {
                    $menu = $menus->get($it['menu_id']);
                    $qty = (int)$it['qty'];
                    $subtotal = bcmul((string)$menu->price, (string)$qty, 2);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'qty' => $qty,
                        'price' => $menu->price,
                    ]);

                    $total = bcadd((string)$total, (string)$subtotal, 2);
                }

                // deduct menu stock
                foreach ($agg as $mid => $needQty) {
                    $m = $menus->get($mid);
                    $m->stock = (int)$m->stock - (int)$needQty;
                    $m->save();
                    if ($m->stock <= 0) {
                        $m->is_available = false; $m->save();
                    }
                }

                $order->total_price = $total;
                $order->save();

                return $order;
            });
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'order_id' => $order->id], 201);
    }

    public function qris()
    {
        $setting = PaymentSetting::first();

        return response()->json([
            'qris_url' => $setting?->qris_url,
        ]);
    }
}
