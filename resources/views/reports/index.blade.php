@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Laporan Penjualan</h3>
    <div>
      <a href="{{ route('admin.reports.sales', array_merge(request()->except('page'), ['export' => 'csv'])) }}" class="btn btn-outline-secondary">Export CSV</a>
    </div>
  </div>

  <form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
      <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $start) }}">
    </div>
    <div class="col-auto">
      <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $end) }}">
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Filter</button>
    </div>
  </form>

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card card-soft p-3">
        <small class="text-muted">Total Penjualan</small>
        <div class="h4">Rp {{ number_format($totalSales,2,',','.') }}</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-soft p-3">
        <small class="text-muted">Jumlah Transaksi</small>
        <div class="h4">{{ $transactions }}</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-soft p-3">
        <small class="text-muted">Total Profit</small>
        <div class="h4">Rp {{ number_format($totalProfit ?? 0,2,',','.') }}</div>
      </div>
    </div>
  </div>

  <div class="card card-soft">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="text-muted small">
            <tr><th>ID</th><th>Date</th><th>Table</th><th>Items</th><th class="text-end">Total</th></tr>
          </thead>
          <tbody>
            @foreach($orders as $o)
              <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $o->table_number ?? '-' }}</td>
                <td>
                  @foreach($o->details as $d)
                    <div class="small">{{ $d->menu->name }} x{{ $d->qty }}</div>
                  @endforeach
                </td>
                <td class="text-end">Rp {{ number_format($o->total_price,2,',','.') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-3">{{ $orders->links() }}</div>
    </div>
  </div>

@endsection
