@extends('layouts.app')

@section('content')
<style>
  html, body { height: 100%; }
  .pos-root { min-height: 100vh; display:flex; gap:20px; padding:20px; box-sizing:border-box; }

  .menu-area { flex: 0 0 70%; }
  .cart-area { flex: 0 0 30%; }

  /* Search */
  .search-bar { display:flex; gap:10px; margin-bottom:18px; }
  .search-input { flex:1; border-radius:30px; padding:12px 16px; border:1px solid #e6e9ef; box-shadow:0 4px 14px rgba(13,110,253,0.04); }
  .search-input:focus { outline:none; box-shadow:0 6px 20px rgba(13,110,253,0.12); }

  /* Grid */
  .menu-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:18px; }
  .menu-card { background:#fff; border-radius:14px; padding:14px; box-shadow:0 8px 26px rgba(13,110,253,0.04); cursor:pointer; transition:transform .18s ease, box-shadow .18s ease; display:flex; flex-direction:column; height:100%; }
  .menu-card:hover { transform:translateY(-8px) scale(1.02); box-shadow:0 18px 40px rgba(13,110,253,0.08); }
  .menu-card .thumb { height:140px; border-radius:10px; overflow:hidden; margin-bottom:10px; }
  .menu-card .thumb img{ width:100%; height:100%; object-fit:cover; display:block }
  .menu-card h5 { margin:0 0 6px 0; font-size:16px; }
  .menu-card .price { font-weight:700; color:#0d6efd; font-size:18px; }
  .menu-card .desc { color:#6b7280; font-size:13px; margin-top:auto }

  /* Cart */
  .cart-panel { position:sticky; top:20px; background:#ffffff; border-radius:14px; padding:18px; box-shadow:0 12px 30px rgba(2,6,23,0.06); }
  .cart-panel h5 { margin-bottom:10px }
  .cart-list { max-height:60vh; overflow:auto; margin-bottom:12px }
  .cart-item { display:flex; justify-content:space-between; align-items:center; gap:10px; padding:8px 0; border-bottom:1px dashed #f1f3f5 }
  .cart-item .meta { font-size:13px }
  .qty-controls { display:flex; gap:6px; align-items:center }
  .qty-controls button{ width:34px; height:34px; border-radius:8px }
  .cart-empty { text-align:center; color:#9ca3af; padding:30px 10px }

  .cart-total { display:flex; justify-content:space-between; align-items:center; margin-top:10px; padding-top:10px; border-top:1px solid #f1f3f5 }
  .cart-total .amount { font-size:20px; font-weight:800 }

  /* Pay button */
  .pay-btn { width:100%; padding:14px; border-radius:12px; color:#fff; font-weight:700; border:none; cursor:pointer; background:linear-gradient(90deg,#2563eb,#0ea5e9); box-shadow:0 8px 24px rgba(14,165,233,0.18); transition:box-shadow .18s ease, transform .12s ease }
  .pay-btn:hover { box-shadow:0 12px 36px rgba(14,165,233,0.24); transform:translateY(-2px) }

  /* Responsive */
  @media(max-width:991px){
    .pos-root { flex-direction:column; padding:12px }
    .menu-area, .cart-area { flex:1 1 100% }
    .menu-grid { grid-template-columns:repeat(2,1fr) }
  }
  @media(max-width:560px){
    .menu-grid { grid-template-columns:1fr }
  }
</style>

<div class="pos-root">

  <div class="menu-area">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="mb-0">Daftar Menu</h4>
        <small class="text-muted">Klik menu untuk menambahkan ke keranjang</small>
      </div>
      <div style="width:320px">
        <div class="search-bar">
          <div class="input-group">
            <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
            <input id="menuSearch" type="search" class="form-control search-input" placeholder="Cari menu...">
          </div>
        </div>
      </div>
    </div>

    <div class="menu-grid">
      @foreach($menus as $menu)
        <div>
          <div class="menu-card" onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})">
            <div class="thumb">
              @if(!empty($menu->image))
                <img src="{{ asset('storage/menus/' . $menu->image) }}" alt="{{ $menu->name }}">
              @else
                <img src="https://via.placeholder.com/400x200?text=No+Image" alt="no-image">
              @endif
            </div>
            <h5>{{ $menu->name }}</h5>
            <div class="price">Rp {{ number_format($menu->price,0,',','.') }}</div>
            <div class="desc">{{ $menu->description ?? '-' }}</div>
          </div>
        </div>
      @endforeach
    </div>

    <div id="noResults" class="text-center text-muted mt-3" style="display:none">Tidak ada menu ditemukan</div>
  </div>

  <div class="cart-area">
    <div class="cart-panel">
      <h5>Keranjang</h5>

      <div id="cartList" class="cart-list">
        <div class="cart-empty" id="cartEmpty">
          <i class="bi bi-cart-x" style="font-size:36px;display:block;margin-bottom:8px;color:#cbd5e1"></i>
          <div>Keranjang kosong</div>
        </div>
      </div>

      <div class="cart-total">
        <div>Total</div>
        <div class="amount" id="cartTotal">Rp 0</div>
      </div>

      <form id="checkoutForm" method="POST" action="{{ route('pos.checkout') }}" style="margin-top:12px">
        @csrf
        <input type="hidden" name="cart" id="cartInput">
        <audio id="beepSound" src="{{ asset('sounds/beep.mp3') }}" preload="auto"></audio>

        <div class="mb-3">
          <label class="form-label">Nomor Meja</label>
          <input type="number" name="table_number" class="form-control" placeholder="Optional">
        </div>

        <button type="submit" class="pay-btn">
          <i class="bi bi-cash-stack me-2"></i> Bayar
        </button>
      </form>
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script>
let cart = [];

function formatRupiah(angka){
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function addToCart(id, name, price){
    let item = cart.find(i => i.menu_id === id);
    if(item){ item.qty++; } else { cart.push({menu_id: id, name, price, qty:1}); }
    renderCart();
}

function changeQty(menu_id, change){
    const idx = cart.findIndex(i => i.menu_id === menu_id);
    if (idx === -1) return;
    cart[idx].qty += change;
    if(cart[idx].qty <= 0){ cart.splice(idx,1); }
    renderCart();
}

function renderCart(){
    let list = document.getElementById('cartList');
    let total = 0;

    if(cart.length === 0){
        list.innerHTML = document.getElementById('cartEmpty').outerHTML;
        document.getElementById('cartTotal').innerText = formatRupiah(0);
        document.getElementById('cartInput').value = JSON.stringify([]);
        return;
    }

    let html = '';
    cart.forEach((item)=>{
        let subtotal = item.price * item.qty;
        total += subtotal;
        html += `
        <div class="cart-item">
          <div class="meta">
            <div><strong>${item.name}</strong></div>
            <div class="muted">${item.qty} x ${formatRupiah(item.price)}</div>
          </div>
          <div style="text-align:right">
            <div style="margin-bottom:8px">${formatRupiah(subtotal)}</div>
            <div class="qty-controls">
              <button type="button" class="btn btn-outline-secondary" onclick="changeQty(${item.menu_id}, -1)">-</button>
              <button type="button" class="btn btn-outline-secondary" onclick="changeQty(${item.menu_id}, 1)">+</button>
            </div>
          </div>
        </div>
        `;
    });

    list.innerHTML = html;
    document.getElementById('cartTotal').innerText = formatRupiah(total);
    document.getElementById('cartInput').value = JSON.stringify(cart);
}

document.getElementById('checkoutForm').addEventListener('submit', function(e){
    if(cart.length === 0){ e.preventDefault(); alert('Keranjang kosong!'); return; }

    const beep = document.getElementById('beepSound');
    if (beep) {
        e.preventDefault();
        try {
            beep.currentTime = 0;
            const p = beep.play();
            if (p && typeof p.then === 'function') {
                p.then(() => { e.target.submit(); }).catch(() => { e.target.submit(); });
            } else { e.target.submit(); }
        } catch (err) { e.target.submit(); }
    }
});

// Real-time search/filter for menu cards
const menuSearch = document.getElementById('menuSearch');
if(menuSearch){
    menuSearch.addEventListener('input', function(){
        const q = this.value.trim().toLowerCase();
        const cards = document.querySelectorAll('.menu-card');
        let visible = 0;
        cards.forEach(card => {
            const titleEl = card.querySelector('h5');
            const name = titleEl ? titleEl.textContent.trim().toLowerCase() : '';
            if(name.indexOf(q) !== -1){ card.style.display = ''; visible++; } else { card.style.display = 'none'; }
        });

        const noEl = document.getElementById('noResults');
        if(noEl) noEl.style.display = visible === 0 ? '' : 'none';
    });
}
</script>
@endpush