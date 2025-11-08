@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Kitchen Dashboard</h1>
                <p class="text-gray-600 mt-2">Manage and prepare food orders</p>
            </div>
            <div class="px-4 py-2 bg-[#e17f12] text-white rounded-lg">
                <i class="bi bi-fire mr-2"></i>
                <span class="font-semibold">{{ $orders->count() }} Active Orders</span>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($orders as $order)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                <!-- Order Header -->
                <div class="bg-gradient-to-r from-[#e17f12] to-[#ab5c16] p-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-lg">{{ $order->order_number }}</h3>
                            <p class="text-sm opacity-90">{{ $order->created_at->format('H:i') }}</p>
                        </div>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold uppercase">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>

                <!-- Order Content -->
                <div class="p-4">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="bi bi-person mr-1"></i>
                            {{ $order->transaction->user->name ?? 'Cashier' }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="bi bi-receipt mr-1"></i>
                            {{ $order->transaction->invoice }}
                        </p>
                    </div>

                    <!-- Items List -->
                    <div class="border-t pt-3 space-y-2 max-h-48 overflow-y-auto">
                        @foreach ($order->transaction->items as $item)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $item->qty }}</p>
                                </div>
                                <p class="text-sm font-semibold text-[#e17f12]">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <!-- Total -->
                    <div class="border-t pt-3 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Total:</span>
                            <span class="text-lg font-bold text-[#005281]">
                                Rp {{ number_format($order->transaction->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-4 py-3 bg-gray-50 space-x-2">
                    @if($order->status == 'pending')
                        <form method="POST" action="{{ route('kitchen.orders.update-status', $order) }}" class="inline-block">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="status" value="preparing">
                            <button type="submit" class="w-full bg-[#005281] text-white px-4 py-2 rounded-lg hover:bg-[#004371] transition-colors font-medium text-sm">
                                <i class="bi bi-play-circle mr-1"></i>Start Cooking
                            </button>
                        </form>
                    @elseif($order->status == 'preparing')
                        <form method="POST" action="{{ route('kitchen.orders.ready', $order) }}" class="inline-block">
                            @csrf
                            @method('POST')
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium text-sm">
                                <i class="bi bi-check-circle mr-1"></i>Mark as Ready
                            </button>
                        </form>
                    @elseif($order->status == 'ready')
                        <div class="w-full bg-green-500 text-white px-4 py-2 rounded-lg text-center font-medium text-sm">
                            <i class="bi bi-check2-circle mr-1"></i>Ready for Service
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="bi bi-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Orders Yet</h3>
                <p class="text-gray-500">Waiting for food orders...</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh every 30 seconds
    setTimeout(function(){
        window.location.reload();
    }, 30000);

    // Realtime via Echo if available
    if (window.Echo) {
        try {
            window.Echo.channel('orders.kitchen')
                .listen('.order.created', function (e) {
                    window.location.reload();
                });
        } catch (err) {
            console.warn('Echo not initialized:', err);
        }
    }
</script>
@endpush
@endsection