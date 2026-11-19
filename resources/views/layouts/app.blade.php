<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Warung Daris') }}</title>
    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #1e293b; color: #fff; width: 250px; }
        .sidebar .brand { padding: 20px 15px; border-bottom: 1px solid #334155; }
        .sidebar .nav-link { color: #94a3b8; padding: 12px 20px; font-weight: 500; border-radius: 8px; margin: 4px 10px; }
        .sidebar .nav-link:hover { color: #fff; background-color: #334155; }
        .sidebar .nav-link.active { color: #fff; background-color: #2563eb; }
        .sidebar .sidebar-heading { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700; padding: 15px 20px 5px; }

        /* Hilangkan panah up/down pada semua input number */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Custom SweetAlert2 Styling */
        .swal2-popup.custom-swal-popup {
            border-radius: 16px !important;
            padding: 1.75rem !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35) !important;
        }
        .swal2-backdrop-show {
            backdrop-filter: blur(4px);
        }

        /* Global Loading Page Overlay */
        #globalLoadingOverlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(5px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        #globalLoadingOverlay.show {
            display: flex;
        }
        .loading-box {
            background: #ffffff;
            color: #0f172a;
            padding: 2rem 2.5rem;
            border-radius: 18px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
            text-align: center;
            min-width: 260px;
            animation: popIn 0.2s ease-out;
        }
        [data-bs-theme="dark"] .loading-box {
            background: #1e293b;
            color: #f8fafc;
            border: 1px solid #334155;
        }
        @keyframes popIn {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Mode Gelap untuk seluruh halaman */
        [data-bs-theme="dark"] body {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }
        [data-bs-theme="dark"] .card,
        [data-bs-theme="dark"] .modal-content,
        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .bg-light,
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #f8fafc !important;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #334155 !important;
        }
        [data-bs-theme="dark"] .table {
            color: #f8fafc !important;
        }
        [data-bs-theme="dark"] .table th,
        [data-bs-theme="dark"] .table td {
            border-color: #334155 !important;
        }
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        [data-bs-theme="dark"] .text-dark {
            color: #f8fafc !important;
        }
        [data-bs-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>

<!-- Global Loading Overlay -->
<div id="globalLoadingOverlay">
    <div class="loading-box">
        <div class="spinner-border text-primary mb-3" style="width: 3.2rem; height: 3.2rem; border-width: 0.3em;" role="status"></div>
        <h6 class="fw-bold mb-1" id="loadingTitle">Menyimpan Data...</h6>
        <small class="text-muted">Mohon tunggu sebentar, sedang diproses</small>
    </div>
</div>

<div class="d-flex">
    <!-- SIDEBAR NAVIGASI -->
    <div class="sidebar d-flex flex-column justify-content-between flex-shrink-0 p-0">
        <div>
          <!-- Header Brand -->
            <div class="brand d-flex align-items-center gap-2">
                <div class="bg-primary text-white rounded p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-shop fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0 text-white">{{ config('app.name', 'Warung Daris') }}</h6>
                    <small class="text-secondary" style="font-size: 0.75rem;">Store Management</small>
                </div>
            </div>

            <!-- MENU UTAMA -->
            <ul class="nav nav-pills flex-column mb-auto pt-3">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sales.kasir') }}" class="nav-link {{ request()->routeIs('sales.kasir', 'sales.create') ? 'active' : '' }}">
                        <i class="bi bi-cart me-2"></i> Kasir / Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.index', 'sales.show') ? 'active' : '' }}">
                        <i class="bi bi-receipt me-2"></i> Riwayat Transaksi
                    </a>
                </li>

                <!-- MENU KHUSUS ADMIN -->
                @if(strtolower(auth()->user()->role) === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <i class="bi bi-people me-2"></i> Pelanggan
                        </a>
                    </li>

                    <div class="sidebar-heading">ADMINISTRASI</div>

                    <li class="nav-item">
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tag me-2"></i> Kategori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="bi bi-box-seam me-2"></i> Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            <i class="bi bi-truck me-2"></i> Supplier
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                            <i class="bi bi-download me-2"></i> Stok Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line me-2"></i> Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-person-gear me-2"></i> Manajemen User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                            <i class="bi bi-clock-history me-2"></i> Log Aktivitas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="bi bi-gear me-2"></i> Pengaturan
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Profil User & Logout di Bawah Sidebar -->
        <div class="p-3 border-top border-secondary">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle border border-secondary" style="width: 36px; height: 36px; min-width: 36px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; min-width: 36px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    @endif
                    <div class="text-truncate">
                        <div class="fw-bold small text-white text-truncate">{{ auth()->user()->name }}</div>
                        <small class="text-uppercase badge {{ strtolower(auth()->user()->role) === 'admin' ? 'bg-danger' : 'bg-info' }}" style="font-size: 0.65rem;">
                            {{ auth()->user()->role }}
                        </small>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light border-0" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-grow-1 p-4 overflow-auto" style="height: 100vh;">
        <!-- TOP BAR KANAN ATAS: TEMA & PROFIL PENGGUNA -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            @if(!request()->routeIs('dashboard'))
                <button type="button" id="themeToggleBtn"
                        class="btn bg-white border-0 shadow-sm d-flex align-items-center gap-2 px-3 py-2"
                        style="border-radius: 10px; height: 42px;"
                        title="Ganti Tema Terang / Gelap">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                    <span id="themeText" class="small fw-semibold">Tema Gelap</span>
                </button>
            @endif

            <!-- Dropdown Profil Kanan Atas -->
            <div class="dropdown">
                <button class="btn bg-white border-0 shadow-sm d-flex align-items-center gap-2 px-2 py-1"
                        type="button"
                        id="topProfileDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="border-radius: 12px; height: 42px;">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                             alt="{{ auth()->user()->name }}"
                             class="rounded-circle border"
                             style="width: 32px; height: 32px; object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                             style="width: 32px; height: 32px; font-size: 0.85rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-start d-none d-sm-block pe-1" style="line-height: 1.1;">
                        <div class="fw-bold small text-dark">{{ auth()->user()->name }}</div>
                        <small class="text-muted" style="font-size: 0.68rem;">{{ ucfirst(auth()->user()->role) }}</small>
                    </div>
                    <i class="bi bi-chevron-down small text-muted pe-1"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="topProfileDropdown" style="min-width: 220px; border-radius: 12px;">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-bold small">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->email }}</div>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-person-circle text-primary"></i> Edit Profil & Foto
                        </button>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        @yield('content')
    </div>
</div>

<!-- MODAL EDIT PROFIL & UPLOAD FOTO -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Edit Profil Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Preview Foto Profil Saat Ini -->
                    <div class="text-center mb-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 alt="Foto Profil"
                                 class="rounded-circle shadow-sm border"
                                 style="width: 90px; height: 90px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm"
                                 style="width: 90px; height: 90px; font-size: 2rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Ganti Foto Profil</label>
                        <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                        <div class="form-text">Format JPG, PNG, WEBP (Maks. 2MB).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                    </div>

                    <hr>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Password Baru <span class="text-muted fw-normal">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Helper Global untuk Menampilkan / Menyembunyikan Loading Page
    window.showPageLoading = function (title = 'Menyimpan Data...') {
        const overlay = document.getElementById('globalLoadingOverlay');
        const titleEl = document.getElementById('loadingTitle');
        if (titleEl) titleEl.textContent = title;
        if (overlay) overlay.classList.add('show');
    };

    window.hidePageLoading = function () {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) overlay.classList.remove('show');
    };

    window.addEventListener('pageshow', function () {
        window.hidePageLoading();
    });

    // Ubah otomatis semua alert() bawaan browser menjadi Custom SweetAlert2
    window.alert = function (message) {
        window.hidePageLoading();
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        Swal.fire({
            title: 'Perhatian',
            text: message,
            icon: 'warning',
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Mengerti',
            background: isDark ? '#1e293b' : '#ffffff',
            color: isDark ? '#f8fafc' : '#0f172a',
            customClass: {
                popup: 'custom-swal-popup border ' + (isDark ? 'border-secondary' : 'border-0')
            }
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        // 1. Toggle Tema Terang / Gelap
        const toggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');
        const themeText = document.getElementById('themeText');

        function updateButtonUI(theme) {
            if (!toggleBtn) return;
            if (theme === 'dark') {
                toggleBtn.className = 'btn bg-white border-0 shadow-sm d-flex align-items-center gap-2 px-3 py-2';
                if (themeIcon) themeIcon.className = 'bi bi-sun-fill text-warning';
                if (themeText) themeText.textContent = 'Tema Terang';
            } else {
                toggleBtn.className = 'btn bg-white border-0 shadow-sm d-flex align-items-center gap-2 px-3 py-2';
                if (themeIcon) themeIcon.className = 'bi bi-moon-stars-fill text-dark';
                if (themeText) themeText.textContent = 'Tema Gelap';
            }
        }

        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        updateButtonUI(currentTheme);

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const activeTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = activeTheme === 'dark' ? 'light' : 'dark';

                document.documentElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateButtonUI(newTheme);
            });
        }

        // 2. Preview Otomatis Setiap Upload Foto
        document.addEventListener('change', function (e) {
            if (e.target && e.target.type === 'file') {
                const input = e.target;
                const parent = input.parentElement;
                let previewBox = parent.querySelector('.auto-image-preview-box');

                if (!previewBox) {
                    previewBox = document.createElement('div');
                    previewBox.className = 'auto-image-preview-box mt-2';
                    previewBox.innerHTML = '<img src="" alt="Preview Foto" class="img-thumbnail" style="max-height: 150px; object-fit: cover;">';
                    parent.appendChild(previewBox);
                }

                const img = previewBox.querySelector('img');
                if (input.files && input.files[0] && input.files[0].type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        img.src = ev.target.result;
                        previewBox.style.display = 'block';
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    img.src = '';
                    previewBox.style.display = 'none';
                }
            }
        });

        // 3. Ubah Otomatis Semua Dialog confirm() Bawaan Browser Jadi Custom SweetAlert2
        document.querySelectorAll('form[onsubmit*="confirm"]').forEach(function (form) {
            const rawAttr = form.getAttribute('onsubmit') || '';
            const match = rawAttr.match(/confirm\((['"`])(.*?)\1\)/);
            const message = match ? match[2] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

            form.removeAttribute('onsubmit');
            form.dataset.hasCustomConfirm = 'true';

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

                Swal.fire({
                    title: 'Konfirmasi Tindakan',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: isDark ? '#334155' : '#64748b',
                    confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    background: isDark ? '#1e293b' : '#ffffff',
                    color: isDark ? '#f8fafc' : '#0f172a',
                    customClass: {
                        popup: 'custom-swal-popup border ' + (isDark ? 'border-secondary' : 'border-0')
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.showPageLoading('Memproses Data...');
                        form.submit();
                    }
                });
            });
        });

        // 4. Tampilkan Loading Page Otomatis Setiap Form Disimpan / Disubmit
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || form.dataset.hasCustomConfirm === 'true') return;

            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            if (method === 'GET') return;
            if (e.defaultPrevented) return;

            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                if (submitBtn.tagName === 'BUTTON') {
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
                }
            }

            window.showPageLoading('Menyimpan Data...');
        });

        // 5. Notifikasi Toast Modern Otomatis untuk Flash Session (Sukses / Error)
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#1e293b' : '#ffffff',
                color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#0f172a'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: @json(session('error')),
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#1e293b' : '#ffffff',
                color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8fafc' : '#0f172a'
            });
        @endif
    });
</script>
@stack('scripts')
</body>
</html>