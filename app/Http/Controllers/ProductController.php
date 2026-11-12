<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Bersihkan koma dari input harga
        $request->merge([
            'purchase_price' => str_replace(',', '.', $request->purchase_price),
            'selling_price'  => str_replace(',', '.', $request->selling_price),
        ]);

        $request->validate([
            'sku'            => 'required|string|unique:products,sku',
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'unit'           => 'required|string',
            'purchase_price' => 'required|numeric',
            'selling_price'  => 'required|numeric',
            'stock'          => 'required|integer',
            'min_stock'      => 'required|integer',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('image');
        $data['purchase_price'] = (float) $request->purchase_price;
        $data['selling_price']  = (float) $request->selling_price;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // Bersihkan koma dari input harga
        $request->merge([
            'purchase_price' => str_replace(',', '.', $request->purchase_price),
            'selling_price'  => str_replace(',', '.', $request->selling_price),
        ]);

        $request->validate([
            'sku'            => 'required|string|unique:products,sku,' . $product->id,
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'unit'           => 'required|string',
            'purchase_price' => 'required|numeric',
            'selling_price'  => 'required|numeric',
            'stock'          => 'required|integer',
            'min_stock'      => 'required|integer',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('image');
        $data['purchase_price'] = (float) $request->purchase_price;
        $data['selling_price']  = (float) $request->selling_price;

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }
}