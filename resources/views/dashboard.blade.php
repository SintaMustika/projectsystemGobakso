@extends('layouts.app')

@section('content')
  <style>
    .dashboard-hero{background:linear-gradient(180deg,rgba(13,110,253,0.03),rgba(13,110,253,0.01));padding:18px;border-radius:.75rem}
    .card-hover-scale{transition:transform .18s ease,box-shadow .18s ease}
    .card-hover-scale:hover{transform:scale(1.01)}
    .stat-icon-lg{width:68px;height:68px;border-radius:.9rem;display:inline-flex;align-items:center;justify-content:center;background:rgba(13,110,253,0.09);color:var(--accent);font-size:1.6rem}
    .recent-scroll{max-height:320px;overflow:auto}
  </style>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card card-soft p-3 card-hover-scale dashboard-hero">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon-lg bg-white"><i class="bi bi-cash-stack text-primary"></i></div>
          <div>
            <small class="text-muted">Total Penjualan Hari Ini</small>
            <div class="h3 mb-0">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-soft p-3 card-hover-scale dashboard-hero">
        <div class="d-flex align-items-center gap-3">
          <div class="stat-icon-lg bg-white"><i class="bi bi-clipboard-check text-success"></i></div>
          <div>
            <small class="text-muted">Jumlah Transaksi (Hari Ini)</small>
            <div class="h3 mb-0">{{ $transactions }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-soft p-3 card-hover-scale dashboard-hero">
        <div class="d-flex align-items-start gap-3">
          <div class="stat-icon-lg bg-white"><i class="bi bi-currency-dollar text-success"></i></div>
          <div>
            <small class="text-muted">Total Profit Hari Ini</small>
            <div class="h3 mb-0">Rp {{ number_format($totalProfit ?? 0, 2, ',', '.') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4 section-gap">
    <div class="col-12">
        <div class="card shadow-sm rounded-3 p-4 mb-4">
          <h5 class="mb-2">Grafik Penjualan 7 Hari Terakhir</h5>
          <p class="text-muted small">Total penjualan per hari (status = paid)</p>
          <div class="bg-white rounded-3 p-3 mb-3 chart-container" style="min-height:300px;">
            <canvas id="salesChart" style="width:100%;height:100%;display:block;"></canvas>
          </div>
        </div>
      </div>
  </div>

  <div class="row mt-4 section-gap">
    <div class="col-12">
      <div class="card card-soft p-3">
        <h5 class="mb-3">Transaksi Terbaru</h5>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-striped table-hover mb-0 align-middle">
              <thead class="table-light small">
                <tr>
                  <th style="width:60px">No</th>
                  <th>Nomor Meja</th>
                  <th class="text-end">Total Harga</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                  <th style="width:130px">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentOrders as $idx => $o)
                  <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $o->table_number ?? '-' }}</td>
                    <td class="text-end">Rp {{ number_format($o->total_price, 0, ',', '.') }}</td>
                    <td>
                      @if($o->status === 'paid')
                        <span class="badge badge-status-paid">Paid</span>
                      @elseif($o->status === 'pending')
                        <span class="badge badge-status-pending">Pending</span>
                      @elseif($o->status === 'cancelled')
                        <span class="badge badge-status-cancelled">Cancelled</span>
                      @else
                        <span class="badge bg-secondary">{{ ucfirst($o->status) }}</span>
                      @endif
                    </td>
                    <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                      <div class="d-flex gap-2 justify-content-end">
                        @if(Route::has('pos.show'))
                          <a href="{{ route('pos.show', $o->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>
                        @else
                          <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>
                        @endif

                        @if(Route::has('pos.destroy'))
                          <form method="POST" action="{{ route('pos.destroy', $o->id) }}" onsubmit="return confirm('Hapus transaksi ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                          </form>
                        @else
                          <button class="btn btn-sm btn-danger" disabled><i class="bi bi-trash"></i> Hapus</button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted">Tidak ada transaksi</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    (function(){
      const labels = @json($chartLabels ?? []);
      const data = @json($chartData ?? []);

      const ctx = document.getElementById('salesChart');
      if (!ctx) return;

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Penjualan (Rp)',
            data: data,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.15)',
            tension: 0.3,
            fill: true,
            pointRadius: 4,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value){
                  return 'Rp ' + Number(value).toLocaleString('id-ID');
                }
              }
            }
          },
        }
      });
    })();
  </script>
@endpush
