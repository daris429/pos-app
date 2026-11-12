@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold m-0">Kasir / Transaksi Penjualan</h4>
            <small class="text-muted">Pilih produk dan lakukan proses transaksi dengan mudah</small>
        </div>
        <div class="badge bg-primary fs-6 px-3 py-2">
            <i class="bi bi-clock me-1"></i> <span id="liveClock"></span>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="row g-3">
            <!-- PENCARIAN & DAFTAR PRODUK (KIRI) -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-3 mb-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchProduct" class="form-control border-start-0" placeholder="Cari nama atau SKU produk..." autofocus>
                    </div>

                    <div class="row g-2 overflow-auto" style="max-height: 520px;" id="productList">
                        @forelse($products as $product)
                            <div class="col-6 col-md-4 product-card" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}">
                                <div class="card h-100 border p-2 text-center btn-add-cart" 
                                     style="cursor: pointer; transition: all 0.2s;"
                                     data-id="{{ $product->id }}"
                                     data-name="{{ $product->name }}"
                                     data-price="{{ (int)$product->selling_price }}"
                                     data-stock="{{ $product->stock }}">
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center mx-auto mb-2 overflow-hidden" style="width: 60px; height: 60px;">
                                        @if($product->image && file_exists(public_path('storage/' . $product->image)))
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                        @else
                                            <i class="bi bi-box-seam text-secondary fs-3"></i>
                                        @endif
                                    </div>
                                    <h6 class="fw-bold text-truncate mb-1" style="font-size: 0.85rem;" title="{{ $product->name }}">{{ $product->name }}</h6>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">Stok: {{ $product->stock }} {{ $product->unit }}</small>
                                    <span class="badge bg-primary text-white">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                Tidak ada produk tersedia dengan stok aktif.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- KERANJANG & DETAIL PEMBAYARAN (KANAN) -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="bi bi-cart3 me-1"></i> Keranjang Belanja</h6>
                    
                    <!-- Tabel Keranjang -->
                    <div class="table-responsive mb-3" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm align-middle" id="cartTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th width="90">Qty</th>
                                    <th class="text-end" width="100">Subtotal</th>
                                    <th width="30"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr id="emptyCartRow">
                                    <td colspan="4" class="text-center py-4 text-muted small">Keranjang masih kosong. Klik produk di sebelah kiri.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ringkasan & Form Pembayaran -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Total Belanja:</span>
                            <span class="fw-bold fs-5 text-primary" id="displayTotal">Rp 0</span>
                            <input type="hidden" name="total" id="inputTotal" value="0">
                            <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold">Pelanggan</label>
                            <select name="customer_id" class="form-select form-select-sm">
                                <option value="">Umum / Non-Member</option>
                                @if(isset($customers))
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? '-' }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Metode Bayar</label>
                                <select name="payment_method" id="paymentMethodSelect" class="form-select form-select-sm">
                                    <option value="tunai">TUNAI</option>
                                    <option value="qris">QRIS / Transfer</option>
                                    <option value="debit">Kartu Debit</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-bold">Diskon (Rp)</label>
                                <input type="number" name="discount" id="inputDiscount" class="form-control form-control-sm" value="0" min="0">
                            </div>
                        </div>

                        <!-- Display Poster QRIS TOKO WD (Memanggil public/images/qris.jpeg & public/qris.jpeg) -->
                        <div id="qrisContainer" class="my-3 text-center p-3 border rounded bg-white shadow-sm" style="display: none;">
                            <h6 class="fw-bold text-dark mb-2">Scan QRIS TOKO WD</h6>
                            <img src="{{ asset('images/qris.jpeg') }}" 
                                 alt="Scan QRIS TOKO WD" 
                                 class="img-fluid rounded border shadow-sm mb-2" 
                                 style="max-width: 250px; width: 100%; height: auto;"
                                 onerror="this.onerror=null; this.src='{{ asset('qris.jpeg') }}';">
                            <small class="text-success fw-bold d-block mt-1">✓ Pastikan pembayaran berhasil sebelum memproses transaksi</small>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold">Uang Diterima (Bayar)</label>
                            <input type="number" name="pay_amount" id="inputPay" class="form-control form-control-lg fw-bold text-success" placeholder="0" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                            <span class="fw-bold small">Kembalian:</span>
                            <span class="fw-bold fs-6 text-dark" id="displayChange">Rp 0</span>
                            <input type="hidden" name="change_amount" id="inputChange" value="0">
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold" id="btnSubmit" disabled>
                            <i class="bi bi-check-circle me-1"></i> Selesaikan Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live Clock
    setInterval(() => {
        const liveClock = document.getElementById('liveClock');
        if (liveClock) {
            const now = new Date();
            liveClock.innerText = now.toLocaleTimeString('id-ID');
        }
    }, 1000);

    // Event Listener Pemunculan QRIS
    const payMethodSelect = document.getElementById('paymentMethodSelect') || document.querySelector('select[name="payment_method"]');
    const qrisBox = document.getElementById('qrisContainer');

    function checkPaymentMethod() {
        if (!payMethodSelect || !qrisBox) return;
        const val = payMethodSelect.value.toLowerCase();
        const text = payMethodSelect.options[payMethodSelect.selectedIndex] ? payMethodSelect.options[payMethodSelect.selectedIndex].text.toLowerCase() : '';
        
        if (val.includes('qris') || text.includes('qris')) {
            qrisBox.style.display = 'block';
        } else {
            qrisBox.style.display = 'none';
        }
    }

    if (payMethodSelect) {
        payMethodSelect.addEventListener('change', checkPaymentMethod);
        checkPaymentMethod();
    }
});

