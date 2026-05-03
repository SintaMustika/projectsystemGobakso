@extends('layouts.app')

@section('content')
  <h3>Order #{{ $order->id }}</h3>
  <div class="mb-3">Status: <strong>{{ $order->status }}</strong></div>
  <table class="table">
    <thead><tr><th>Menu</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
    <tbody>
      @foreach($order->details as $d)
        <tr>
          <td>{{ $d->menu->name }}</td>
          <td>{{ $d->qty }}</td>
          <td>{{ number_format($d->price,2) }}</td>
          <td>{{ number_format($d->price * $d->qty,2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mb-3">Total: Rp {{ number_format($order->total_price,2) }}</div>

  @if($order->status !== 'paid')
    <form method="POST" action="{{ route('pos.pay', $order->id) }}">
      @csrf
      <button class="btn btn-success">Pay</button>
    </form>
  @endif

@endsection
