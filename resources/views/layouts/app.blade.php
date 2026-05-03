<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Kasir</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --sidebar-start:#081426;
      --sidebar-end:#072033;
      --navbar:#0b2133;
      --accent:#0d6efd;
      --bg:#f6f8fb;
      --text:#111827;
    }

    body{
      font-family:'Poppins',sans-serif;
      background:var(--bg);
      color:var(--text);
    }

    /* SIDEBAR */
    .sidebar{
      width:250px;
      height:100vh;
      background:linear-gradient(180deg,var(--sidebar-start),var(--sidebar-end));
      position:fixed;
      top:0;
      left:0;
      color:white;
      z-index:1040;
      transition:transform .25s ease;
    }

    .sidebar .list-group-item{
      background:transparent;
      border:none;
      color:rgba(255,255,255,0.8);
      border-radius:8px;
      margin-bottom:5px;
      transition:0.3s;
    }

    .sidebar .list-group-item:hover{
      background:rgba(255,255,255,0.1);
      color:white;
    }

    .sidebar .list-group-item.active{
      background:rgba(255,255,255,0.15);
      color:white;
      font-weight:600;
    }

    /* NAVBAR */
    .navbar{
      background:linear-gradient(90deg,var(--sidebar-start),var(--sidebar-end));
      box-shadow:none;
      margin-left:250px;
    }

    .navbar .container-fluid{
      padding-left:1rem;
      padding-right:1rem;
    }

    /* CONTENT */
    .content-area{
      margin-left:250px;
      padding:25px;
    }

    @media (max-width:768px){
      .sidebar{
        position:fixed;
        top:0;
        left:0;
        transform:translateX(-100%);
        width:250px;
        height:100vh;
      }
      .sidebar.show{
        transform:translateX(0);
      }
      .content-area{
        margin-left:0;
        padding-top:70px;
      }
      .navbar{
        margin-left:0;
      }
    }

    /* CARD */
    .card{
      border:none;
      border-radius:12px;
      box-shadow:0 8px 20px rgba(0,0,0,0.05);
      transition:0.3s;
    }

    .card:hover{
      transform:translateY(-5px);
      box-shadow:0 15px 40px rgba(0,0,0,0.1);
    }

    .stat-icon{
      width:55px;
      height:55px;
      border-radius:10px;
      display:flex;
      align-items:center;
      justify-content:center;
      background:rgba(13,110,253,0.1);
      color:var(--accent);
      font-size:20px;
    }

    /* TABLE */
    .table{
      border-radius:10px;
      overflow:hidden;
    }

    .table thead{
      background:#f1f5f9;
    }

    .table-hover tbody tr:hover{
      background:#f9fafb;
    }

    /* BADGE */
    .badge-paid{background:#198754;}
    .badge-pending{background:#ffc107;color:black;}
    .badge-cancel{background:#dc3545;}

    /* ALERT */
    .alert{
      border-radius:10px;
    }

  </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">

    <button id="sidebarToggle" class="btn btn-link text-white d-lg-none me-2" aria-label="Toggle sidebar"><i class="bi bi-list" style="font-size:1.25rem"></i></button>

    <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
      Bakso Cahaya Mandiri
    </a>

    <div class="ms-auto">
      @auth
      <div class="dropdown">
        <a class="text-white text-decoration-none d-flex align-items-center" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle me-2"></i>
          {{ auth()->user()->name }}
        </a>

        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="dropdown-item">Logout</button>
            </form>
          </li>
        </ul>
      </div>
      @endauth
    </div>

  </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar p-3">
  @auth
    @php
      $role = auth()->user()->role;
      $roleTitle = $role ? ucfirst($role) : '';
      $subTitle = ($role === 'admin') ? 'Admin Panel' : (($role === 'kasir') ? 'Kasir Panel' : '');
    @endphp

    <h5 class="text-white">{{ $roleTitle }}</h5>
    <small class="text-white-50">{{ $subTitle }}</small>
  @else
    <h5 class="text-white">Guest</h5>
    <small class="text-white-50"></small>
  @endauth

  <div class="mt-4">
    @auth
      @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="list-group-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>

        <a href="{{ route('admin.menus.index') }}" class="list-group-item {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
          <i class="bi bi-card-list me-2"></i> Menu
        </a>

        <a href="{{ route('admin.ingredients.index') }}" class="list-group-item {{ request()->routeIs('admin.ingredients.*') ? 'active' : '' }}">
          <i class="bi bi-box-seam me-2"></i> Stok
        </a>

        <a href="{{ route('pos.index') }}" class="list-group-item {{ request()->routeIs('pos.*') ? 'active' : '' }}">
          <i class="bi bi-receipt me-2"></i> Transaksi
        </a>

        <a href="{{ route('admin.reports.sales') }}" class="list-group-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
          <i class="bi bi-bar-chart me-2"></i> Laporan
        </a>

      @else
        @if(auth()->user()->role === 'dapur')
          <a href="{{ route('dapur.index') }}" class="list-group-item {{ request()->routeIs('dapur.*') ? 'active' : '' }}">
            <i class="bi bi-egg-fried me-2"></i> Dapur
          </a>
        @else
          {{-- Kasir sidebar (limited) --}}
          <a href="{{ route('kasir.pos') }}" class="list-group-item {{ request()->routeIs('kasir.pos') || request()->routeIs('pos.index') ? 'active' : '' }}">
            <i class="bi bi-receipt me-2"></i> POS
          </a>
        @endif
      @endif
    @endauth
  </div>
</div>

<!-- CONTENT -->
<div class="content-area">
  <div class="container-fluid">

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

<script>
  (function(){
    const btn = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    if(!btn || !sidebar) return;

    btn.addEventListener('click', function(e){
      e.stopPropagation();
      sidebar.classList.toggle('show');
    });

    // Close sidebar when clicking outside on small screens
    document.addEventListener('click', function(e){
      if(window.innerWidth <= 768 && sidebar.classList.contains('show')){
        if(!sidebar.contains(e.target) && !btn.contains(e.target)){
          sidebar.classList.remove('show');
        }
      }
    });
  })();
</script>

</body>
</html>