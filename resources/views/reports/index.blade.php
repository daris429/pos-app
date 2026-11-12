@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Laporan Penjualan</h4>
            <small class="text-muted">Ringkasan performa penjualan dan produk terlaris toko Anda</small>
        </div>
        <div class="d-print-none d-flex gap-2">
            <a href="{{ route('reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- FORM FILTER TANGGAL -->
    <div class="card border-0 shadow-sm p-3 mb-4 d-print-none">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- WIDGET RINGKASAN STATISTIK -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary text-white shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-white-50 fw-bold">Total Penjualan</small>
                        <h4 class="fw-bold m-0 mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded p-3">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-success text-white shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-white-50 fw-bold">Total Keuntungan</small>
                        <h4 class="fw-bold m-0 mt-1">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h4>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded p-3">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-warning text-white shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-white-50 fw-bold">Total Transaksi</small>
                        <h4 class="fw-bold m-0 mt-1">{{ number_format($totalTransactions, 0, ',', '.') }}</h4>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded p-3">
                        <i class="bi bi-cart-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 bg-indigo text-white shadow-sm p-3" style="background-color: #6366f1;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-white-50 fw-bold">Rata-rata Transaksi</small>
                        <h4 class="fw-bold m-0 mt-1">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</h4>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded p-3">
                        <i class="bi bi-calculator fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DETAIL LAPORAN -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-calendar-week me-2 text-primary"></i>Rincian Penjualan Harian</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th class="text-center">Jumlah Transaksi</th>
                                <th class="text-end">Omzet (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailySales as $sale)
                                <tr>
                                    <td>{{ $sale->date }}</td>
                                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $sale->total_transactions }}</span></td>
                                    <td class="text-end fw-bold">Rp {{ number_format($sale->omzet, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Tidak ada data transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-trophy me-2 text-warning"></i>10 Produk Terlaris</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th class="text-center">Terjual</th>
                                <th class="text-end">Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $product)
                                <tr>
                                    <td class="fw-bold">{{ $product->name }}</td>
                                    <td class="text-center"><span class="badge bg-secondary rounded-pill px-3">{{ $product->total_qty }}</span></td>
                                    <td class="text-end fw-bold text-primary">Rp {{ number_format($product->total_omzet, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada data produk terjual.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .d-print-none { display: none !important; }
    .flex-grow-1 { padding: 0 !important; }
}
</style>
@endsection