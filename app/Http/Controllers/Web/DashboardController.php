<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $totalSales = Order::whereDate('created_at', $today)
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->sum('total_price');

        // Count paid transactions for today
        $transactions = Order::whereDate('created_at', $today)
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->count();

        // Top 5 best-selling menus for today (by qty)
        $topMenus = OrderDetail::select('menu_id', DB::raw('SUM(qty) as total_qty'))
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereIn('orders.status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->whereDate('orders.created_at', $today)
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('menu')
            ->get();

        // Recent 5 orders (latest)
        $recentOrders = Order::with('details.menu')
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Prepare 7-day sales data (last 7 days including today)
        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i);
            $labels[] = $d->format('d M');
            $dateString = $d->toDateString();
            $total = Order::whereDate('created_at', $dateString)
                ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
                ->sum('total_price');
            $data[] = (float) $total;
        }

        // Calculate total profit for today
        $totalProfit = 0.0;
        $todayOrders = Order::whereDate('created_at', $today)
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->with('details.menu.recipes.ingredient')
            ->get();

        foreach ($todayOrders as $o) {
            foreach ($o->details as $d) {
                $menu = $d->menu;
                if (!$menu) continue;
                $unitProfit = $menu->profitPerUnit();
                if (is_null($unitProfit)) continue; // skip menus without recipe
                $totalProfit += $unitProfit * $d->qty;
            }
        }

        return view('dashboard', [
            'totalSales' => $totalSales,
            'transactions' => $transactions,
            'topMenus' => $topMenus,
            'recentOrders' => $recentOrders,
            'chartLabels' => $labels,
            'chartData' => $data,
            'totalProfit' => round($totalProfit, 2),
        ]);
    }
}
