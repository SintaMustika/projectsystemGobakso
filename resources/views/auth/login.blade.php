@extends('layouts.auth')

@section('content')
<style>
  :root{
    --bcm-orange:#ff4b1f;
    --bcm-orange-dark:#d93a12;
    --bcm-navy:#001526;
    --bcm-navy-soft:#08233a;
    --bcm-cream:#fff7ed;
    --bcm-text:#172033;
    --bcm-muted:#667085;
  }

  body{
    background:#fff7ed;
  }

  main.py-4{
    padding-top:0 !important;
    padding-bottom:0 !important;
  }

  .login-page{
    min-height:100vh;
    display:grid;
    grid-template-columns:1.05fr .95fr;
    background:#fffaf6;
    color:var(--bcm-text);
  }

  .login-brand-panel{
    position:relative;
    overflow:hidden;
    display:flex;
    align-items:center;
    padding:64px;
    color:#fff;
    background:
      radial-gradient(circle at 20% 18%, rgba(255,75,31,.32), transparent 30%),
      linear-gradient(135deg,var(--bcm-navy) 0%,#061d32 52%,#0c2c45 100%);
  }

  .login-brand-panel::after{
    content:"";
    position:absolute;
    right:-120px;
    bottom:-150px;
    width:360px;
    height:360px;
    border-radius:50%;
    border:70px solid rgba(255,255,255,.05);
  }

  .brand-content{
    position:relative;
    z-index:1;
    max-width:620px;
  }

  .brand-badge{
    display:inline-flex;
    align-items:center;
    gap:.65rem;
    padding:.55rem .85rem;
    border-radius:999px;
    border:1px solid rgba(255,255,255,.16);
    background:rgba(255,255,255,.08);
    color:#ffd4c5;
    font-weight:700;
    margin-bottom:1.35rem;
  }

  .brand-title{
    font-size:clamp(2.3rem,4.5vw,4.6rem);
    line-height:1.02;
    font-weight:800;
    letter-spacing:0;
    margin-bottom:1.25rem;
  }

  .brand-copy{
    max-width:560px;
    color:rgba(255,255,255,.78);
    font-size:1.08rem;
    line-height:1.85;
    margin-bottom:2rem;
  }

  .feature-list{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
    max-width:520px;
  }

  .feature-item{
    display:flex;
    align-items:center;
    gap:.65rem;
    border:1px solid rgba(255,255,255,.13);
    border-radius:16px;
    background:rgba(255,255,255,.07);
    padding:14px 16px;
    font-weight:700;
  }

  .feature-item i{
    color:#ffb199;
  }

  .login-form-panel{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:48px 24px;
  }

  .login-card{
    width:100%;
    max-width:456px;
    border:1px solid rgba(15,23,42,.07);
    border-radius:28px;
    background:#fff;
    box-shadow:0 28px 70px rgba(18,32,52,.13);
    padding:34px;
  }

  .login-icon{
    width:74px;
    height:74px;
    margin:0 auto 18px;
    border-radius:22px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    background:linear-gradient(135deg,var(--bcm-orange),#ff8a3d);
    box-shadow:0 18px 34px rgba(255,75,31,.28);
    font-size:2.1rem;
  }

  .login-card h1{
    color:var(--bcm-navy);
    font-size:1.75rem;
    line-height:1.2;
    font-weight:800;
    text-align:center;
    margin-bottom:.45rem;
  }

  .login-subtitle{
    color:var(--bcm-muted);
    text-align:center;
    font-weight:600;
    margin-bottom:1.7rem;
  }

  .form-label{
    color:var(--bcm-navy);
    font-weight:700;
    margin-bottom:.5rem;
  }

  .form-control{
    min-height:52px;
    border-radius:14px;
    border-color:#e6e9ef;
    padding:.8rem 1rem;
  }

  .form-control:focus{
    border-color:var(--bcm-orange);
    box-shadow:0 0 0 .22rem rgba(255,75,31,.14);
  }

  .form-check-input:checked{
    background-color:var(--bcm-orange);
    border-color:var(--bcm-orange);
  }

  .btn-login{
    min-height:52px;
    border:none;
    border-radius:15px;
    background:var(--bcm-orange);
    color:#fff;
    font-weight:800;
    box-shadow:0 16px 34px rgba(255,75,31,.26);
    transition:transform .2s ease, box-shadow .2s ease, background-color .2s ease;
  }

  .btn-login:hover,
  .btn-login:focus{
    color:#fff;
    background:var(--bcm-orange-dark);
    transform:translateY(-2px);
    box-shadow:0 22px 42px rgba(255,75,31,.34);
  }

  .login-footer{
    color:var(--bcm-muted);
    text-align:center;
    margin-top:1.35rem;
    font-size:.88rem;
  }

  .alert{
    border-radius:14px;
  }

  @media (max-width:991.98px){
    .login-page{
      grid-template-columns:1fr;
    }

    .login-brand-panel{
      min-height:auto;
      padding:46px 24px;
    }

    .brand-title{
      font-size:2.6rem;
    }

    .login-form-panel{
      min-height:auto;
      padding:34px 18px 46px;
    }
  }

  @media (max-width:575.98px){
    .feature-list{
      grid-template-columns:1fr;
    }

    .login-card{
      border-radius:22px;
      padding:26px 20px;
    }

    .brand-title{
      font-size:2.25rem;
    }
  }
</style>

<div class="login-page">
  <section class="login-brand-panel">
    <div class="brand-content">
      <div class="brand-badge">
        <i class="bi bi-cup-hot-fill"></i>
        Bakso Cahaya Mandiri
      </div>

      <h2 class="brand-title">Sistem Pemesanan &amp; Kasir Digital</h2>
      <p class="brand-copy">
        Sistem digital untuk mengelola pesanan, dapur, kasir, stok, dan laporan penjualan.
      </p>

      <div class="feature-list">
        <div class="feature-item"><i class="bi bi-check-circle-fill"></i> QR Order</div>
        <div class="feature-item"><i class="bi bi-check-circle-fill"></i> Dapur Realtime</div>
        <div class="feature-item"><i class="bi bi-check-circle-fill"></i> Manajemen Stok</div>
        <div class="feature-item"><i class="bi bi-check-circle-fill"></i> Laporan Penjualan</div>
      </div>
    </div>
  </section>

  <section class="login-form-panel">
    <div class="login-card">
      <div class="login-icon">
        <i class="bi bi-basket3-fill"></i>
      </div>

      <h1>Masuk Sistem</h1>
      <div class="login-subtitle">Admin • Kasir • Dapur</div>

      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus>
          @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input name="password" type="password" class="form-control" required>
          @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small" for="remember">Remember me</label>
          </div>
        </div>

        <div class="d-grid">
          <button class="btn btn-login" type="submit">Masuk Dashboard</button>
        </div>
      </form>

      <div class="login-footer">© Bakso Cahaya Mandiri 2026</div>
    </div>
  </section>
</div>
@endsection
