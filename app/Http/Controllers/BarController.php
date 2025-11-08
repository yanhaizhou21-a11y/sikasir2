<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class BarController extends Controller
{
    public function index()
    {
        // Get all drink orders pending or in preparation for bar
        $orders = Order::with(['transaction.items.product', 'transaction.user'])
            ->where('destination', 'bar')
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboard.bar', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    public function markAsReady(Order $order)
    {
        $order->update(['status' => 'ready']);
        return redirect()->back()->with('success', 'Order marked as ready');
    }
}
