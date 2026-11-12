<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- GUEST ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// --- AUTH ROUTES (Dapat Diakses oleh Admin maupun Kasir) ---
Route::middleware('auth')->group(function () {
    
    // Dashboard & Profil
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Transaksi Kasir
    Route::get('kasir', [SaleController::class, 'create'])->name('sales.kasir');
    Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
    
    // Riwayat, Detail & Struk Transaksi (Bisa dibuka Kasir & Admin)
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/{id}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('sales/{id}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    // Ubah Password Profil
    Route::get('profile/password', [UserController::class, 'showChangePasswordForm'])->name('users.change-password');
    Route::post('profile/password', [UserController::class, 'changePassword']);

    // --- ROUTE KHUSUS ADMIN ONLY ---
    Route::middleware('role:admin')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class);
        Route::resource('suppliers', SupplierController::class)->except('show');
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
        
        // PEMBATALAN TRANSAKSI (HANYA UNTUK ADMIN)
        Route::post('sales/{id}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

        // Laporan & Export
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'exportExcel'])->name('reports.export');
        
        // Log Aktivitas
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // User Management & Settings
        Route::resource('users', UserController::class)->except('show');
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::match(['put', 'post'], 'settings', [SettingController::class, 'update'])->name('settings.update');
    });
});