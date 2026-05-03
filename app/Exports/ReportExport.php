<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        $query = Order::where('status', 'paid')->with('details.menu')->orderByDesc('created_at');

        if ($this->start && $this->end) {
            $query->whereBetween('created_at', [$this->start . ' 00:00:00', $this->end . ' 23:59:59']);
        } elseif ($this->start) {
            $query->whereDate('created_at', '>=', $this->start);
        } elseif ($this->end) {
            $query->whereDate('created_at', '<=', $this->end);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Meja',
            'Total Harga',
            'Status',
            'Tanggal',
        ];
    }

    public function map($order): array
    {
        return [
            $order->table_number ?? '-',
            $order->total_price,
            $order->status,
            $order->created_at->toDateTimeString(),
        ];
    }
}
