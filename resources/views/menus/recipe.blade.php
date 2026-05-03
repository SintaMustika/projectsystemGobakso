@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Kelola Resep: {{ $menu->name }}</h3>
  <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Back to Menus</a>
  </div>

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row">
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-body">
          <h5>Add Ingredient</h5>
          <form method="POST" action="{{ route('admin.menus.recipe.store', $menu->id) }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Ingredient</label>
              <select name="ingredient_id" class="form-select">
                @foreach($ingredients as $ing)
                  <option value="{{ $ing->id }}">{{ $ing->item_name }} (stock: {{ rtrim(rtrim((string)$ing->stock_quantity, '0'), '.') }}{{ $ing->unit ? ' ' . $ing->unit : '' }})</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Quantity (usage)</label>
              <input type="number" step="0.001" min="0.001" name="qty_usage" class="form-control" required>
            </div>
            <button class="btn btn-primary">Add</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5>Recipe Items</h5>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr><th>Ingredient</th><th>Quantity</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @foreach($menu->recipes as $r)
                  <tr>
                    <td>{{ optional($r->ingredient)->item_name ?? '—' }}</td>
                    <td>
                      <form class="d-flex" method="POST" action="{{ route('admin.menus.recipe.update', [$menu->id, $r->id]) }}">
                        @csrf @method('PUT')
                        <input type="number" step="0.001" min="0.001" name="qty_usage" value="{{ old('qty_usage', rtrim(rtrim((string)$r->qty_usage, '0'), '.')) }}" class="form-control me-2" style="width:140px;">
                        @if(optional($r->ingredient)->unit)
                          <span class="badge bg-secondary align-self-center">{{ optional($r->ingredient)->unit }}</span>
                        @endif
                        <button class="btn btn-sm btn-outline-success me-2" title="Update"><i class="bi bi-check2"></i></button>
                      </form>
                    </td>
                    <td class="text-end">
                      <form method="POST" action="{{ route('admin.menus.recipe.delete', [$menu->id, $r->id]) }}" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus bahan dari resep?')" title="Delete"><i class="bi bi-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                @endforeach
                @if($menu->recipes->isEmpty())
                  <tr><td colspan="3" class="text-center">No ingredients added yet.</td></tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
