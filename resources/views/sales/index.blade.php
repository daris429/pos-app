@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Riwayat Transaksi</h4>
            <small class="text-muted">Daftar seluruh transaksi penjualan toko</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td class="fw-bold text-primary">{{ $sale->invoice_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->user->name ?? '-' }}</td>
                            <td>{{ $sale->customer->name ?? 'Umum' }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($sale->status === 'CANCELLED' || $sale->status === 'VOID')
                                    <span class="badge bg-danger">DIBATALKAN</span>
                                @else
                                    <span class="badge bg-success">PAID</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($sale->status !== 'CANCELLED' && $sale->status !== 'VOID')
                                        <form action="{{ route('sales.cancel', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan transaksi {{ $sale->invoice_no }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Batalkan Transaksi">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection