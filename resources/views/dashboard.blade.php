@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">Dashboard</h2>
            <p class="text-muted small m-0">Ringkasan aktivitas dan statistik toko Anda hari ini.</p>
        </div>
        <div class="card border-0 shadow-sm px-3 py-2 bg-white">
            <span class="text-primary font-weight-bold">
                <i class="bi bi-calendar-event me-2"></i> {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}
            </span>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-primary text-white rounded-3 h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase">Penjualan Hari Ini</div>
                        <h3 class="fw-bold mb-0 mt-1">Rp {{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-3 p-3">
                        <i class="bi bi-wallet2 fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-success text-white rounded-3 h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase">Transaksi Hari Ini</div>
                        <h3 class="fw-bold mb-0 mt-1">{{ $transaksiHariIni ?? 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-3 p-3">
                        <i class="bi bi-cart-check fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-warning text-white rounded-3 h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase">Stok Menipis</div>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stokMenipis ?? 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-3 p-3">
                        <i class="bi bi-exclamation-triangle fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-info text-white rounded-3 h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-white-50 small font-weight-bold text-uppercase">Total Produk Active</div>
                        <h3 class="fw-bold mb-0 mt-1">{{ $totalProduk ?? $totalProdukActive ?? 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-3 p-3">
                        <i class="bi bi-box-seam fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel 5 Transaksi Terakhir -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold"><i class="bi bi-clock-history me-2"></i> 5 Transaksi Terakhir</h6>
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-light text-primary">Lihat Semua &rarr;</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($latestSales ?? $recentSales ?? []) as $sale)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $sale->invoice_number ?? $sale->invoice ?? ('INV-' . $sale->id) }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->created_at ?? $sale->sale_date)->format('Y-m-d H:i:s') }}</td>
                                <td><span class="badge bg-light text-dark border"><i class="bi bi-person me-1"></i> {{ $sale->user->name ?? 'Administrator' }}</span></td>
                                <td class="fw-bold text-success">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-primary rounded-circle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection