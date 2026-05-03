@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Ingredients</h3>
  <a href="{{ route('admin.ingredients.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Ingredient</a>
  </div>

  <div class="card card-soft">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="text-muted small">
            <tr><th>ID</th><th>Item</th><th>Stock</th><th>Min Stock</th><th class="text-end">Actions</th></tr>
          </thead>
          <tbody>
            @foreach($ingredients as $ing)
              @php $isLow = $ing->stock_quantity < $ing->min_stock; @endphp
              <tr class="{{ $isLow ? 'table-danger' : '' }}">
                <td>{{ $ing->id }}</td>
                <td>{{ $ing->item_name }}</td>
                <td>
                  <strong class="{{ $isLow ? 'text-danger' : '' }}">{{ rtrim(rtrim((string)$ing->stock_quantity, '0'), '.') }}</strong>
                  @if($ing->unit)
                    <span class="badge bg-secondary ms-2">{{ $ing->unit }}</span>
                  @endif
                  @if($isLow)
                    <span class="badge bg-danger ms-2">Low</span>
                  @endif
                </td>
                <td>{{ rtrim(rtrim((string)$ing->min_stock, '0'), '.') }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.ingredients.edit', $ing->id) }}" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-pencil"></i></a>
                  <form class="d-inline" action="{{ route('admin.ingredients.destroy', $ing->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete ingredient?')"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $ingredients->links() }}</div>
    </div>
  </div>
@endsection
