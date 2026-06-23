@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Pengaturan QRIS</h3>
  </div>

  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">QRIS Saat Ini</h5>

          @if($setting && $setting->qris_url)
            <img src="{{ $setting->qris_url }}" alt="QRIS" class="img-fluid border rounded mb-3">
            <div class="small text-muted text-break">{{ $setting->qris_url }}</div>
          @else
            <div class="alert alert-warning mb-0">QRIS belum diupload.</div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Upload QRIS</h5>

          <form method="POST" action="{{ route('admin.payment-settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label class="form-label">Gambar QRIS</label>
              <input name="qris_image" type="file" class="form-control" accept="image/png,image/jpeg,image/webp" required>
              @error('qris_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <button class="btn btn-primary">
              <i class="bi bi-upload me-1"></i> Simpan QRIS
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
