@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h4 class="fw-bold m-0">Detail Stok Masuk</h4>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary me-2"><i class="bi bi-printer me-1"></i> Cetak</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
            <div>
                <h5 class="fw-bold text-primary">{{ config('app.name', 'Warung Daris') }}</h5>
                <small class="text-muted">Nota Penerimaan Barang Masuk</small>
            </div>
            <div class="text-end">
                <h6 class="fw-bold m-0">{{ $purchase->reference_no }}</h6>
                <small class="text-muted">Tanggal: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</small>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <small class="text-muted d-block fw-bold">SUPPLIER:</small>
                <strong>{{ $purchase->supplier->name ?? '-' }}</strong><br>
                <small>{{ $purchase->supplier->phone ?? '-' }}</small><br>
                <small>{{ $purchase->supplier->address ?? '-' }}</small>
            </div>
            <div class="col-6 text-end">
                <small class="text-muted d-block fw-bold">PETUGAS PENERIMA:</small>
                <strong>{{ $purchase->user->name ?? '-' }}</strong>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Produk</th>
                        <th class="text-center" width="120">Jumlah (Qty)</th>
                        <th class="text-end" width="180">Harga Beli Satuan</th>
                        <th class="text-end" width="180">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $item->product->name ?? '-' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end fw-bold">Total Nilai Pembelian:</th>
                        <th class="text-end fw-bold text-primary">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($purchase->notes)
            <div class="mt-3 p-3 bg-light rounded">
                <small class="fw-bold d-block text-muted">Catatan:</small>
                <small>{{ $purchase->notes }}</small>
            </div>
        @endif
    </div>
</div>

<style>
@media print {
    .sidebar, .d-print-none { display: none !important; }
    .flex-grow-1 { padding: 0 !important; }
}
</style>
@endsection