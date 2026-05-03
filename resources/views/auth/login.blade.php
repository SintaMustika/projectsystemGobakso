
@extends('layouts.auth')

@section('content')

  <div class="d-flex align-items-center justify-content-center" style="min-height:75vh;">
    <div class="card shadow-sm w-100" style="max-width:420px;border-radius:12px;">
      <div class="card-body p-4">
        <div class="text-center mb-3">
          <div class="mb-2"><i class="bi bi-basket3-fill text-primary" style="font-size:36px"></i></div>
          <h4 class="mb-1">Login Sistem Kasir Bakso</h4>
          <div class="text-muted small">Masuk menggunakan akun Anda</div>
        </div>

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

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label small" for="remember">Remember me</label>
            </div>
          </div>

          <div class="d-grid">
            <button class="btn btn-primary">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
