<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class KitchenController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:dapur']);
    }

    public function index()
    {
        $orders = Order::with('details.menu')
            ->whereIn('status', ['paid', 'processing'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('dapur.index', compact('orders'));
    }

    public function process(Order $order)
    {
        if ($order->status !== 'paid') {
            return redirect()->back()->with('error', 'Order tidak dapat diproses');
        }

        $order->status = 'processing';
        $order->save();

        return redirect()->back()->with('success', 'Order sedang diproses');
    }

    public function done(Order $order)
    {
        if ($order->status !== 'processing') {
            return redirect()->back()->with('error', 'Order tidak dapat diselesaikan');
        }

        $order->status = 'done';
        $order->save();

        return redirect()->back()->with('success', 'Order selesai');
    }
}
