<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bakso Cahaya Mandiri - Sistem Pemesanan Bakso Digital</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --orange:#ff4b1f;
      --orange-dark:#d83612;
      --navy:#071627;
      --navy-soft:#0d2238;
      --cream:#fff7ed;
      --ink:#132033;
      --muted:#667085;
      --line:rgba(255,255,255,.14);
      --shadow:0 24px 70px rgba(5,15,30,.18);
    }

    *{box-sizing:border-box}
    html{scroll-behavior:smooth}
    body{
      margin:0;
      font-family:'Poppins',sans-serif;
      background:#fffaf6;
      color:var(--ink);
    }

    .navbar{
      background:rgba(7,22,39,.94);
      backdrop-filter:blur(16px);
      border-bottom:1px solid rgba(255,255,255,.08);
    }

    .brand-mark{
      width:42px;
      height:42px;
      border-radius:12px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      color:#fff;
      background:linear-gradient(135deg,var(--orange),#ff8a3d);
      box-shadow:0 12px 28px rgba(255,75,31,.28);
      font-size:1.25rem;
    }

    .hero{
      position:relative;
      overflow:hidden;
      color:#fff;
      background:
        radial-gradient(circle at 16% 22%, rgba(255,75,31,.32), transparent 30%),
        linear-gradient(135deg,#071627 0%,#0b1c30 48%,#132c43 100%);
      padding:108px 0 86px;
    }

    .hero::after{
      content:"";
      position:absolute;
      inset:auto 0 0 0;
      height:96px;
      background:linear-gradient(180deg,rgba(255,250,246,0),#fffaf6);
      pointer-events:none;
    }

    .hero .container{position:relative;z-index:1}
    .eyebrow{
      display:inline-flex;
      align-items:center;
      gap:.5rem;
      padding:.45rem .75rem;
      border:1px solid rgba(255,255,255,.16);
      border-radius:999px;
      background:rgba(255,255,255,.08);
      color:#ffd8ca;
      font-size:.85rem;
      font-weight:600;
      margin-bottom:1.25rem;
    }

    .hero-title{
      max-width:760px;
      font-weight:800;
      letter-spacing:0;
      line-height:1.04;
      margin:0;
    }

    .hero-subtitle{
      max-width:650px;
      color:rgba(255,255,255,.78);
      line-height:1.8;
      margin-top:1.25rem;
    }

    .btn-orange{
      --bs-btn-color:#fff;
      --bs-btn-bg:var(--orange);
      --bs-btn-border-color:var(--orange);
      --bs-btn-hover-color:#fff;
      --bs-btn-hover-bg:var(--orange-dark);
      --bs-btn-hover-border-color:var(--orange-dark);
      --bs-btn-active-bg:var(--orange-dark);
      --bs-btn-active-border-color:var(--orange-dark);
      box-shadow:0 16px 30px rgba(255,75,31,.26);
    }

    .btn-hero{
      min-height:52px;
      padding:.8rem 1.25rem;
      border-radius:14px;
      font-weight:700;
    }

    .btn-outline-hero{
      color:#fff;
      border-color:rgba(255,255,255,.36);
      background:rgba(255,255,255,.06);
    }

    .btn-outline-hero:hover{
      color:#fff;
      border-color:rgba(255,255,255,.62);
      background:rgba(255,255,255,.14);
    }

    .hero-stats{
      display:grid;
      grid-template-columns:repeat(3,minmax(0,1fr));
      gap:12px;
      max-width:600px;
      margin-top:2rem;
    }

    .stat-pill{
      border:1px solid rgba(255,255,255,.13);
      border-radius:14px;
      background:rgba(255,255,255,.07);
      padding:14px;
    }

    .stat-pill strong{
      display:block;
      color:#fff;
      font-size:1rem;
    }

    .stat-pill span{
      color:rgba(255,255,255,.62);
      font-size:.82rem;
    }

    .mockup-wrap{
      position:relative;
      max-width:470px;
      margin-left:auto;
    }

    .mockup-panel{
      border:1px solid var(--line);
      border-radius:28px;
      background:linear-gradient(180deg,rgba(255,255,255,.14),rgba(255,255,255,.07));
      box-shadow:var(--shadow);
      padding:22px;
    }

    .mockup-top{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      padding-bottom:18px;
      border-bottom:1px solid var(--line);
      margin-bottom:18px;
    }

    .qr-chip{
      display:inline-flex;
      align-items:center;
      gap:.45rem;
      border-radius:999px;
      background:rgba(255,75,31,.16);
      color:#ffd0c2;
      padding:.45rem .7rem;
      font-size:.82rem;
      font-weight:700;
    }

    .screen-card{
      border-radius:20px;
      background:#fff;
      color:var(--ink);
      padding:18px;
      box-shadow:0 18px 42px rgba(0,0,0,.16);
    }

    .menu-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
    }

    .menu-icon{
      width:58px;
      height:58px;
      flex:0 0 58px;
      border-radius:18px;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#fff1e8;
      font-size:1.85rem;
    }

    .status-badge{
      display:inline-flex;
      align-items:center;
      gap:.35rem;
      border-radius:999px;
      padding:.35rem .65rem;
      background:#e8f8ef;
      color:#167044;
      font-size:.78rem;
      font-weight:700;
    }

    .order-card{
      margin-top:16px;
      border-radius:20px;
      padding:18px;
      background:linear-gradient(135deg,#fff7ed,#fff);
      border:1px solid #ffe0d1;
      color:var(--ink);
    }

    .table-code{
      display:inline-flex;
      align-items:center;
      border-radius:12px;
      background:var(--navy);
      color:#fff;
      padding:.5rem .75rem;
      font-weight:800;
      letter-spacing:.04em;
    }

    .section{
      padding:76px 0;
    }

    .section-title{
      font-weight:800;
      color:var(--navy);
      margin-bottom:.75rem;
    }

    .section-subtitle{
      color:var(--muted);
      max-width:640px;
      margin:0 auto;
      line-height:1.7;
    }

    .feature-card{
      height:100%;
      border:1px solid #f1dfd4;
      border-radius:20px;
      background:#fff;
      padding:26px;
      box-shadow:0 16px 38px rgba(38,23,13,.06);
    }

    .feature-icon{
      width:54px;
      height:54px;
      border-radius:16px;
      display:flex;
      align-items:center;
      justify-content:center;
      background:linear-gradient(135deg,var(--orange),#ff8a3d);
      color:#fff;
      font-size:1.35rem;
      margin-bottom:1.25rem;
    }

    .feature-card h5{
      color:var(--navy);
      font-weight:800;
      margin-bottom:.6rem;
    }

    .feature-card p{
      color:var(--muted);
      line-height:1.7;
      margin:0;
    }

    .flow-section{
      background:var(--navy);
      color:#fff;
      position:relative;
      overflow:hidden;
    }

    .flow-section::before{
      content:"";
      position:absolute;
      inset:0;
      background:radial-gradient(circle at 82% 18%,rgba(255,75,31,.28),transparent 30%);
      pointer-events:none;
    }

    .flow-section .container{position:relative;z-index:1}

    .flow-line{
      display:grid;
      grid-template-columns:repeat(5,minmax(0,1fr));
      gap:14px;
      align-items:center;
      margin-top:34px;
    }

    .flow-step{
      min-height:132px;
      border:1px solid rgba(255,255,255,.14);
      border-radius:20px;
      background:rgba(255,255,255,.07);
      padding:22px 16px;
      text-align:center;
    }

    .flow-step i{
      color:#ffb199;
      font-size:1.6rem;
      margin-bottom:.75rem;
    }

    .flow-step strong{
      display:block;
      font-size:1rem;
    }

    .flow-arrow{
      display:none;
      color:#ffb199;
      font-size:1.4rem;
      font-weight:800;
      text-align:center;
    }

    .footer{
      background:#fff;
      border-top:1px solid #f3dfd4;
      color:var(--muted);
      padding:26px 0;
    }

    @media (max-width:991.98px){
      .hero{
        padding:86px 0 70px;
      }

      .mockup-wrap{
        margin:42px auto 0;
      }

      .flow-line{
        grid-template-columns:1fr;
        max-width:420px;
        margin-left:auto;
        margin-right:auto;
      }

      .flow-arrow{
        display:block;
      }

      .flow-step{
        min-height:auto;
      }
    }

    @media (max-width:575.98px){
      .hero{
        padding:70px 0 58px;
      }

      .hero-title{
        font-size:2.35rem;
      }

      .hero-stats{
        grid-template-columns:1fr;
      }

      .btn-hero{
        width:100%;
      }

      .section{
        padding:56px 0;
      }

      .mockup-panel{
        border-radius:22px;
        padding:16px;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('home') }}">
      <span class="brand-mark"><i class="bi bi-cup-hot-fill"></i></span>
      <span>Bakso Cahaya Mandiri</span>
    </a>

    <div class="ms-auto">
      @guest
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light rounded-3 px-3">Login Admin</a>
      @else
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-orange rounded-3 px-3">Dashboard</a>
      @endguest
    </div>
  </div>
</nav>

<main>
  <section class="hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7">
          <div class="eyebrow">
            <i class="bi bi-qr-code-scan"></i>
            Sistem Pemesanan Bakso Digital
          </div>

          <h1 class="display-4 hero-title">Bakso Cahaya Mandiri</h1>
          <p class="hero-subtitle fs-5">
            Sistem pemesanan bakso digital berbasis QR Code yang menghubungkan pelanggan, kasir, dapur, dan admin secara realtime.
          </p>

          <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
            <a href="{{ route('login') }}" class="btn btn-outline-hero btn-hero">
              <i class="bi bi-person-lock me-2"></i>Login Admin
            </a>
            <a href="#alur" class="btn btn-orange btn-hero">
              <i class="bi bi-qr-code me-2"></i>Mulai Pesan
            </a>
          </div>

          <div class="hero-stats">
            <div class="stat-pill">
              <strong>QR Code</strong>
              <span>Pesan dari meja</span>
            </div>
            <div class="stat-pill">
              <strong>Realtime</strong>
              <span>Dapur terhubung</span>
            </div>
            <div class="stat-pill">
              <strong>Terintegrasi</strong>
              <span>Kasir dan admin</span>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="mockup-wrap">
            <div class="mockup-panel">
              <div class="mockup-top">
                <div>
                  <div class="fw-bold">Panel Pesanan</div>
                  <small class="text-white-50">Realtime order monitor</small>
                </div>
                <span class="qr-chip"><i class="bi bi-broadcast"></i> Live</span>
              </div>

              <div class="screen-card">
                <div class="menu-row">
                  <div class="d-flex align-items-center gap-3">
                    <div class="menu-icon">🍜</div>
                    <div>
                      <h5 class="fw-bold mb-1">Bakso Urat</h5>
                      <div class="text-secondary">Rp 12.000</div>
                    </div>
                  </div>
                  <span class="status-badge">
                    <i class="bi bi-check-circle-fill"></i>
                    Tersedia
                  </span>
                </div>
              </div>

              <div class="order-card">
                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                  <div>
                    <div class="fw-bold"><i class="bi bi-receipt-cutoff me-2 text-danger"></i>Pesanan Masuk</div>
                    <small class="text-secondary">Notifikasi ke kasir dan dapur</small>
                  </div>
                  <span class="table-code">MEJA-01</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-success fw-bold">
                  <i class="bi bi-fire"></i>
                  Diproses Dapur
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="fitur" class="section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title">Fitur Utama</h2>
        <p class="section-subtitle">
          Dirancang untuk membuat operasional Bakso Cahaya Mandiri lebih cepat, rapi, dan mudah dipantau.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-qr-code-scan"></i></div>
            <h5>Scan QR Meja</h5>
            <p>Customer scan QR tanpa antre.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-phone"></i></div>
            <h5>Pesan Digital</h5>
            <p>Customer memilih menu langsung dari HP.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-bell"></i></div>
            <h5>Dapur Realtime</h5>
            <p>Pesanan langsung masuk ke dapur.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-cash-coin"></i></div>
            <h5>Kasir Terintegrasi</h5>
            <p>Pembayaran dan laporan otomatis.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="alur" class="section flow-section">
    <div class="container">
      <div class="text-center">
        <h2 class="section-title text-white">Alur Pemesanan</h2>
        <p class="section-subtitle text-white-50">
          Proses pemesanan dibuat sederhana dari meja pelanggan sampai pembayaran di kasir.
        </p>
      </div>

      <div class="flow-line">
        <div class="flow-step">
          <i class="bi bi-qr-code-scan d-block"></i>
          <strong>Scan QR</strong>
        </div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step">
          <i class="bi bi-card-checklist d-block"></i>
          <strong>Pilih Menu</strong>
        </div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step">
          <i class="bi bi-cart-check d-block"></i>
          <strong>Checkout</strong>
        </div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step">
          <i class="bi bi-egg-fried d-block"></i>
          <strong>Dapur Proses</strong>
        </div>
        <div class="flow-arrow">↓</div>
        <div class="flow-step">
          <i class="bi bi-wallet2 d-block"></i>
          <strong>Pembayaran</strong>
        </div>
      </div>
    </div>
  </section>
</main>

<footer class="footer">
  <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2 text-center text-md-start">
    <strong class="text-dark">Bakso Cahaya Mandiri</strong>
    <small>© {{ date('Y') }} Sistem Pemesanan Bakso Digital</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
