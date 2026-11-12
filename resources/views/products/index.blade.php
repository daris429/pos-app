@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Daftar Produk</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Produk
        </button>
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
                        <th width="70">Foto</th>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="rounded bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 45px; height: 45px;">
                                    @if($product->image && file_exists(public_path('storage/' . $product->image)))
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                    @else
                                        <i class="bi bi-box-seam text-secondary fs-5"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="fw-bold">{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td><span class="badge bg-secondary">{{ $product->category->name ?? '-' }}</span></td>
                            <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                            <td class="fw-bold text-primary">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $product->stock <= $product->min_stock ? 'bg-danger' : 'bg-success' }}">
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editProductModal{{ $product->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL EDIT PRODUK -->
                        <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Produk</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">SKU</label>
                                                    <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Kategori</label>
                                                    <select name="category_id" class="form-select" required>
                                                        <option value="">-- Pilih Kategori --</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold">Nama Produk</label>
                                                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Satuan</label>
                                                    <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Harga Beli</label>
                                                    <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', (int)$product->purchase_price) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Harga Jual</label>
                                                    <input type="number" name="selling_price" class="form-control" value="{{ old('selling_price', (int)$product->selling_price) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Stok</label>
                                                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Stok Minimum</label>
                                                    <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}" required>
                                                </div>
                                                
                                                <div class="col-12 mt-3">
                                                    <label class="form-label small fw-bold">Foto Produk</label>
                                                    @if($product->image && file_exists(public_path('storage/' . $product->image)))
                                                        <div class="mb-2">
                                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-height: 100px;">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                    <small class="text-muted">Format: JPG, PNG, WEBP (Max 2MB). Biarkan kosong jika tidak diganti.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL TAMBAH PRODUK -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">SKU</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Produk</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Satuan</label>
                            <input type="text" name="unit" class="form-control" placeholder="pcs/kg" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Harga Beli</label>
                            <input type="number" name="purchase_price" class="form-control" placeholder="5000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Harga Jual</label>
                            <input type="number" name="selling_price" class="form-control" placeholder="7000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Stok</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Stok Minimum</label>
                            <input type="number" name="min_stock" class="form-control" required>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label small fw-bold">Foto Produk</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, WEBP (Max 2MB).</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection