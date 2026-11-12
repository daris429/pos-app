<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Warung Daris') }}</title>
    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #1e293b; color: #fff; width: 250px; }
        .sidebar .brand { padding: 20px 15px; border-bottom: 1px solid #334155; }
        .sidebar .nav-link { color: #94a3b8; padding: 12px 20px; font-weight: 500; border-radius: 8px; margin: 4px 10px; }
        .sidebar .nav-link:hover { color: #fff; background-color: #334155; }
        .sidebar .nav-link.active { color: #fff; background-color: #2563eb; }
        .sidebar .sidebar-heading { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700; padding: 15px 20px 5px; }
    </style>
</head>
<body>
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

        <!-- Profil User & Logout -->
        <div class="p-3 border-top border-secondary">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; min-width: 35px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
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
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>