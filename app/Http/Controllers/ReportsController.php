<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(Request $request)
    {
        // Get date range from request or use default (current month)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Get transaction statistics
        $totalTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');
        $avgTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Get top products by querying TransactionItems directly
        $topProductsQuery = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select('products.id', 'products.name', DB::raw('SUM(transaction_items.qty) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();

        // Convert to collection with Product models
        $topProducts = collect($topProductsQuery)->map(function ($item) {
            $product = Product::find($item->id);
            if ($product) {
                $product->total_sold = $item->total_sold;
                return $product;
            }
            return null;
        })->filter();

        // Daily revenue chart data (last 30 days)
        $dailyRevenue = Transaction::whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by payment method
        $byMethod = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as cnt, SUM(total) as sum')
            ->groupBy('payment_method')
            ->pluck('sum', 'payment_method');

        $countByMethod = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as cnt')
            ->groupBy('payment_method')
            ->pluck('cnt', 'payment_method');

        return view('reports.index', compact(
            'totalTransactions',
            'totalRevenue',
            'avgTransactionValue',
            'topProducts',
            'dailyRevenue',
            'startDate',
            'endDate',
            'byMethod',
            'countByMethod'
        ));
    }
}
