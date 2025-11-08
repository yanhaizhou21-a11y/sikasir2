@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Laporan</h1>
        <p class="text-gray-600 mt-2">Dashboard laporan dan analisis transaksi</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Filter Periode</h2>
        <form method="GET" action="{{ route('reports.index') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Transaksi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalTransactions) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="bi bi-receipt-cutoff text-3xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="bi bi-cash-coin text-3xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Rata-rata per Transaksi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($avgTransactionValue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="bi bi-graph-up text-3xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Penjualan per Metode Pembayaran</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border rounded-lg p-4">
                <div class="text-sm text-gray-600">Cash</div>
                <div class="text-2xl font-bold">Rp {{ number_format($byMethod['cash'] ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs text-gray-500">Transaksi: {{ number_format($countByMethod['cash'] ?? 0) }}</div>
            </div>
            <div class="border rounded-lg p-4">
                <div class="text-sm text-gray-600">QRIS</div>
                <div class="text-2xl font-bold">Rp {{ number_format($byMethod['qris'] ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs text-gray-500">Transaksi: {{ number_format($countByMethod['qris'] ?? 0) }}</div>
            </div>
            <div class="border rounded-lg p-4">
                <div class="text-sm text-gray-600">Debit</div>
                <div class="text-2xl font-bold">Rp {{ number_format($byMethod['debit'] ?? 0, 0, ',', '.') }}</div>
                <div class="text-xs text-gray-500">Transaksi: {{ number_format($countByMethod['debit'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Produk Terlaris</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Terjual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topProducts as $index => $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product->total_sold ?? 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($product->total_sold ?? 0) > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ($product->total_sold ?? 0) > 0 ? 'Aktif' : 'Belum Terjual' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data produk</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Revenue Chart (Placeholder - can be enhanced with Chart.js) -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Grafik Pendapatan (30 Hari Terakhir)</h2>
        <div class="h-64 flex items-center justify-center text-gray-500">
            <div class="text-center">
                <i class="bi bi-bar-chart text-6xl mb-4"></i>
                <p>Grafik dapat ditambahkan menggunakan Chart.js atau library chart lainnya</p>
                <p class="text-sm mt-2">Total Data: {{ $dailyRevenue->count() }} hari</p>
            </div>
        </div>
    </div>
</div>
@endsection

