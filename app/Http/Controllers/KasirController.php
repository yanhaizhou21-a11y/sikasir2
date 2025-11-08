<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'subcategory')->get();
        $categories = Category::with(['subcategories', 'products'])->get();

        // Build mapping Category => Subcategories for frontend
        $subcategoryMap = $categories->mapWithKeys(function ($cat) {
            return [
                $cat->name => $cat->subcategories->pluck('name')->values(),
            ];
        });

        return view('kasir.index', [
            'products' => $products,
            'categories' => $categories,
            'subcategoryMap' => $subcategoryMap,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
        ]);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'total_price' => $request->total_price,
        ]);

        foreach ($request->items as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => Product::find($item['product_id'])->price,
            ]);
        }

        return response()->json(['message' => 'Transaksi berhasil!', 'id' => $transaction->id]);
    }
    
    public function show($id)
    {
        $transaction = Transaction::with('items.product')->findOrFail($id);
        return view('kasir.show', compact('transaction'));
    }
}