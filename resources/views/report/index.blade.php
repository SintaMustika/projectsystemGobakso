@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Laporan Penjualan</h3>
    <div>
      <a href="{{ route('admin.report.export', array_merge(request()->only(['start_date','end_date']))) }}" class="btn btn-success">Export Excel</a>
    </div>
  </div>

  <form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
      <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $start ?? '') }}" placeholder="Start date">
    </div>
    <div class="col-auto">
      <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $end ?? '') }}" placeholder="End date">
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Filter</button>
      <a href="{{ route('admin.report') }}" class="btn btn-outline-secondary ms-1">Reset</a>
    </div>
  </form>

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card card-soft p-3">
        <small class="text-muted">Total Penjualan</small>
        <div class="h4">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-soft p-3">
        <small class="text-muted">Jumlah Transaksi</small>
        <div class="h4">{{ $transactions }}</div>
      </div>
    </div>
  </div>

  <div class="card card-soft">
    <div class="card-body p-3">
      <div class="table-responsive shadow-sm rounded">
        <table class="table table-striped table-hover mb-0 align-middle">
          <thead class="table-light small">
            <tr>
              <th style="width:60px">No</th>
              <th>Nomor Meja</th>
              <th class="text-end">Total Harga</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th style="width:130px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $idx => $o)
              <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $o->table_number ?? '-' }}</td>
                <td class="text-end">Rp {{ number_format($o->total_price, 0, ',', '.') }}</td>
                <td>
                  @if($o->status === 'paid')
                    <span class="badge badge-status-paid">Paid</span>
                  @elseif($o->status === 'pending')
                    <span class="badge badge-status-pending">Pending</span>
                  @elseif($o->status === 'cancelled')
                    <span class="badge badge-status-cancelled">Cancelled</span>
                  @else
                    <span class="badge bg-secondary">{{ ucfirst($o->status) }}</span>
                  @endif
                </td>
                <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
                <td>
                  <div class="d-flex gap-2 justify-content-end">
                    @if(Route::has('pos.show'))
                      <a href="{{ route('pos.show', $o->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>
                    @else
                      <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>
                    @endif

                    @if(Route::has('pos.destroy'))
                      <form method="POST" action="{{ route('pos.destroy', $o->id) }}" onsubmit="return confirm('Hapus transaksi ini?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                      </form>
                    @else
                      <button class="btn btn-sm btn-danger" disabled><i class="bi bi-trash"></i> Hapus</button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">Tidak ada data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

@endsection
