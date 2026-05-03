@extends('layouts.app')

@section('content')
  <h3>Edit Menu</h3>
  <form method="POST" action="{{ route('admin.menus.update', $menu->id) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" value="{{ old('name', $menu->name) }}" required>
      @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Price</label>
      <input name="price" type="number" step="0.01" class="form-control" value="{{ old('price', $menu->price) }}" required>
      @error('price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Image (upload)</label>
      @if($menu->image)
        <div class="mb-2">
          <img src="{{ asset('storage/menus/' . $menu->image) }}" alt="{{ $menu->name }}" style="max-height:140px;object-fit:cover;width:220px" class="rounded">
        </div>
      @endif
      <input name="image" type="file" class="form-control">
      @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-check mb-3">
      <input name="is_available" value="1" class="form-check-input" type="checkbox" {{ old('is_available', $menu->is_available) ? 'checked' : ''}}>
      <label class="form-check-label">Available</label>
    </div>
    <button class="btn btn-primary">Update</button>
  </form>
@endsection
