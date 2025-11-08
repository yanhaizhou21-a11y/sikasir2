<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')->latest()->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $products = Product::all();
        return view('transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris,debit'
        ]);

        $total = 0;
        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            $total += $product->harga_jual * $item['qty'];
        }

        $status = $request->payment_method === 'cash' ? 'paid' : 'unpaid';

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'invoice' => 'INV-' . strtoupper(Str::random(8)),
            'total' => $total,
            'payment_method' => $request->payment_method,
            'transaction_time' => now(),
            'status' => $status
        ]);

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'harga_jual' => $product->harga_jual,
                'subtotal' => $product->harga_jual * $item['qty']
            ]);
        }

        // Create orders and route them based on product categories
        $this->routeOrders($transaction);

        // Auto-generate receipt for cash or immediately successful methods
        if ($transaction->status === 'paid') {
            $this->generateReceipt($transaction);
        } else {
            // For QRIS: generate QR code for display
            if ($transaction->payment_method === 'qris') {
                $this->generateQris($transaction);
            }
        }

        // Redirect based on route context
        if (request()->routeIs('kasir.transaksi.*')) {
            return redirect()->route('kasir.dashboard')->with('success', 'Transaksi berhasil disimpan.');
        }
        
        return redirect()->route('admin.transactions.index')->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Route orders to Kitchen or Bar based on product categories
     */
    private function routeOrders(Transaction $transaction)
    {
        $transaction->load('items.product.category');
        
        $foodItems = [];
        $drinkItems = [];
        
        foreach ($transaction->items as $item) {
            $product = $item->product;
            $categoryName = strtolower($product->category->name ?? '');
            
            if (in_array($categoryName, ['makanan', 'food', 'dessert'])) {
                $foodItems[] = $item;
            } elseif (in_array($categoryName, ['minuman', 'drink', 'beverage'])) {
                $drinkItems[] = $item;
            }
        }

        // Create order for Kitchen if there are food items
        if (!empty($foodItems)) {
            $order = \App\Models\Order::create([
                'transaction_id' => $transaction->id,
                'order_number' => 'ORD-K-' . strtoupper(Str::random(8)),
                'order_type' => 'food',
                'status' => 'pending',
                'destination' => 'kitchen',
            ]);
            event(new \App\Events\OrderCreated($order));
        }

        // Create order for Bar if there are drink items
        if (!empty($drinkItems)) {
            $order = \App\Models\Order::create([
                'transaction_id' => $transaction->id,
                'order_number' => 'ORD-B-' . strtoupper(Str::random(8)),
                'order_type' => 'drink',
                'status' => 'pending',
                'destination' => 'bar',
            ]);
            event(new \App\Events\OrderCreated($order));
        }
    }

    /**
     * Generate and store HTML receipt file for a transaction
     */
    private function generateReceipt(Transaction $transaction): void
    {
        $transaction->loadMissing('items.product', 'user');

        $html = view('transactions.receipt', [
            'transaction' => $transaction,
        ])->render();

        $relativeDir = 'receipts';
        $filename = strtolower($transaction->invoice) . '.html';
        $fullPath = storage_path('app/public/' . $relativeDir);
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        file_put_contents($fullPath . '/' . $filename, $html);

        // Save or update receipt record
        $transaction->receipt()->updateOrCreate([], [
            'file_path' => 'storage/' . $relativeDir . '/' . $filename,
            'type' => 'html',
        ]);
    }

    /**
     * Generate QR code file for QRIS payments
     */
    private function generateQris(Transaction $transaction): void
    {
        // For demo, encode invoice and total; integrate with real QRIS provider as needed
        $payload = json_encode([
            'invoice' => $transaction->invoice,
            'amount' => $transaction->total,
        ]);

        $dir = public_path('qrcodes');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . '/' . strtolower($transaction->invoice) . '.svg';
        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(240)->generate($payload, $path);
    }

    /**
     * Confirm non-cash payment and generate receipt
     */
    public function confirmPayment(Transaction $transaction)
    {
        if ($transaction->status === 'paid') {
            return back()->with('info', 'Transaksi sudah lunas.');
        }

        $transaction->update(['status' => 'paid', 'transaction_time' => now()]);
        $this->generateReceipt($transaction);

        return back()->with('success', 'Pembayaran dikonfirmasi dan struk dibuat.');
    }

    /**
     * View receipt (HTML) in browser
     */
    public function receipt(Transaction $transaction)
    {
        if (!$transaction->receipt) {
            $this->generateReceipt($transaction);
            $transaction->refresh();
        }

        return redirect(asset($transaction->receipt->file_path));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('items.product', 'user');
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        abort(404); // Edit transaksi biasanya tidak diperlukan
    }

    public function update(Request $request, Transaction $transaction)
    {
        abort(404); // Update transaksi biasanya tidak dilakukan
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('admin.transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}