let cart = {};

// Filter Produk Search
document.getElementById('searchProduct').addEventListener('input', function(e) {
    let query = e.target.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        let name = card.getAttribute('data-name');
        let sku = card.getAttribute('data-sku');
        if (name.includes(query) || sku.includes(query)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Klik Produk ke Keranjang
document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        let id = this.getAttribute('data-id');
        let name = this.getAttribute('data-name');
        let price = parseInt(this.getAttribute('data-price'));
        let maxStock = parseInt(this.getAttribute('data-stock'));

        if (cart[id]) {
            if (cart[id].qty < maxStock) {
                cart[id].qty++;
            } else {
                alert('Jumlah melebihi stok yang tersedia!');
            }
        } else {
            cart[id] = { id: id, name: name, price: price, qty: 1, maxStock: maxStock };
        }
        renderCart();
    });
});

// Render Keranjang & Hitung Total
function renderCart() {
    let cartBody = document.getElementById('cartBody');
    cartBody.innerHTML = '';
    let keys = Object.keys(cart);

    if (keys.length === 0) {
        cartBody.innerHTML = `<tr id="emptyCartRow"><td colspan="4" class="text-center py-4 text-muted small">Keranjang masih kosong. Klik produk di sebelah kiri.</td></tr>`;
        updateCalculations(0);
        return;
    }

    let subtotalSum = 0;
    keys.forEach((id, index) => {
        let item = cart[id];
        let itemSubtotal = item.qty * item.price;
        subtotalSum += itemSubtotal;

        let tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <span class="fw-bold d-block text-truncate" style="max-width: 130px;" title="${item.name}">${item.name}</span>
                <small class="text-muted">@ Rp ${item.price.toLocaleString('id-ID')}</small>
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][price]" value="${item.price}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
                <input type="hidden" name="items[${index}][subtotal]" value="${itemSubtotal}">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-1" onclick="changeQty(${id}, -1)">-</button>
                    <input type="text" class="form-control text-center px-1" value="${item.qty}" readonly>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-1" onclick="changeQty(${id}, 1)">+</button>
                </div>
            </td>
            <td class="text-end fw-bold">Rp ${itemSubtotal.toLocaleString('id-ID')}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(${id})"><i class="bi bi-x-circle"></i></button>
            </td>
        `;
        cartBody.appendChild(tr);
    });

    updateCalculations(subtotalSum);
}

function changeQty(id, delta) {
    if (cart[id]) {
        let newQty = cart[id].qty + delta;
        if (newQty > cart[id].maxStock) {
            alert('Jumlah melebihi stok yang tersedia!');
            return;
        }
        if (newQty <= 0) {
            delete cart[id];
        } else {
            cart[id].qty = newQty;
        }
        renderCart();
    }
}

function removeItem(id) {
    delete cart[id];
    renderCart();
}

function updateCalculations(subtotal) {
    let discount = parseInt(document.getElementById('inputDiscount').value) || 0;
    let total = Math.max(0, subtotal - discount);

    document.getElementById('inputSubtotal').value = subtotal;
    document.getElementById('inputTotal').value = total;
    document.getElementById('displayTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');

    calculateChange();
}

document.getElementById('inputDiscount').addEventListener('input', function() {
    let subtotal = parseInt(document.getElementById('inputSubtotal').value) || 0;
    updateCalculations(subtotal);
});

document.getElementById('inputPay').addEventListener('input', calculateChange);

function calculateChange() {
    let total = parseInt(document.getElementById('inputTotal').value) || 0;
    let pay = parseInt(document.getElementById('inputPay').value) || 0;
    let btnSubmit = document.getElementById('btnSubmit');

    let change = pay - total;
    if (change >= 0 && total > 0 && pay >= total) {
        document.getElementById('displayChange').innerText = 'Rp ' + change.toLocaleString('id-ID');
        document.getElementById('inputChange').value = change;
        btnSubmit.disabled = false;
    } else {
        document.getElementById('displayChange').innerText = 'Rp 0 (Kurang)';
        document.getElementById('inputChange').value = 0;
        btnSubmit.disabled = true;
    }
}
</script>
@endsection