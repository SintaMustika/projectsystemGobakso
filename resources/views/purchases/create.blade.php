@extends('layouts.app')

@section('content')
  <h3>Tambah Pembelian Bahan</h3>

  <form method="POST" action="{{ route('admin.purchases.store') }}">
    @csrf

    <div class="mb-3">
      <label class="form-label">Supplier</label>
      <input name="supplier" class="form-control" value="{{ old('supplier') }}" required>
      @error('supplier')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Tanggal</label>
      <input name="purchase_date" type="date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
      @error('purchase_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <h5>Items</h5>
    <table class="table" id="itemsTable">
      <thead><tr><th>Bahan</th><th>Qty</th><th>Harga Satuan</th><th></th></tr></thead>
      <tbody>
          <tr>
            <td>
              <select name="items[0][ingredient_id]" class="form-select" required>
                <option value="">-- pilih bahan --</option>
                @foreach($ingredients as $ing)
                  <option value="{{ $ing->id }}">{{ $ing->item_name }}@if($ing->unit) ({{ $ing->unit }})@endif</option>
                @endforeach
              </select>
            </td>
            <td><input name="items[0][qty]" type="number" step="0.001" class="form-control" required></td>
            <td><input name="items[0][unit_price]" type="number" step="0.01" class="form-control" required></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>
          </tr>
      </tbody>
    </table>

    <button type="button" id="addItem" class="btn btn-secondary btn-sm mb-3">Tambah Bahan</button>

    <div>
      <button class="btn btn-primary">Simpan Pembelian</button>
      <a href="{{ route('admin.purchases.index') }}" class="btn btn-link">Batal</a>
    </div>
  </form>

@push('scripts')
<script>
  (function(){
    const ingredients = @json($ingredients->map(function($i){ return ['id' => $i->id, 'label' => $i->item_name . ($i->unit ? ' (' . $i->unit . ')' : '')]; })->values());
    let idx = 1;
    document.getElementById('addItem').addEventListener('click', function(){
      const tbody = document.getElementById('itemsBody');
      const tr = document.createElement('tr');
      const select = document.createElement('select');
      select.className = 'form-select';
      select.name = 'items[' + idx + '][ingredient_id]';
      select.required = true;
      const empty = document.createElement('option'); empty.value = ''; empty.text = '-- pilih bahan --';
      select.appendChild(empty);
      ingredients.forEach(function(it){
        const o = document.createElement('option'); o.value = it.id; o.text = it.label; select.appendChild(o);
      });

      tr.innerHTML = '<td></td><td><input name="items[' + idx + '][qty]" type="number" step="0.001" class="form-control" required></td><td><input name="items[' + idx + '][unit_price]" type="number" step="0.01" class="form-control" required></td><td><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>';
      tr.querySelector('td').appendChild(select);
      tbody.appendChild(tr);
      idx++;
    });

    document.addEventListener('click', function(e){
      if(e.target && e.target.classList.contains('remove-row')){
        const tr = e.target.closest('tr');
        if(tr) tr.remove();
      }
    });
  })();
</script>
@endpush

@endsection
