<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'subcategory', 'ingredients'])->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $ingredients = Ingredient::all();
        return view('products.create', compact('categories', 'subcategories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'barcode'        => 'required|string|unique:products,barcode',
            'harga_modal'    => 'required|numeric|min:0',
            'harga_jual'     => 'required|numeric|min:0',
            'category_id'    => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Validate that harga_jual is greater than or equal to harga_modal
        if ($request->harga_jual < $request->harga_modal) {
            return back()->withErrors(['harga_jual' => 'Harga jual harus lebih besar atau sama dengan harga modal.'])->withInput();
        }

        $data = $request->only([
            'name',
            'description',
            'barcode',
            'harga_modal',
            'harga_jual',
            'category_id',
            'subcategory_id',
        ]);

        if ($request->hasFile('image')) {
            $filename = $request->file('image')->store('products', 'public');
            $data['image'] = $filename;
        }

        $product = Product::create($data);

        // Simpan bahan baku
        if ($request->has('ingredients')) {
            $syncData = [];
            foreach ($request->ingredients as $id => $data) {
                if (isset($data['selected']) && $data['selected'] == 1) {
                    $syncData[$id] = ['quantity' => $data['quantity'] ?? 0];
                }
            }
            $product->ingredients()->sync($syncData);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $ingredients = \App\Models\Ingredient::all();

        return view('products.edit', compact('product', 'categories', 'subcategories', 'ingredients'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'barcode'        => 'required|string|unique:products,barcode,' . $product->id,
            'harga_modal'    => 'required|numeric|min:0',
            'harga_jual'     => 'required|numeric|min:0',
            'category_id'    => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Validate that harga_jual is greater than or equal to harga_modal
        if ($request->harga_jual < $request->harga_modal) {
            return back()->withErrors(['harga_jual' => 'Harga jual harus lebih besar atau sama dengan harga modal.'])->withInput();
        }

        $data = $request->only([
            'name',
            'description',
            'barcode',
            'harga_modal',
            'harga_jual',
            'category_id',
            'subcategory_id',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $filename = $request->file('image')->store('products', 'public');
            $data['image'] = $filename;
        }

        $product->update($data);

        // Update bahan baku
        $syncData = [];
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $id => $data) {
                if (isset($data['selected']) && $data['selected'] == 1) {
                    $syncData[$id] = ['quantity' => $data['quantity'] ?? 0];
                }
            }
        }
        $product->ingredients()->sync($syncData);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
