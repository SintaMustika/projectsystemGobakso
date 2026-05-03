@extends('layouts.app')

@section('content')
  <h3>Create Ingredient</h3>
  <form method="POST" action="{{ route('admin.ingredients.store') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Item Name</label>
      <input name="item_name" class="form-control" value="{{ old('item_name') }}" required>
      @error('item_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Stock Quantity</label>
      <input name="stock_quantity" type="number" step="0.001" class="form-control" value="{{ old('stock_quantity') }}" required>
      @error('stock_quantity')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Unit</label>
      <select name="unit" class="form-select">
        <option value="">-- select unit --</option>
        <option value="gram" {{ old('unit') == 'gram' ? 'selected' : '' }}>gram</option>
        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>pcs</option>
        <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>ml</option>
        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>liter</option>
      </select>
      @error('unit')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Price per Unit (Rp)</label>
      <input name="price" type="number" step="0.01" class="form-control" value="{{ old('price') }}">
      @error('price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Min Stock</label>
      <input name="min_stock" type="number" step="0.001" class="form-control" value="{{ old('min_stock') }}">
      @error('min_stock')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary">Save</button>
  </form>
@endsection
