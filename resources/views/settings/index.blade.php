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
                <form action="{{ route("settings.update") }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("PUT")

                    <!-- Upload Logo Toko -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Logo Toko (Kiri Atas)</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            @php
                                $currentLogo = $settings["store_logo"]->value ?? null;
                            @endphp
                            @if($currentLogo)
                                <img src="{{ asset('storage/' . $currentLogo) }}" alt="Logo Toko" class="rounded border bg-white p-1" style="width: 55px; height: 55px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                                    <i class="bi bi-shop fs-3"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <input type="file" name="store_logo" class="form-control form-control-sm" accept="image/*">
                                <div class="form-text">Format JPG, PNG, WEBP, SVG (Maks. 2MB). Kosongkan jika tidak ingin mengganti logo.</div>
                            </div>
                        </div>
                    </div>

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
                        <input type="text" name="store_phone" class="form-control" value="{{ $settings["store_phone"]->value ?? "" }}" maxlength="15" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);" required placeholder="Contoh: 081234567890">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pesan Catatan Kaki Struk (Receipt Footer)</label>
                        <textarea name="receipt_footer" class="form-control" rows="2" placeholder="Baris 1: Terima kasih telah berbelanja!&#10;Baris 2: Barang yang sudah dibeli tidak dapat ditukar.">{{ $settings["receipt_footer"]->value ?? "" }}</textarea>
                        <div class="form-text">Tekan <strong>Enter</strong> untuk membuat baris kedua. Teks ini akan dicetak di bagian paling bawah nota pembayaran.</div>
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