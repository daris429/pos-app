<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->index();
    }

    public function index()
    {
        // Tangkap tanggal hari ini dalam format YYYY-MM-DD
        $todayString = date('Y-m-d');

        // 1. Total Penjualan Hari Ini (Pakai LIKE tanggal agar tembus semua timezone)
        $penjualanHariIni = Sale::where('created_at', 'LIKE', $todayString . '%')
            ->orWhere('sale_date', 'LIKE', $todayString . '%')
            ->sum('total') ?? 0;

        // 2. Total Transaksi Hari Ini
        $transaksiHariIni = Sale::where('created_at', 'LIKE', $todayString . '%')
            ->orWhere('sale_date', 'LIKE', $todayString . '%')
            ->count() ?? 0;

        // 3. Stok Menipis (Stok <= 5)
        $stokMenipis = Product::where('stock', '<=', 5)->count() ?? 0;

        // 4. Total Produk Active (Ambil total semua produk tanpa filter)
        $totalProduk = Product::count() ?? 0;

        // 5. Transaksi Terakhir
        $latestSales = Sale::latest()->take(5)->get();

        return view('dashboard', [
            'penjualanHariIni'   => $penjualanHariIni,
            'transaksiHariIni'   => $transaksiHariIni,
            'stokMenipis'        => $stokMenipis,
            'totalProduk'        => $totalProduk,
            'totalProdukActive'  => $totalProduk, // Variabel cadangan
            'latestSales'        => $latestSales,
            'recentSales'        => $latestSales,  // Variabel cadangan
        ]);
    }
}