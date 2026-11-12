@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold m-0">Detail Transaksi Penjualan</h4>
        <div>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            <!-- TOMBOL BATALKAN TRANSAKSI: HANYA TAMPIL UNTUK ADMIN -->
            @if(auth()->user()->role === 'admin' && $sale->status !== 'CANCELLED')
                <form action="{{ route('sales.cancel', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini? Stok produk akan dikembalikan.')">
                    @csrf
                    <button type="submit" class="btn btn-danger me-2">
                        <i class="bi bi-x-circle me-1"></i> Batalkan Transaksi
                    </button>
                </form>
            @endif

            <a href="{{ route('sales.receipt', $sale->id) }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i> Cetak Struk
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        <!-- Informasi Transaksi -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Transaksi</h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="140">No. Invoice</td>
                        <td class="fw-bold">{{ $sale->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>{{ $sale->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kasir</td>
                        <td>{{ $sale->user->name ?? 'Kasir' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pelanggan</td>
                        <td>{{ $sale->customer->name ?? 'Umum / Non-Member' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Metode Bayar</td>
                        <td><span class="badge bg-info text-dark">{{ strtoupper($sale->payment_method) }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($sale->status === 'CANCELLED')
                                <span class="badge bg-danger">CANCELLED</span>
                            @else
                                <span class="badge bg-success">PAID</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Item Produk Dibeli -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Item Produk Dibeli</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Harga Satuan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                    <td class="text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pembayaran -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Ringkasan Pembayaran</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-danger">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($sale->discount, 0, ',', '.') }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2 fw-bold fs-5 text-primary">
                    <span>Total Akhir</span>
                    <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Bayar</span>
                    <span>Rp {{ number_format($sale->pay_amount, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection