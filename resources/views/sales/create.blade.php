@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <!-- Header Kasir -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold m-0">Kasir / Transaksi Penjualan</h3>
            <small class="text-muted">Pilih produk dan lakukan proses transaksi dengan mudah</small>
        </div>
        <div class="bg-primary text-white px-3 py-2 rounded-3 shadow-sm fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-clock"></i>
            <span id="liveClock">{{ now()->format('H.i.s') }}</span>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3">
        <!-- KOLOM KIRI: PENCARIAN & DAFTAR PRODUK -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <!-- Kolom Pencarian -->
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="searchProduct" class="form-control border-start-0" placeholder="Cari nama atau SKU produk..." autofocus>
                    </div>

                    <!-- Grid Produk -->
                    <div class="row g-2 overflow-auto" id="productGrid" style="max-height: 68vh;">
                        @forelse($products as $product)
                            <div class="col-6 col-md-4 product-item" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku ?? '') }}">
                                <div class="card h-100 border shadow-sm text-center p-2 product-card"
                                     style="cursor: pointer; transition: transform 0.15s;"
                                     onclick="addToCart({{ $product->id }}, @js($product->name), {{ $product->selling_price ?? $product->price ?? 0 }}, {{ $product->stock }})">
                                    <div class="d-flex justify-content-center align-items-center mb-2" style="height: 65px;">
                                        @if(!empty($product->image))
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-height: 60px; max-width: 100%; object-fit: contain;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-seam fs-4"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="fw-bold small text-truncate" title="{{ $product->name }}">{{ $product->name }}</div>
                                    <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">
                                        Stok: {{ $product->stock }} {{ $product->unit ?? 'pcs' }}
                                    </small>
                                    <button type="button" class="btn btn-primary btn-sm w-100 fw-semibold mt-auto">
                                        Rp {{ number_format($product->selling_price ?? $product->price ?? 0, 0, ',', '.') }}
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">
                                Belum ada produk tersedia.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: KERANJANG BELANJA & PEMBAYARAN -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-cart3 me-1"></i> Keranjang Belanja
                    </h6>

                    <form action="{{ route('sales.store') }}" method="POST" id="checkoutForm">
                        @csrf
                        <input type="hidden" name="subtotal" id="subtotalHidden" value="0">
                        <input type="hidden" name="total" id="totalHidden" value="0">
                        <input type="hidden" name="change_amount" id="changeHidden" value="0">
                        <div id="hiddenItemsContainer"></div>

                        <!-- Tabel Keranjang -->
                        <div class="table-responsive mb-3" style="max-height: 240px; overflow-y: auto;">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center" style="width: 115px;">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                        <th style="width: 28px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4 small">Keranjang masih kosong</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <!-- Total Belanja -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold">Total Belanja:</span>
                            <h4 class="fw-bold text-primary m-0" id="grandTotalText">Rp 0</h4>
                        </div>

                        <!-- Pelanggan -->
                        <div class="mb-2">
                            <label class="form-label small fw-semibold mb-1">Pelanggan</label>
                            <select name="customer_id" class="form-select form-select-sm">
                                <option value="">Umum / Non-Member</option>
                                @foreach(($customers ?? []) as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->phone ? '(' . $customer->phone . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Metode Bayar & Diskon -->
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold mb-1">Metode Bayar</label>
                                <select name="payment_method" id="paymentMethod" class="form-select form-select-sm" onchange="handlePaymentMethodChange()">
                                    <option value="TUNAI">TUNAI</option>
                                    <option value="QRIS">QRIS / Transfer</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold mb-1">Diskon (Rp)</label>
                                <input type="number" name="discount" id="discountInput" class="form-control form-control-sm" value="0" min="0" oninput="calculatePayment()">
                            </div>
                        </div>

                        <!-- Info Singkat Mode QRIS -->
                        <div id="qrisInfoBadge" class="alert alert-info py-2 px-3 small d-none justify-content-between align-items-center mb-2">
                            <span><i class="bi bi-qr-code-scan me-1"></i> QRIS tampil via pop-up modal</span>
                            <button type="button" class="btn btn-sm btn-primary py-0 px-2" onclick="openQrisModal()">Lihat QR</button>
                        </div>

                        <!-- Uang Diterima -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold mb-1">Uang Diterima (Bayar)</label>
                            <input type="number" name="pay_amount" id="paidInput" class="form-control form-control-lg fw-bold" value="0" min="0" oninput="calculatePayment()" required>
                        </div>

                        <!-- Kembalian -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold small">Kembalian:</span>
                            <span class="fw-bold text-danger" id="changeText">Rp 0 (Kurang)</span>
                        </div>

                        <button type="button" id="submitBtn" class="btn btn-success w-100 fw-bold py-2" onclick="handleCheckoutClick()" disabled>
                            <i class="bi bi-check-circle me-1"></i> Selesaikan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL POP-UP QRIS DENGAN TIMER 1 MENIT -->
<div class="modal fade" id="qrisModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="bi bi-qr-code-scan me-2"></i> Scan QRIS TOKO WD
                </h6>
                <button type="button" class="btn-close btn-close-white" onclick="closeQrisModal()" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center p-4">
                <!-- Countdown Timer 1 Menit -->
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-warning bg-opacity-25 text-warning-emphasis fw-bold mb-2">
                    <i class="bi bi-stopwatch-fill text-danger"></i>
                    <span>Sisa Waktu: <span id="qrisCountdown" class="text-danger fs-6">01:00</span></span>
                </div>

                <!-- Nominal Tagihan -->
                <div class="mb-3">
                    <small class="text-muted d-block">Total Pembayaran</small>
                    <h3 class="fw-bold text-primary m-0" id="qrisModalTotal">Rp 0</h3>
                </div>

                <!-- Kotak Gambar QRIS (Diperbesar) -->
                <div class="position-relative d-inline-block p-2 bg-white rounded-3 shadow-sm border mb-3">
                    @php
                        $qrisSetting = class_exists(\App\Models\Setting::class) ? \App\Models\Setting::first() : null;
                        $qrisFile = $qrisSetting->qris_image ?? $qrisSetting->qris ?? null;
                        
                        if ($qrisFile) {
                            $qrisUrl = asset('storage/' . $qrisFile);
                        } elseif (file_exists(public_path('images/qris.jpg'))) {
                            $qrisUrl = asset('images/qris.jpg');
                        } elseif (file_exists(public_path('images/qris.jpeg'))) {
                            $qrisUrl = asset('images/qris.jpeg');
                        } elseif (file_exists(public_path('qris.png'))) {
                            $qrisUrl = asset('qris.png');
                        } elseif (file_exists(public_path('qris.jpg'))) {
                            $qrisUrl = asset('qris.jpg');
                        } else {
                            $qrisUrl = asset('images/qris.png');
                        }
                    @endphp

                    <img id="qrisImage"
                         src="{{ $qrisUrl }}"
                         alt="Scan QRIS TOKO WD"
                         class="img-fluid rounded"
                         style="max-height: 360px; width: auto;">

               
                    <!-- Overlay Saat Waktu Habis -->
                    <div id="qrisExpiredOverlay"
                         class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 rounded-3 d-none flex-column align-items-center justify-content-center text-white p-3">
                        <i class="bi bi-clock-history fs-1 text-warning mb-2"></i>
                        <div class="fw-bold mb-2">Waktu Pembayaran Habis</div>
                        <button type="button" class="btn btn-sm btn-warning fw-bold px-3" onclick="startQrisTimer()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang QR (1 Menit)
                        </button>
                    </div>
                </div>

                <div class="text-success small fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> Pastikan pembayaran berhasil sebelum memproses transaksi
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4 d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" onclick="closeQrisModal()">Batal</button>
                <button type="button" id="confirmQrisBtn" class="btn btn-success flex-fill fw-bold" onclick="submitQrisTransaction()">
                    <i class="bi bi-check2-circle me-1"></i> Sudah Dibayar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];
    let qrisTimerInterval = null;
    let qrisTimeLeft = 60; // 1 Menit = 60 detik

    // Jam Real-Time
    setInterval(() => {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            clockEl.textContent = `${pad(now.getHours())}.${pad(now.getMinutes())}.${pad(now.getSeconds())}`;
        }
    }, 1000);

    // Filter Pencarian Produk
    document.getElementById('searchProduct').addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const sku = item.getAttribute('data-sku') || '';
            item.style.display = (name.includes(keyword) || sku.includes(keyword)) ? '' : 'none';
        });
    });

    function formatRupiah(angka) {
        return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
    }

    function addToCart(id, name, price, stock) {
        if (stock <= 0) {
            alert('Stok produk ini sudah habis!');
            return;
        }

        const existingIndex = cart.findIndex(item => item.product_id === id);
        if (existingIndex !== -1) {
            if (cart[existingIndex].quantity + 1 > stock) {
                alert('Jumlah melebihi stok yang tersedia!');
                return;
            }
            cart[existingIndex].quantity += 1;
            cart[existingIndex].subtotal = cart[existingIndex].quantity * cart[existingIndex].price;
        } else {
            cart.push({
                product_id: id,
                name: name,
                price: Number(price),
                quantity: 1,
                subtotal: Number(price),
                stock: Number(stock)
            });
        }
        renderCart();
    }

    function changeQty(index, delta) {
        const newQty = cart[index].quantity + delta;
        if (newQty <= 0) {
            cart.splice(index, 1);
        } else if (newQty > cart[index].stock) {
            alert('Jumlah melebihi stok yang tersedia!');
            return;
        } else {
            cart[index].quantity = newQty;
            cart[index].subtotal = newQty * cart[index].price;
        }
        renderCart();
    }

    // Qty bisa diketik langsung
    function setManualQty(index, value) {
        let parsedQty = parseInt(value);
        if (isNaN(parsedQty) || parsedQty < 1) {
            parsedQty = 1;
        }
        if (parsedQty > cart[index].stock) {
            alert('Jumlah melebihi stok yang tersedia!');
            parsedQty = cart[index].stock;
        }
        cart[index].quantity = parsedQty;
        cart[index].subtotal = parsedQty * cart[index].price;
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const hiddenContainer = document.getElementById('hiddenItemsContainer');

        if (cart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4 small">Keranjang masih kosong</td></tr>';
            hiddenContainer.innerHTML = '';
            calculatePayment();
            return;
        }

        tbody.innerHTML = cart.map((item, index) => `
            <tr>
                <td>
                    <div class="fw-bold small text-truncate" style="max-width: 120px;" title="${item.name}">${item.name}</div>
                    <small class="text-muted" style="font-size: 0.72rem;">@ ${formatRupiah(item.price)}</small>
                </td>
                <td class="text-center">
                    <div class="input-group input-group-sm justify-content-center" style="width: 105px; margin: 0 auto;">
                        <button type="button" class="btn btn-outline-secondary px-2" onclick="changeQty(${index}, -1)">-</button>
                        <input type="number"
                               class="form-control text-center px-1 fw-bold"
                               value="${item.quantity}"
                               min="1"
                               max="${item.stock}"
                               onchange="setManualQty(${index}, this.value)"
                               onkeydown="if(event.key==='Enter'){ event.preventDefault(); this.blur(); }">
                        <button type="button" class="btn btn-outline-secondary px-2" onclick="changeQty(${index}, 1)">+</button>
                    </div>
                </td>
                <td class="text-end fw-bold small">${formatRupiah(item.subtotal)}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm text-danger p-0 border-0" onclick="removeItem(${index})" title="Hapus">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        // Input array sesuai validasi SaleController@store
        hiddenContainer.innerHTML = cart.map((item, index) => `
            <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            <input type="hidden" name="items[${index}][price]" value="${item.price}">
            <input type="hidden" name="items[${index}][subtotal]" value="${item.subtotal}">
        `).join('');

        calculatePayment();
    }

    function getSubtotal() {
        return cart.reduce((sum, item) => sum + item.subtotal, 0);
    }

    function getGrandTotal() {
        const subtotal = getSubtotal();
        const discount = Math.max(0, Number(document.getElementById('discountInput').value) || 0);
        return Math.max(0, subtotal - discount);
    }

    function handlePaymentMethodChange() {
        const method = document.getElementById('paymentMethod').value;
        const qrisBadge = document.getElementById('qrisInfoBadge');
        const paidInput = document.getElementById('paidInput');
        const grandTotal = getGrandTotal();

        if (method === 'QRIS') {
            qrisBadge.classList.remove('d-none');
            qrisBadge.classList.add('d-flex');
            paidInput.value = grandTotal;
            paidInput.readOnly = true;
        } else {
            qrisBadge.classList.add('d-none');
            qrisBadge.classList.remove('d-flex');
            paidInput.readOnly = false;
        }
        calculatePayment();
    }

    function calculatePayment() {
        const subtotal = getSubtotal();
        const grandTotal = getGrandTotal();
        const method = document.getElementById('paymentMethod').value;
        const paidInput = document.getElementById('paidInput');

        if (method === 'QRIS') {
            paidInput.value = grandTotal;
        }

        const paid = Math.max(0, Number(paidInput.value) || 0);
        const diff = paid - grandTotal;

        document.getElementById('subtotalHidden').value = subtotal;
        document.getElementById('totalHidden').value = grandTotal;
        document.getElementById('changeHidden').value = Math.max(0, diff);
        document.getElementById('grandTotalText').textContent = formatRupiah(grandTotal);

        const changeEl = document.getElementById('changeText');
        const submitBtn = document.getElementById('submitBtn');

        if (cart.length === 0) {
            changeEl.className = 'fw-bold text-muted';
            changeEl.textContent = 'Rp 0';
            submitBtn.disabled = true;
            return;
        }

        if (diff < 0) {
            changeEl.className = 'fw-bold text-danger';
            changeEl.textContent = `${formatRupiah(Math.abs(diff))} (Kurang)`;
            submitBtn.disabled = true;
        } else {
            changeEl.className = 'fw-bold text-success';
            changeEl.textContent = formatRupiah(diff);
            submitBtn.disabled = false;
        }
    }

    function handleCheckoutClick() {
        if (cart.length === 0) return;

        const method = document.getElementById('paymentMethod').value;
        if (method === 'QRIS') {
            openQrisModal();
        } else {
            if (typeof window.showPageLoading === 'function') {
                window.showPageLoading('Menyimpan Transaksi...');
            }
            document.getElementById('checkoutForm').submit();
        }
    }

    function openQrisModal() {
        if (cart.length === 0) {
            alert('Keranjang belanja masih kosong!');
            return;
        }

        document.getElementById('qrisModalTotal').textContent = formatRupiah(getGrandTotal());
        const modalEl = document.getElementById('qrisModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        startQrisTimer();
    }

    function startQrisTimer() {
        clearInterval(qrisTimerInterval);
        qrisTimeLeft = 60; // 1 menit

        const countdownEl = document.getElementById('qrisCountdown');
        const expiredOverlay = document.getElementById('qrisExpiredOverlay');
        const confirmBtn = document.getElementById('confirmQrisBtn');

        expiredOverlay.classList.add('d-none');
        expiredOverlay.classList.remove('d-flex');
        confirmBtn.disabled = false;
        countdownEl.textContent = '01:00';

        qrisTimerInterval = setInterval(() => {
            qrisTimeLeft--;
            const minutes = String(Math.floor(qrisTimeLeft / 60)).padStart(2, '0');
            const seconds = String(qrisTimeLeft % 60).padStart(2, '0');
            countdownEl.textContent = `${minutes}:${seconds}`;

            if (qrisTimeLeft <= 0) {
                clearInterval(qrisTimerInterval);
                countdownEl.textContent = '00:00';
                expiredOverlay.classList.remove('d-none');
                expiredOverlay.classList.add('d-flex');
                confirmBtn.disabled = true;
            }
        }, 1000);
    }

    function closeQrisModal() {
        clearInterval(qrisTimerInterval);
        const modalEl = document.getElementById('qrisModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    function submitQrisTransaction() {
        clearInterval(qrisTimerInterval);
        closeQrisModal();
        if (typeof window.showPageLoading === 'function') {
            window.showPageLoading('Memproses Pembayaran QRIS...');
        }
        document.getElementById('checkoutForm').submit();
    }
</script>
@endpush