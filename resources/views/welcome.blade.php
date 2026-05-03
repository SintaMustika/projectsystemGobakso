<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bakso Cahaya Mandiri — Sistem Kasir Modern</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --primary:#0d6efd;
      --navy:#072033;
      --bg-gradient: linear-gradient(135deg, #061428 0%, #072033 100%);
      --soft-shadow: 0 10px 30px rgba(2,6,23,0.12);
    }
    html{scroll-behavior:smooth}
    body{font-family:'Poppins',sans-serif;background:#f8fafc;color:#0f1724}
    .hero{background:var(--bg-gradient);color:white;padding:72px 0}
    .hero .lead{opacity:.95}

    /* Buttons */
    .btn-primary{background:var(--primary);border-color:var(--primary);transition:transform .22s cubic-bezier(.2,.9,.2,1),box-shadow .22s ease}
    .btn-glow{position:relative}
    .btn-glow:hover{transform:translateY(-4px);box-shadow:0 12px 40px rgba(13,110,253,0.22)}

    /* Card features */
    .card.feature{border-radius:18px;box-shadow:0 8px 24px rgba(2,6,23,0.06);transition:transform .25s cubic-bezier(.2,.9,.2,1),box-shadow .25s ease,opacity .6s ease;transform:translateY(0);opacity:1}
    .card.feature:hover{transform:translateY(-10px);box-shadow:0 20px 50px rgba(2,6,23,0.12)}

    /* Large radii */
    .rounded-xl{border-radius:18px}
    .section{padding:64px 0}
    .muted{color:rgba(15,23,36,0.6)}
    .cta{background:linear-gradient(90deg,var(--primary),#0a58ca);color:white;padding:48px 0;border-radius:16px}
    footer{padding:28px 0;color:rgba(15,23,36,0.6)}

    /* Fade / scroll animations */
    .fade-in, .fade-up {opacity:0; transform:translateY(18px); transition:opacity .6s ease, transform .6s cubic-bezier(.2,.9,.2,1)}
    .fade-in.visible, .fade-up.visible {opacity:1; transform:translateY(0)}
    .hero-title{transform:translateY(10px);opacity:0;transition:opacity .8s ease, transform .8s cubic-bezier(.2,.9,.2,1)}
    .hero-title.visible{opacity:1;transform:translateY(0)}

    @media (max-width:767px){.hero{padding:48px 0}.section{padding:36px 0}}
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="6" fill="#0d6efd"/><path d="M7 12h10M7 8h6M7 16h10" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      <span class="fw-bold">Bakso Cahaya Mandiri</span>
    </a>

    <div class="ms-auto">
      @guest
        <a href="{{ route('login') }}" class="btn btn-outline-primary">Login</a>
      @else
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
      @endguest
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6 text-white">
        <h1 class="display-5 fw-bold hero-title">Sistem Kasir Modern untuk Bisnis Anda</h1>
        <p class="lead mt-3 fade-in">Kelola penjualan, stok, dan laporan dengan mudah dalam satu sistem</p>

        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg fade-up">Login</a>
          <a href="{{ route('login') }}" class="btn btn-primary btn-lg btn-glow fade-up">Mulai Sekarang</a>
        </div>
      </div>

      <div class="col-md-6 text-center mt-4 mt-md-0">
        <img src="https://via.placeholder.com/640x400.png?text=Dashboard+Preview" alt="Preview" class="img-fluid rounded-xl shadow" />
      </div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="section bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Fitur Utama</h2>
      <p class="muted">Semua kebutuhan operasional kasir dalam satu aplikasi</p>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="card feature p-4 h-100 rounded-xl fade-up">
          <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-primary bg-opacity-10 rounded-3"><i class="bi bi-lightning-fill text-primary fs-4"></i></div>
            <div>
              <h5 class="mb-1">POS Cepat</h5>
              <p class="muted mb-0 small">Transaksi cepat dan responsif</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card feature p-4 h-100 rounded-xl fade-up">
          <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-primary bg-opacity-10 rounded-3"><i class="bi bi-box-seam text-primary fs-4"></i></div>
            <div>
              <h5 class="mb-1">Manajemen Stok</h5>
              <p class="muted mb-0 small">Pantau bahan baku dan notifikasi stok rendah</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card feature p-4 h-100 rounded-xl fade-up">
          <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-primary bg-opacity-10 rounded-3"><i class="bi bi-graph-up text-primary fs-4"></i></div>
            <div>
              <h5 class="mb-1">Laporan Penjualan</h5>
              <p class="muted mb-0 small">Lihat ringkasan penjualan harian dan mingguan</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card feature p-4 h-100 rounded-xl fade-up">
          <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-primary bg-opacity-10 rounded-3"><i class="bi bi-printer-fill text-primary fs-4"></i></div>
            <div>
              <h5 class="mb-1">Cetak Struk</h5>
              <p class="muted mb-0 small">Integrasi printer untuk struk fisik</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section class="section bg-light">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h3 class="fw-bold">Cocok untuk UMKM, Cafe, dan Restoran</h3>
        <p class="muted">Sistem dirancang untuk memudahkan pemilik usaha mengelola transaksi, persediaan, dan laporan tanpa memerlukan banyak waktu atau pelatihan.</p>
      </div>
      <div class="col-md-6 text-center mt-4 mt-md-0">
        <img src="https://via.placeholder.com/520x320.png?text=Mockup" class="img-fluid rounded-xl shadow" alt="Mockup" />
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="cta d-flex align-items-center justify-content-between p-4 px-5">
          <div>
            <h4 class="fw-bold mb-1">Mulai Gunakan Sekarang</h4>
            <p class="mb-0 muted">Daftar dan jalankan kasir Anda hanya dalam beberapa menit.</p>
          </div>
          <div>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg">Login</a>
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg ms-2">Mulai Sekarang</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="bg-white">
  <div class="container text-center">
    <div class="py-3">
      <strong>Bakso Cahaya Mandiri</strong> &nbsp; • &nbsp; <small class="muted">© {{ date('Y') }} Bakso Cahaya Mandiri. All rights reserved.</small>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Hero fade-in on load
  document.addEventListener('DOMContentLoaded', function () {
    const heroTitle = document.querySelector('.hero-title');
    const heroLead = document.querySelector('.hero .lead');
    setTimeout(() => {
      if (heroTitle) heroTitle.classList.add('visible');
      if (heroLead) heroLead.classList.add('visible');
    }, 120);

    // IntersectionObserver for fade-up elements
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          io.unobserve(entry.target);
        }
      });
    }, {threshold: 0.12});

    document.querySelectorAll('.fade-up').forEach(el => io.observe(el));
  });
</script>

</body>
</html>
