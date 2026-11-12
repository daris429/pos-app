<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Menampilkan Riwayat Transaksi Penjualan
     */
    public function index()
    {
        $sales = Sale::with(['customer', 'user'])->latest()->paginate(10);
        return view('sales.index', compact('sales'));
    }

    /**
     * Menampilkan Halaman Transaksi Kasir
     */
    public function create()
    {
        $products  = Product::where('stock', '>', 0)->get();
        $customers = Customer::all();

        return view('sales.create', compact('products', 'customers'));
    }

    /**
     * Memproses & Menyimpan Transaksi Penjualan Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'total'              => 'required|numeric|min:1',
            'pay_amount'         => 'required|numeric|min:' . $request->total,
            'payment_method'     => 'required|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric',
            'items.*.subtotal'   => 'required|numeric',
        ], [
            'pay_amount.min' => 'Jumlah pembayaran kurang dari total belanja.',
            'items.required' => 'Keranjang belanja masih kosong.',
        ]);

        DB::beginTransaction();
        try {
            $invoiceNo = 'INV-' . date('YmdHis');

            // 1. Simpan Header Transaksi Penjualan
            $sale = Sale::create([
                'invoice_no'     => $invoiceNo,
                'customer_id'    => $request->customer_id ?: null,
                'user_id'        => Auth::id(),
                'sale_date'      => now(),
                'subtotal'       => $request->subtotal ?? $request->total,
                'discount'       => $request->discount ?? 0,
                'total'          => $request->total,
                'pay_amount'     => $request->pay_amount,
                'change_amount'  => $request->change_amount ?? ($request->pay_amount - $request->total),
                'payment_method' => $request->payment_method,
                'status'         => 'PAID',
            ]);

            // 2. Simpan Detail Item Penjualan & Potong Stok Produk
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return back()->with('error', 'Stok produk ' . $product->name . ' tidak mencukupi!')->withInput();
                }

                SaleItem::create([
                    'sale_id'        => $sale->id,
                    'product_id'     => $item['product_id'],
                    'price'          => $item['price'],
                    'purchase_price' => $product->purchase_price ?? $product->buy_price ?? 0,
                    'quantity'       => $item['quantity'],
                    'subtotal'       => $item['subtotal'],
                ]);

                // Potong Stok Produk
                $product->decrement('stock', $item['quantity']);
            }

            // 3. Catat Aktivitas Transaksi Penjualan
            if (class_exists('App\Helpers\LogHelper')) {
                LogHelper::log('Transaksi Kasir', "Transaksi penjualan baru #{$invoiceNo} sebesar Rp " . number_format($sale->total, 0, ',', '.'));
            }

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                             ->with('success', "Transaksi #{$invoiceNo} berhasil diproses!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan Detail Transaksi Penjualan
     */
    public function show($id)
    {
        $sale = Sale::with(['customer', 'user', 'items.product'])->findOrFail($id);
        return view('sales.show', compact('sale'));
    }

    /**
     * Cetak Struk Transaksi
     */
    public function receipt($id)
    {
        $sale = Sale::with(['customer', 'user', 'items.product'])->findOrFail($id);
        return view('sales.receipt', compact('sale'));
    }

    /**
     * Membatalkan Transaksi (Void) - KHUSUS ROLE ADMIN ONLY
     */
    public function cancel($id)
    {
        // Guard Backend: Cek jika bukan admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang memiliki akses untuk membatalkan transaksi.');
        }

        DB::beginTransaction();
        try {
            $sale = Sale::with('items.product')->findOrFail($id);

            // Cek jika sudah dibatalkan sebelumnya
            if ($sale->status === 'CANCELLED' || $sale->status === 'VOID') {
                return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya!');
            }

            // 1. Kembalikan stok produk
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // 2. Ubah status transaksi menjadi CANCELLED
            $sale->update(['status' => 'CANCELLED']);

            // 3. Catat di Log Aktivitas
            if (class_exists('App\Helpers\LogHelper')) {
                LogHelper::log('Pembatalan Transaksi', "Membatalkan transaksi invoice #{$sale->invoice_no} senilai Rp " . number_format($sale->total, 0, ',', '.'));
            }

            DB::commit();
            return back()->with('success', "Transaksi #{$sale->invoice_no} berhasil dibatalkan dan stok produk telah dikembalikan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}