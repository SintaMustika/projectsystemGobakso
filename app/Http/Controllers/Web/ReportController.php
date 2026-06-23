<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'export' => 'nullable|in:csv',
        ]);

        $start = $data['start_date'] ?? now()->toDateString();
        $end = $data['end_date'] ?? now()->toDateString();

        $query = Order::with('details.menu')
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->orderByDesc('created_at');

        $totalSales = (clone $query)->sum('total_price');
        $transactions = (clone $query)->count();

        // Calculate totals (sales, modal, profit) for the selected range using stored fields when available
        $totalProfit = 0.0;
        $totalModal = 0.0;
        $ordersForCalc = (clone $query)->with('details')->get();
        foreach ($ordersForCalc as $order) {
            foreach ($order->details as $d) {
                $qty = (int)$d->qty;
                if (isset($d->profit) && $d->profit != 0) {
                    $totalProfit += (float)$d->profit;
                } else {
                    // fallback to menu-based calc
                    $menu = $d->menu;
                    if ($menu) {
                        $unitProfit = $menu->profitPerUnit();
                        if (!is_null($unitProfit)) $totalProfit += $unitProfit * $qty;
                    }
                }

                if (isset($d->capital_price) && $d->capital_price != 0) {
                    $totalModal += (float)$d->capital_price * $qty;
                } else {
                    $menu = $d->menu;
                    if ($menu) {
                        $unitCost = $menu->materialCost();
                        if (!is_null($unitCost)) $totalModal += $unitCost * $qty;
                    }
                }
            }
        }

        // If export CSV requested
        if (($data['export'] ?? null) === 'csv') {
            $orders = $query->get();
            $filename = 'sales_report_'.$start.'_'.$end.'.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];

            $callback = function () use ($orders) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Order ID','Date','Table','Total Price','Modal','Profit','Status','Items']);
                foreach ($orders as $order) {
                    $modal = 0; $profit = 0;
                    foreach ($order->details as $d) {
                        $qty = (int)$d->qty;
                        $modal += (float)($d->capital_price ?? 0) * $qty;
                        $profit += (float)($d->profit ?? 0);
                    }
                    $items = $order->details->map(function ($d) {
                        return ($d->menu->name ?? 'Unknown').' x'.$d->qty;
                    })->implode(' | ');
                    fputcsv($out, [$order->id, $order->created_at->toDateTimeString(), $order->table_number, $order->total_price, $modal, $profit, $order->status, $items]);
                }
                fclose($out);
            };

            return Response::stream($callback, 200, $headers);
        }

        $orders = $query->paginate(25)->withQueryString();

        return view('reports.index', compact('orders', 'totalSales', 'transactions', 'start', 'end', 'totalProfit'));
    }

    /**
     * Simple report page for /report (web admin)
     * - lists all paid orders ordered by newest
     */
    public function simple(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        $query = Order::whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->with('details.menu')
            ->orderByDesc('created_at');

        // Apply date filters if provided
        if ($start && $end) {
            $query->whereBetween('created_at', [
                $start . ' 00:00:00',
                $end . ' 23:59:59',
            ]);
        } elseif ($start) {
            $query->whereDate('created_at', '>=', $start);
        } elseif ($end) {
            $query->whereDate('created_at', '<=', $end);
        }

        $orders = $query->get();

        $totalSales = $orders->sum('total_price');
        $transactions = $orders->count();

        return view('report.index', compact('orders', 'totalSales', 'transactions', 'start', 'end'));
    }

    public function export(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        $fileName = 'sales_report_' . now()->format('Ymd_His') . '.xlsx';

        // If maatwebsite/excel isn't installed, fall back to CSV stream
        if (! class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            $orders = Order::with('details.menu')
                ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
                ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
                ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
                ->orderByDesc('created_at')
                ->get();

            $filename = 'sales_report_' . ($start ?? now()->toDateString()) . '_' . ($end ?? now()->toDateString()) . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($orders) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Order ID','Date','Table','Total Price','Status','Items']);
                foreach ($orders as $order) {
                    $items = $order->details->map(function ($d) {
                        return ($d->menu->name ?? 'Unknown').' x'.$d->qty;
                    })->implode(' | ');
                    fputcsv($out, [$order->id, $order->created_at->toDateTimeString(), $order->table_number, $order->total_price, $order->status, $items]);
                }
                fclose($out);
            };

            return Response::stream($callback, 200, $headers);
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($start, $end), $fileName);
    }
}
