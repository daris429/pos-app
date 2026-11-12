@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Input Stok Masuk Baru</h4>
            <small class="text-muted">Isi form di bawah ini untuk mencatat restock barang dari supplier</small>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <h6 class="fw-bold mb-3 text-primary">Informasi Pembelian</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">No. Ref / Nota</label>
                        <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no', $refNo) }}" required readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Supplier</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan pembelian..."></textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0 text-primary">Daftar Produk Restock</h6>
                        <button type="button" class="btn btn-sm btn-success" id="addRow">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="productTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Pilih Produk</th>
                                    <th width="140">Jumlah (Qty)</th>
                                    <th width="180">Harga Beli Satuan</th>
                                    <th width="50" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="products[0][id]" class="form-select product-select" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ (int)$product->purchase_price }}">
                                                    {{ $product->sku }} - {{ $product->name }} (Stok saat ini: {{ $product->stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="products[0][qty]" class="form-control" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="products[0][price]" class="form-control price-input" placeholder="0" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Stok Masuk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let rowIdx = 1;

document.getElementById('addRow').addEventListener('click', function() {
    let table = document.getElementById('productTable').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow();
    
    newRow.innerHTML = `
        <td>
            <select name="products[${rowIdx}][id]" class="form-select product-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ (int)$product->purchase_price }}">
                        {{ $product->sku }} - {{ $product->name }} (Stok saat ini: {{ $product->stock }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="products[${rowIdx}][qty]" class="form-control" value="1" min="1" required>
        </td>
        <td>
            <input type="number" name="products[${rowIdx}][price]" class="form-control price-input" placeholder="0" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
        </td>
    `;
    rowIdx++;
});

document.addEventListener('change', function(e) {
    if (e.target && e.target.classList.contains('product-select')) {
        let selectedOption = e.target.options[e.target.selectedIndex];
        let price = selectedOption.getAttribute('data-price') || 0;
        let row = e.target.closest('tr');
        row.querySelector('.price-input').value = price;
    }
});

document.addEventListener('click', function(e) {
    if (e.target && (e.target.classList.contains('remove-row') || e.target.closest('.remove-row'))) {
        let rows = document.querySelectorAll('#productTable tbody tr');
        if (rows.length > 1) {
            e.target.closest('tr').remove();
        } else {
            alert('Minimal harus ada 1 item produk!');
        }
    }
});
</script>
@endpush
@endsection