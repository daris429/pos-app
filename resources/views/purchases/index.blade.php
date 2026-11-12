@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Stok Masuk (Restock Supplier)</h4>
            <small class="text-muted">Kelola pembelian barang dari supplier untuk menambah stok toko</small>
        </div>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Stok Masuk
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No Ref/Nota</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Petugas</th>
                        <th class="text-end">Total Pembelian</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td class="fw-bold text-primary">{{ $purchase->reference_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-secondary">{{ $purchase->supplier->name ?? '-' }}</span></td>
                            <td>{{ $purchase->user->name ?? '-' }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat transaksi stok masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection