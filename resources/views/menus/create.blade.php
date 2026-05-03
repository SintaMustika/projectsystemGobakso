@extends('layouts.app')

@section('content')
  <h3>Create Menu</h3>
  <form method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" value="{{ old('name') }}" required>
      @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Price</label>
      <input name="price" type="number" step="0.01" class="form-control" value="{{ old('price') }}" required>
      @error('price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Image (upload)</label>
      <input name="image" type="file" class="form-control">
      @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-check mb-3">
      <input name="is_available" value="1" class="form-check-input" type="checkbox" {{ old('is_available', 1) ? 'checked' : '' }}>
      <label class="form-check-label">Available</label>
    </div>
    <button class="btn btn-primary">Save</button>
  </form>
@endsection
