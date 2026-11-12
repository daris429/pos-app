@extends("layouts.app")
@section("title", "Pengaturan Toko")

@section("content")
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-gear me-1"></i> Pengaturan Informasi Toko & Struk
            </div>
            <div class="card-body">
                <form action="{{ route("settings.update") }}" method="POST">
                    @csrf
                    @method("PUT")
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Toko</label>
                        <input type="text" name="store_name" class="form-control" value="{{ $settings["store_name"]->value ?? "" }}" required placeholder="Contoh: Toko Berkah Jaya">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat Toko</label>
                        <textarea name="store_address" class="form-control" rows="3" required placeholder="Alamat lengkap toko...">{{ $settings["store_address"]->value ?? "" }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">No. Telepon / WhatsApp</label>
                        <input type="text" name="store_phone" class="form-control" value="{{ $settings["store_phone"]->value ?? "" }}" required placeholder="Contoh: 081234567890">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pesan Catatan Kaki Struk (Receipt Footer)</label>
                        <input type="text" name="receipt_footer" class="form-control" value="{{ $settings["receipt_footer"]->value ?? "" }}" placeholder="Contoh: Terima kasih atas kunjungan Anda!">
                        <div class="form-text">Teks ini akan dicetak di bagian paling bawah nota pembayaran.</div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
