@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Daftar Transaksi</h1>
            <p class="text-gray-600 mt-2">Kelola semua transaksi penjualan</p>
        </div>
        @if(auth()->user()->hasRole('kasir') || auth()->user()->hasRole('admin'))
        <a href="{{ route('kasir.index') }}" 
           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="bi bi-plus-circle mr-2"></i>Transaksi Baru
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $transactions->total() }}</p>
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
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        Rp {{ number_format($transactions->sum('total'), 0, ',', '.') }}
                    </p>
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
                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        Rp {{ $transactions->count() > 0 ? number_format($transactions->avg('total'), 0, ',', '.') : '0' }}
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="bi bi-graph-up text-3xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Invoice
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kasir
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Metode Pembayaran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $transaction->invoice }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($transaction->transaction_time ?? $transaction->created_at)->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($transaction->payment_method == 'cash') bg-green-100 text-green-800
                                @elseif($transaction->payment_method == 'qris') bg-blue-100 text-blue-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                @if($transaction->payment_method == 'cash')
                                    <i class="bi bi-cash-coin mr-1"></i>Cash
                                @elseif($transaction->payment_method == 'qris')
                                    <i class="bi bi-qr-code mr-1"></i>QRIS
                                @else
                                    <i class="bi bi-credit-card mr-1"></i>Debit
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($transaction->status == 'paid') bg-green-100 text-green-800
                                @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-3 items-center">
                                <a href="{{ route('admin.transactions.show', $transaction) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Lihat Transaksi">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @php
                                    $receiptUrl = auth()->user()->hasRole('kasir')
                                        ? route('kasir.transaksi.receipt', $transaction)
                                        : route('admin.transactions.receipt', $transaction);
                                @endphp
                                <a href="{{ $receiptUrl }}" class="text-green-600 hover:text-green-800" title="Lihat Struk">
                                    <i class="bi bi-receipt"></i>
                                </a>
                                @if(in_array($transaction->payment_method, ['qris','debit']) && $transaction->status !== 'paid')
                                <form action="{{ auth()->user()->hasRole('kasir') ? route('kasir.transaksi.confirm', $transaction) : route('admin.transactions.confirm', $transaction) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:text-emerald-800" title="Konfirmasi Pembayaran">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                </form>
                                @endif
                                @if(auth()->user()->hasRole('admin'))
                                <form action="{{ route('admin.transactions.destroy', $transaction) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada transaksi ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

@stack('scripts')
<script>
    // Optional: Add DataTables for better table functionality
    // Uncomment if you want to use DataTables
    /*
    $(document).ready(function() {
        $('table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });
    });
    */
</script>
@endsection

