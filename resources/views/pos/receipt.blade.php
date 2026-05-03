<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Receipt - Bakso Cahaya Mandiri</title>
  <style>
    :root{--width:250px}
    body{font-family: 'Courier New', Courier, monospace;margin:0;padding:8px;background:#fff}
    .receipt{width:var(--width);max-width:100%;margin:0 auto;color:#000}
    .center{text-align:center}
    h1.store{font-size:16px;margin:0;font-weight:700}
    .meta{font-size:11px;color:#333;margin-top:6px}
    .sep{border-top:1px dashed #333;margin:8px 0}
    .items{width:100%;font-size:12px}
    .items .row{display:flex;justify-content:space-between;align-items:flex-start;padding:3px 0}
    .items .name{flex:1;margin-right:8px}
    .items .price{text-align:right;min-width:70px}
    .muted{color:#666;font-size:11px}
    .total{display:flex;justify-content:space-between;font-weight:700;padding-top:6px;border-top:1px dashed #333;margin-top:6px}
    .thanks{margin-top:10px;text-align:center;font-size:12px}
    .no-print{margin-top:10px;text-align:center}
    .badge-unit{background:#eee;border-radius:3px;padding:2px 6px;font-size:10px;margin-left:6px}
    @media print{body{margin:0;padding:6px} .no-print{display:none}}
  </style>
</head>
<body>
  <div class="receipt">
    <div class="center">
      <div class="store">Bakso Cahaya Mandiri</div>
      <div class="meta">{{ now()->format('d-m-Y H:i') }}</div>
      <div class="meta">No Meja: {{ $order->table_number ?? '-' }}</div>
    </div>

    <div class="sep"></div>

    <div class="items">
      @foreach($order->details as $d)
        <div class="row">
          <div class="name">
            <div>
              {{ Str::limit(optional($d->menu)->name ?? '—', 30) }}
              @if(optional($d->menu)->unit)
                <span class="badge-unit">{{ optional($d->menu)->unit }}</span>
              @endif
            </div>
            <div class="muted">x{{ $d->qty }}</div>
          </div>
          <div class="price">Rp {{ number_format($d->price * $d->qty,2,',','.') }}</div>
        </div>
      @endforeach
    </div>

    <div class="total">
      <div>Total</div>
      <div>Rp {{ number_format($order->total_price,2,',','.') }}</div>
    </div>

    <div class="thanks">Terima Kasih</div>

    <div class="no-print">
      <button onclick="window.print()">Print</button>
    </div>
  </div>

  <script>
    // Auto print and then redirect to /pos
    function redirectToPos(){
      try{ window.location.href = '{{ route('pos.index') }}'; } catch(e){}
    }

    window.addEventListener('load', function(){
      setTimeout(function(){ window.print(); }, 300);
    });

    if ('onafterprint' in window){
      window.onafterprint = redirectToPos;
    } else if (window.matchMedia){
      try{
        var mql = window.matchMedia('print');
        if (mql.addEventListener) mql.addEventListener('change', function(e){ if(!e.matches) redirectToPos(); });
        else if (mql.addListener) mql.addListener(function(m){ if(!m.matches) redirectToPos(); });
      }catch(e){}
      setTimeout(redirectToPos, 8000);
    } else {
      setTimeout(redirectToPos, 8000);
    }
  </script>
</body>
</html>
