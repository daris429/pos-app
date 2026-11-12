@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Log Aktivitas Sistem</h4>
            <small class="text-muted">Catatan rekam jejak riwayat aksi seluruh pengguna sistem POS</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="200">Waktu & Tanggal</th>
                        <th width="180">Pengguna</th>
                        <th width="180">Aksi</th>
                        <th>Keterangan / Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="small text-muted">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                <span class="fw-bold">{{ $log->user_name ?? 'System' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $log->action }}</span>
                            </td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat aktivitas pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection