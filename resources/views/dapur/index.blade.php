@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Dapur - Pesanan</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        @forelse($orders as $order)
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">Pesanan #{{ $order->id }} - Meja {{ $order->table_number ?? '-' }}</h5>
                                <small class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</small>
                            </div>
                            <div class="text-end">
                                @if($order->status === 'paid')
                                    <span class="badge bg-primary">PAID</span>
                                @elseif($order->status === 'processing')
                                    <span class="badge bg-warning text-dark">PROCESSING</span>
                                @elseif($order->status === 'completed')
                                    <span class="badge bg-success">COMPLETED</span>
                                @else
                                    <span class="badge bg-secondary">{{ strtoupper($order->status) }}</span>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <ul class="list-group list-group-flush mb-3">
                            @foreach($order->details as $detail)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $detail->menu->name ?? 'Unknown' }}</strong>
                                        <div class="text-muted small">Rp {{ number_format($detail->price,0,',','.') }}</div>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill">x{{ $detail->qty }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Total:</strong> Rp {{ number_format($order->total_price,0,',','.') }}
                            </div>
                            <div class="d-flex gap-2">
                                @if($order->status === 'paid')
                                    <form action="{{ route('dapur.process', $order) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-primary">Proses</button>
                                    </form>
                                @endif

                                @if($order->status === 'processing')
                                    <form action="{{ route('dapur.complete', $order) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Selesai</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center text-muted">Tidak ada pesanan untuk ditampilkan.</div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
