@extends('layouts.app')
@section('title', 'Kasir / Transaksi')

@section('content')
<div class="row g-3">
    <!-- Notifikasi Error Jika Ada -->
    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Kolom Kiri: Pilih Produk -->
    <div class="col-md-7">
        <div class="card card-custom p-3 mb-3">
            <input type="text" id="search-product" class="form-control form-control-lg" placeholder="Cari nama produk atau scan barcode..." autofocus>
        </div>

        <div class="row g-2" id="product-list" style="max-height: 580px; overflow-y: auto;">
            @foreach($products as $product)
                <div class="col-4 col-md-3 product-item" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku ?? '') }}">
                    <div class="card card-custom h-100 p-2 text-center btn-add-cart" 
                         style="cursor: pointer;" 
                         data-id="{{ $product->id }}" 
                         data-name="{{ $product->name }}" 
                         data-price="{{ $product->selling_price }}" 
                         data-stock="{{ $product->stock }}">
                        <div class="fw-bold text-truncate" style="font-size: 0.9rem;">{{ $product->name }}</div>
                        <div class="text-primary fw-bold mt-1" style="font-size: 0.85rem;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                        <div class="text-muted small mt-1" style="font-size: 0.75rem;">Stok: {{ $product->stock }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Kolom Kanan: Keranjang & Form -->
    <div class="col-md-5">
        <div class="card card-custom p-3">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Keranjang Belanja</h5>
            
            <form action="{{ route('sales.store') }}" method="POST" id="form-checkout">
                @csrf
                
                <div id="cart-inputs-container"></div>

                <div class="table-responsive mb-3" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm align-middle" id="cart-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center" style="width: 80px;">Qty</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center" style="width: 30px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            <tr id="empty-cart-msg">
                                <td colspan="4" class="text-center text-muted py-4">Keranjang masih kosong</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pelanggan</label>
                    <select name="customer_id" class="form-select">
                        <option value="">-- Pelanggan Umum --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">Subtotal:</span>
                    <span class="fw-bold" id="text-subtotal">Rp 0</span>
                </div>

                <!-- Pembayaran -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Metode Bayar</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>

                <div class="mb-3" id="wrapper-cash-input">
                    <label class="form-label small fw-semibold">Jumlah Bayar (Rp)</label>
                    <input type="number" name="cash_amount" id="cash_amount" class="form-control form-control-lg fw-bold" placeholder="0" min="0">
                </div>

                <div class="d-flex justify-content-between mb-3 fs-5 border-top pt-2">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-success" id="text-total">Rp 0</span>
                </div>

                <div class="d-flex justify-content-between mb-3 text-secondary" id="wrapper-change">
                    <span>Kembalian:</span>
                    <span class="fw-bold" id="text-change">Rp 0</span>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" id="btn-submit-sale" disabled>
                    Simpan Transaksi
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal QRIS -->
<div class="modal fade" id="modalQris" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-3 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-qr-code-scan me-2"></i>Scan Pembayaran QRIS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">Silakan scan kode QRIS di bawah ini menggunakan aplikasi M-Banking atau E-Wallet pelanggan.</p>
                
                <div class="p-3 bg-light rounded-3 d-inline-block border mb-3">
                    <img src="{{ asset('images/qris.jpeg') }}" 
                         alt="Kode QRIS Toko" 
                         class="img-fluid rounded" 
                         style="max-width: 280px; height: auto;" 
                         onerror="this.onerror=null; this.src='{{ asset('images/qris.jpg') }}'; if(!this.complete || this.naturalWidth === 0) this.src='{{ asset('images/qris.png') }}';">
                </div>
                
                <div class="fs-4 fw-bold text-success mb-1" id="qris-total-text">Total: Rp 0</div>
                <div class="badge bg-warning text-dark fs-6 px-3 py-2">Menunggu Pembayaran...</div>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-success fw-bold px-4 py-2" id="btn-confirm-qris">
                    <i class="bi bi-check-circle me-1"></i> Pembayaran Selesai
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];

    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    function renderCart() {
        const tbody = document.getElementById('cart-items');
        const hiddenInputs = document.getElementById('cart-inputs-container');
        
        tbody.innerHTML = '';
        hiddenInputs.innerHTML = '';

        if (cart.length === 0) {
            tbody.innerHTML = '<tr id="empty-cart-msg"><td colspan="4" class="text-center text-muted py-4">Keranjang masih kosong</td></tr>';
            document.getElementById('text-subtotal').innerText = 'Rp 0';
            document.getElementById('text-total').innerText = 'Rp 0';
            document.getElementById('text-change').innerText = 'Rp 0';
            document.getElementById('btn-submit-sale').disabled = true;
            return;
        }

        let total = 0;
        cart.forEach((item, index) => {
            const subtotal = item.price * item.qty;
            total += subtotal;

            tbody.innerHTML += `
                <tr>
                    <td>
                        <div class="fw-semibold small">${item.name}</div>
                        <div class="text-muted extra-small">${formatRupiah(item.price)}</div>
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center input-qty" data-index="${index}" value="${item.qty}" min="1" max="${item.stock}">
                    </td>
                    <td class="text-end fw-bold small">${formatRupiah(subtotal)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm text-danger p-0 btn-remove-item" data-index="${index}">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </td>
                </tr>
            `;

            hiddenInputs.innerHTML += `
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
                <input type="hidden" name="items[${index}][price]" value="${item.price}">
            `;
        });

        document.getElementById('text-subtotal').innerText = formatRupiah(total);
        document.getElementById('text-total').innerText = formatRupiah(total);
        document.getElementById('qris-total-text').innerText = 'Total: ' + formatRupiah(total);
        document.getElementById('btn-submit-sale').disabled = false;
        calculateChange();
    }

    function calculateChange() {
        let total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        let cash = parseFloat(document.getElementById('cash_amount').value) || 0;
        let method = document.getElementById('payment_method').value;

        if (method !== 'cash') {
            document.getElementById('cash_amount').value = total;
            document.getElementById('text-change').innerText = 'Rp 0';
            return;
        }

        let change = cash - total;
        document.getElementById('text-change').innerText = formatRupiah(change > 0 ? change : 0);
    }

    document.querySelectorAll('.btn-add-cart').forEach(card => {
        card.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const stock = parseInt(this.dataset.stock);

            const existingIndex = cart.findIndex(item => item.id == id);
            if (existingIndex > -1) {
                if (cart[existingIndex].qty < stock) {
                    cart[existingIndex].qty++;
                } else {
                    alert('Stok produk tidak mencukupi.');
                }
            } else {
                if (stock > 0) {
                    cart.push({ id, name, price, stock, qty: 1 });
                } else {
                    alert('Stok produk habis.');
                }
            }
            renderCart();
        });
    });

    document.getElementById('cart-table').addEventListener('input', function(e) {
        if (e.target.classList.contains('input-qty')) {
            const index = e.target.dataset.index;
            let val = parseInt(e.target.value) || 1;
            if (val > cart[index].stock) val = cart[index].stock;
            cart[index].qty = val;
            renderCart();
        }
    });

    document.getElementById('cart-table').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-item')) {
            const index = e.target.closest('.btn-remove-item').dataset.index;
            cart.splice(index, 1);
            renderCart();
        }
    });

    document.getElementById('payment_method').addEventListener('change', function() {
        const method = this.value;
        const cashWrapper = document.getElementById('wrapper-cash-input');
        const changeWrapper = document.getElementById('wrapper-change');
        
        if (method === 'qris') {
            cashWrapper.style.display = 'none';
            changeWrapper.style.display = 'none';
            
            let total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            if (total > 0) {
                document.getElementById('cash_amount').value = total;
                const qrisModal = new bootstrap.Modal(document.getElementById('modalQris'));
                qrisModal.show();
            } else {
                alert('Pilih produk terlebih dahulu.');
                this.value = 'cash';
                cashWrapper.style.display = 'block';
                changeWrapper.style.display = 'flex';
            }
        } else {
            cashWrapper.style.display = 'block';
            changeWrapper.style.display = 'flex';
        }
        calculateChange();
    });

    document.getElementById('cash_amount').addEventListener('input', calculateChange);

    // KLIK PEMBAYARAN SELESAI PADA MODAL QRIS
    document.getElementById('btn-confirm-qris').onclick = function() {
        let total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        document.getElementById('cash_amount').value = total;
        
        // Kirim Form Langsung
        document.getElementById('form-checkout').submit();
    };

    document.getElementById('search-product').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.dataset.name;
            const sku = item.dataset.sku;
            if (name.includes(query) || sku.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endpush