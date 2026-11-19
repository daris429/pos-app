@extends('layouts.app')

@section('content')
@php
    $lowStockProducts = \App\Models\Product::whereColumn('stock', '<=', 'min_stock')
        ->orderBy('stock', 'asc')
        ->get();
@endphp

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">Dashboard</h2>
            <p class="text-muted small m-0">Ringkasan aktivitas dan statistik toko Anda hari ini.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Tombol Tema Terang / Gelap di Samping Lonceng -->
            <button type="button" id="themeToggleBtn"
                    class="btn bg-white border-0 shadow-sm d-flex align-items-center gap-2 px-3 py-2"
                    style="border-radius: 10px; height: 42px;"
                    title="Ganti Tema Terang / Gelap">
                <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                <span id="themeText" class="small fw-semibold">Tema Gelap</span>
            </button>

            <!-- Lonceng Notifikasi Stok Menipis (Khusus Admin) -->
            @if(strtolower(auth()->user()->role) === 'admin')
                <div class="dropdown">
                    <button class="btn bg-white border-0 shadow-sm position-relative d-flex align-items-center justify-content-center"
                            type="button"
                            id="lowStockBell"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="width: 42px; height: 42px; border-radius: 10px;"
                            title="Notifikasi Stok Menipis">
                        <i class="bi bi-bell-fill fs-5 {{ $lowStockProducts->count() > 0 ? 'text-warning' : 'text-secondary' }}"></i>
                        @if($lowStockProducts->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;">
                                {{ $lowStockProducts->count() }}
                            </span>
                        @endif
                    </button>

                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2" aria-labelledby="lowStockBell" style="width: 330px; border-radius: 12px; overflow: hidden; z-index: 1060;">
                        <div class="px-3 py-2 bg-warning bg-opacity-10 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-dark">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Peringatan Stok Menipis
                            </span>
                            <span class="badge bg-danger rounded-pill">{{ $lowStockProducts->count() }} Produk</span>
                        </div>

                        <div style="max-height: 280px; overflow-y: auto;">
                            @forelse($lowStockProducts as $item)
                                <a href="{{ route('purchases.index') }}" class="dropdown-item py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                    <div class="text-truncate me-2">
                                        <div class="fw-semibold small text-dark text-truncate">{{ $item->name }}</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">Min. Stok: {{ $item->min_stock }} {{ $item->unit ?? '' }}</small>
                                    </div>
                                    <span class="badge {{ $item->stock == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        Sisa: {{ $item->stock }}
                                    </span>
                                </a>
                            @empty
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-1"></i>
                                    Semua stok produk dalam kondisi aman.
                                </div>
                            @endforelse
                        </div>

                        @if($lowStockProducts->count() > 0)
                            <a href="{{ route('purchases.index') }}" class="dropdown-item text-center small fw-semibold text-primary py-2 bg-light">
                                <i class="bi bi-plus-circle me-1"></i> Restok Sekarang di Stok Masuk
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Kotak Tanggal -->
            <div class="card border-0 shadow-sm px-3 py-2 bg-white d-flex justify-content-center" style="height: 42px; border-radius: 10px;">
                <span class="text-primary fw-semibold small">
                    <i class="bi bi-calendar-event me-2"></i> {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}
                </span>
            </div>
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
                        <h3 class="fw-bold mb-0 mt-1">{{ $stokMenipis ?? $lowStockProducts->count() }}</h3>
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