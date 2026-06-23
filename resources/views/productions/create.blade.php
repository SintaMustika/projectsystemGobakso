@extends('layouts.app')

@section('content')
  <h3>Produksi Menu</h3>

  <form method="POST" action="{{ route('admin.productions.store') }}">
    @csrf

    <div class="mb-3">
      <label class="form-label">Menu</label>
      <select name="menu_id" class="form-select" required>
        <option value="">-- pilih menu --</option>
        @foreach($menus as $m)
          <option value="{{ $m->id }}">{{ $m->name }}</option>
        @endforeach
      </select>
      @error('menu_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Jumlah Produksi</label>
      <input name="qty" type="number" class="form-control" value="{{ old('qty',1) }}" min="1" required>
      @error('qty')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Tanggal Produksi</label>
      <input name="production_date" type="date" class="form-control" value="{{ old('production_date', date('Y-m-d')) }}">
    </div>

    <div>
      <button class="btn btn-primary">Simpan Produksi</button>
      <a href="{{ route('admin.productions.index') }}" class="btn btn-link">Batal</a>
    </div>
  </form>

@endsection
