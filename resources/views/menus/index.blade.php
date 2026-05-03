@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Menus</h3>
    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Menu</a>
  </div>

  <div class="card card-soft">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="text-muted small">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Price</th>
              <th>Profit</th>
              <th>Available</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($menus as $m)
              <tr>
                <td>{{ $m->id }}</td>
                <td>{{ $m->name }}</td>
                <td>Rp {{ number_format($m->price,2,',','.') }}</td>
                <td>
                  @php $profit = $m->profitPerUnit(); @endphp
                  @if(is_null($profit))
                    <span class="text-muted">Belum ada resep</span>
                  @else
                    Rp {{ number_format($profit,2,',','.') }}
                  @endif
                </td>
                <td>
                  @if($m->is_available)
                    <span class="badge bg-success">Available</span>
                  @else
                    <span class="badge bg-secondary">Unavailable</span>
                  @endif
                </td>
                <td class="text-end">
                  <a href="{{ route('admin.menus.recipe', $m->id) }}" class="btn btn-sm btn-outline-primary me-2" title="Kelola Resep"><i class="bi bi-journal-bookmark"></i></a>
                  <a href="{{ route('admin.menus.edit', $m->id) }}" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-pencil"></i></a>
                  <form class="d-inline" action="{{ route('admin.menus.destroy', $m->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete menu?')"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $menus->links() }}</div>
    </div>
  </div>
@endsection
