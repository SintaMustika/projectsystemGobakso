@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Riwayat Produksi</h3>
    <a href="{{ route('admin.productions.create') }}" class="btn btn-primary"><i class="bi bi-gear me-1"></i> Produksi Baru</a>
  </div>

  <div class="card card-soft">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table class="table">
          <thead class="small text-muted"><tr><th>Menu</th><th>Qty</th><th>Tanggal</th></tr></thead>
          <tbody>
            @foreach($productions as $p)
              <tr>
                <td>{{ $p->menu->name ?? '-' }}</td>
                <td>{{ $p->qty }}</td>
                <td>{{ $p->production_date->format('Y-m-d') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $productions->links() }}</div>
    </div>
  </div>

@endsection
