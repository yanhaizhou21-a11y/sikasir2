@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detail Transaksi</h1>
                <p class="text-gray-600 mt-2">Invoice: {{ $transaction->invoice }}</p>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $receiptUrl = auth()->user()->hasRole('kasir')
                        ? route('kasir.transaksi.receipt', $transaction)
                        : route('admin.transactions.receipt', $transaction);
                @endphp
                @if(in_array($transaction->payment_method, ['qris','debit']) && $transaction->status !== 'paid')
                <form action="{{ auth()->user()->hasRole('kasir') ? route('kasir.transaksi.confirm', $transaction) : route('admin.transactions.confirm', $transaction) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        <i class="bi bi-check2-circle mr-2"></i>Konfirmasi Pembayaran
                    </button>
                </form>
                @endif
                <a href="{{ $receiptUrl }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="bi bi-receipt mr-2"></i>Lihat Struk
                </a>
                <a href="{{ route('admin.transactions.index') }}" 
                   class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                    <i class="bi bi-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Transaction Header Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Transaksi</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Invoice:</span>
                        <span class="font-medium">{{ $transaction->invoice }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kasir:</span>
                        <span class="font-medium">{{ $transaction->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal:</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($transaction->transaction_time ?? $transaction->created_at)->format('d F Y H:i:s') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode Pembayaran:</span>
                        <span class="font-medium">
                            <span class="px-2 py-1 rounded text-xs
                                @if($transaction->payment_method == 'cash') bg-green-100 text-green-800
                                @elseif($transaction->payment_method == 'qris') bg-blue-100 text-blue-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                @if($transaction->payment_method == 'cash')
                                    <i class="bi bi-cash-coin"></i> Cash
                                @elseif($transaction->payment_method == 'qris')
                                    <i class="bi bi-qr-code"></i> QRIS
                                @else
                                    <i class="bi bi-credit-card"></i> Debit
                                @endif
                            </span>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 rounded text-xs font-medium
                            @if($transaction->status == 'paid') bg-green-100 text-green-800
                            @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah Item:</span>
                        <span class="font-medium">{{ $transaction->items->count() }} item</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Qty:</span>
                        <span class="font-medium">{{ $transaction->items->sum('qty') }} pcs</span>
                    </div>
                    <div class="border-t pt-3 mt-3">
                        <div class="flex justify-between text-xl font-bold">
                            <span class="text-gray-800">Total:</span>
                            <span class="text-blue-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Items -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detail Item</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produk
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Harga Satuan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Qty
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subtotal
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transaction->items as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($item->product->hasImage() && $item->product->image_url)
                                <img src="{{ $item->product->image_url }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-10 h-10 rounded object-cover mr-3 flex-shrink-0"
                                     style="max-width: 40px; max-height: 40px;"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center mr-3 hidden flex-shrink-0">
                                    <i class="bi bi-image text-gray-400 text-sm"></i>
                                </div>
                                @else
                                <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="bi bi-image text-gray-400 text-sm"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                    @if($item->product->category)
                                    <div class="text-xs text-gray-500">{{ $item->product->category->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->qty }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                            Total:
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            Rp {{ number_format($transaction->total, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    @if(auth()->user()->hasRole('admin'))
    <div class="mt-6 flex gap-4">
        <form action="{{ route('admin.transactions.destroy', $transaction) }}" 
              method="POST" 
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');"
              class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="bi bi-trash mr-2"></i>Hapus Transaksi
            </button>
        </form>
    </div>
    @endif
</div>
@endsection

