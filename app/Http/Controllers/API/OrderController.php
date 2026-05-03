<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // only admins/owners can view all orders
        $user = $request->user();
        if (! $user || ! in_array($user->role ?? '', ['admin', 'owner'])) {
            abort(403);
        }
        return Order::with('details.menu')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'table_number' => 'nullable|integer',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $total = 0;
        $details = [];

        foreach ($data['items'] as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $linePrice = $menu->price * $item['qty'];
            $total += $linePrice;
            $details[] = new OrderDetail([
                'menu_id' => $menu->id,
                'qty' => $item['qty'],
                'price' => $menu->price,
            ]);
        }

        $order = DB::transaction(function () use ($data, $total, $details) {
            $order = Order::create([
                'table_number' => $data['table_number'] ?? null,
                'total_price' => $total,
                'status' => 'pending',
            ]);
            foreach ($details as $det) {
                $det->order_id = $order->id;
                $det->save();
            }
            return $order->load('details.menu');
        });

        return response()->json($order, 201);
    }

    public function show(Request $request, Order $order)
    {
        // allow owner/admin/cashier to view
        return $order->load('details.menu');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,paid',
        ]);

        if ($order->status === 'paid' && $data['status'] === 'paid') {
            return response()->json(['message' => 'Order already paid'], 400);
        }

        if ($data['status'] === 'paid') {
            try {
                $order->pay();
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        } else {
            $order->status = $data['status'];
            $order->save();
        }

        return response()->json($order->load('details.menu'));
    }
}
