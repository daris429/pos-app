<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user'])->latest()->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products  = Product::all();
        $refNo     = 'PO-' . date('YmdHis');

        return view('purchases.create', compact('suppliers', 'products', 'refNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_no'  => 'required|unique:purchases,reference_no',
            'supplier_id'   => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'products'      => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty'=> 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            $purchase = Purchase::create([
                'reference_no'  => $request->reference_no,
                'supplier_id'   => $request->supplier_id,
                'user_id'       => Auth::id(),
                'purchase_date' => $request->purchase_date,
                'total_amount'  => 0,
                'notes'         => $request->notes,
            ]);

            foreach ($request->products as $item) {
                $subtotal = $item['qty'] * $item['price'];
                $totalAmount += $subtotal;

                PurchaseItem::create([
                    'purchase_id'   => $purchase->id,
                    'product_id'    => $item['id'],
                    'quantity'      => $item['qty'],
                    'purchase_price'=> $item['price'],
                    'subtotal'      => $subtotal,
                ]);

                // OTOMATIS TAMBAH STOK PRODUK DI DATABASE
                $product = Product::findOrFail($item['id']);
                $product->increment('stock', $item['qty']);
                
                // Opsional: Update harga beli terakhir jika ada perubahan
                $product->update(['purchase_price' => $item['price']]);
            }

            $purchase->update(['total_amount' => $totalAmount]);

            LogHelper::log('Stok Masuk', "Menambah stok masuk no invoice {$purchase->reference_no}");

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Transaksi Stok Masuk berhasil disimpan dan stok otomatis bertambah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'items.product'])->findOrFail($id);
        return view('purchases.show', compact('purchase'));
    }
}