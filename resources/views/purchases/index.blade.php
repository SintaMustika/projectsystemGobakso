@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Pembelian Bahan</h3>
    <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary"><i class="bi bi-cart-plus me-1"></i> Tambah Pembelian</a>
  </div>

  <div class="card card-soft">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table class="table">
          <thead class="small text-muted">
            <tr><th>Invoice</th><th>Supplier</th><th>Tanggal</th><th>Total</th><th>Detail</th></tr>
          </thead>
          <tbody>
            @foreach($purchases as $p)
              <tr>
                <td>{{ $p->invoice }}</td>
                <td>{{ $p->supplier }}</td>
                <td>{{ $p->purchase_date->format('Y-m-d') }}</td>
                <td>Rp {{ number_format($p->total_price,0,',','.') }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#d{{ $p->id }}">Lihat</button>
                </td>
              </tr>
              <tr class="p-0">
                <td colspan="5" class="p-0">
                  <div class="collapse" id="d{{ $p->id }}">
                    <div class="p-3 bg-light">
                      <table class="table table-sm mb-0">
                        <thead><tr><th>Bahan</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
                        <tbody>
                        @foreach($p->details as $d)
                          <tr>
                            <td>{{ $d->ingredient->item_name ?? "-" }}</td>
                            <td>{{ rtrim(rtrim((string)$d->qty,'0'),'.') }} @if($d->ingredient && $d->ingredient->unit) {{ $d->ingredient->unit }} @endif</td>
                            <td>Rp {{ number_format($d->unit_price,0,',','.') }}</td>
                            <td>Rp {{ number_format($d->subtotal,0,',','.') }}</td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-3">{{ $purchases->links() }}</div>
    </div>
  </div>

@endsection
