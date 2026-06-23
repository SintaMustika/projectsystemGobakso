<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;

class KitchenController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:dapur']);
    }

    public function index()
    {
        $orders = Order::with('items.menu')
            ->whereIn('status', [
                Order::STATUS_PENDING,
                Order::STATUS_WAITING,
                Order::STATUS_PROCESSING,
            ])
            ->latest()
            ->get();

        return view('dapur.index', compact('orders'));
    }

    public function process(Order $order)
    {
        if ($order->isCompleted()) {
            return redirect()->back()->with('error', 'Order sudah selesai, tidak dapat diproses');
        }

        if (! in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_WAITING], true)) {
            return redirect()->back()->with('error', 'Order tidak dapat diproses');
        }

        $order->status = Order::STATUS_PROCESSING;
        $order->save();

        return redirect()->back()->with('success', 'Order sedang diproses');
    }

    public function complete(Order $order)
    {
        if ($order->isCompleted()) {
            return redirect()->back()->with('error', 'Order sudah selesai');
        }

        if ($order->status !== Order::STATUS_PROCESSING) {
            return redirect()->back()->with('error', 'Order tidak dapat diselesaikan');
        }

        $order->status = Order::STATUS_COMPLETED;
        $order->save();

        return redirect()->back()->with('success', 'Order selesai');
    }
}
