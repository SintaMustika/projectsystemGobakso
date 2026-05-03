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

        // Build aggregated ingredient requirements from cart
        $requirements = [];
        $requirements_meta = []; // for logging which menus contributed
        foreach ($normalized as $it) {
            $recipesQuery = \App\Models\Recipe::where('menu_id', $it['menu_id']);
            Log::info('Recipe query for menu', ['menu_id' => $it['menu_id'], 'sql' => $recipesQuery->toSql(), 'bindings' => $recipesQuery->getBindings()]);
            $recipes = $recipesQuery->get();

            Log::info('Recipes fetched', ['menu_id' => $it['menu_id'], 'count' => $recipes->count()]);

            if ($recipes->isEmpty()) {
                $menuName = optional($it['menu'])->name ?? 'unknown';
                $msg = 'Menu belum memiliki resep bahan';
                Log::error('Menu belum memiliki resep bahan', ['menu_id' => $it['menu_id'], 'menu_name' => $menuName]);
                return redirect()->back()->with('error', $msg);
            }

                foreach ($recipes as $recipe) {
                $ingId = $recipe->ingredient_id;
                $need = bcmul((string)$recipe->qty_usage, (string)$it['qty'], 3);

                Log::info('Recipe item contribution', [
                    'menu_id' => $it['menu_id'],
                    'menu_name' => optional($it['menu'])->name ?? null,
                    'ingredient_id' => $ingId,
                    'qty_usage' => (string)$recipe->qty_usage,
                    'qty_order' => $it['qty'],
                    'need' => $need,
                ]);

                if (isset($requirements[$ingId])) {
                    $requirements[$ingId] = bcadd($requirements[$ingId], $need, 3);
                } else {
                    $requirements[$ingId] = $need;
                }

                $requirements_meta[$ingId][] = [
                    'menu_id' => $it['menu_id'],
                    'menu_name' => optional($it['menu'])->name ?? null,
                    'recipe_qty_usage' => (string)$recipe->qty_usage,
                    'need' => $need,
                ];
            }
        }

        try {
            $order = \DB::transaction(function () use ($validated, $normalized, $requirements) {
                // Lock ingredient rows first and verify availability
                if (!empty($requirements)) {
                    $ingredients = \App\Models\Ingredient::whereIn('id', array_keys($requirements))->lockForUpdate()->get()->keyBy('id');
                } else {
                    $ingredients = collect();
                }

                // Validate stock
                foreach ($requirements as $ingId => $need) {
                    $ingredient = $ingredients->get($ingId);
                    if (! $ingredient) {
                        $meta = $requirements_meta[$ingId] ?? null;
                        $msg = "Bahan tidak ditemukan (id: {$ingId})" . ($meta ? ' contributed by menus: ' . implode(',', array_map(function($m){return $m['menu_id'];}, $meta)) : '');
                        Log::error($msg, ['ingredient_id' => $ingId, 'meta' => $meta]);
                        throw new \Exception($msg);
                    }

                    Log::info('Ingredient stock check', ['ingredient_id' => $ingId, 'stock' => (string)$ingredient->stock_quantity, 'need' => $need]);

                    if (bccomp((string)$ingredient->stock_quantity, (string)$need, 3) < 0) {
                        $msg = "Stok tidak cukup untuk bahan {$ingredient->item_name} (id: {$ingId}). Dibutuhkan: {$need}, tersedia: {$ingredient->stock_quantity}";
                        Log::error($msg, ['ingredient_id' => $ingId, 'need' => $need, 'stock' => (string)$ingredient->stock_quantity]);
                        throw new \Exception($msg);
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

                // Deduct aggregated requirements from ingredients
                foreach ($requirements as $ingId => $need) {
                    $ingredient = $ingredients->get($ingId);
                    $stockBefore = (string)$ingredient->stock_quantity;
                    $newStock = bcsub($stockBefore, (string)$need, 3);
                    if (bccomp($newStock, '0', 3) < 0) {
                        $msg = "Stok tidak cukup saat pengurangan untuk bahan {$ingredient->item_name} (id: {$ingId}). Dibutuhkan: {$need}, tersedia: {$stockBefore}";
                        Log::error($msg, ['ingredient_id' => $ingId, 'need' => $need, 'stock_before' => $stockBefore]);
                        throw new \Exception($msg);
                    }

                    Log::info('Updating ingredient stock', [
                        'ingredient_id' => $ingId,
                        'stock_before' => $stockBefore,
                        'need' => $need,
                        'stock_after' => $newStock,
                        'meta' => $requirements_meta[$ingId] ?? null,
                    ]);

                    $ingredient->stock_quantity = $newStock;
                    $ingredient->save();

                    if (bccomp((string)$newStock, '0', 3) === 0) {
                        $affected = \App\Models\Menu::whereHas('recipes', function ($q) use ($ingredient) {
                            $q->where('ingredient_id', $ingredient->id);
                        })->update(['is_available' => false]);
                        Log::info('Marked menus unavailable due to zero stock', ['ingredient_id' => $ingId, 'affected_menus' => $affected]);
                    }
                }

                $order->total_price = $total;
                $order->status = 'paid';
                $order->save();

                return $order;
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('pos.receipt', $order->id)->with('success', 'Pembayaran berhasil dan stok diperbarui');
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
}
