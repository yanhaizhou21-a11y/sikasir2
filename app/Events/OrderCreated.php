<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load('transaction.items.product', 'transaction.user');
    }

    public function broadcastOn(): array
    {
        // Broadcast to destination-specific public channel
        return [new Channel('orders.' . $this->order->destination)];
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'destination' => $this->order->destination,
            'transaction' => [
                'invoice' => $this->order->transaction->invoice,
                'total' => $this->order->transaction->total,
                'user' => $this->order->transaction->user?->name,
                'items' => $this->order->transaction->items->map(function ($i) {
                    return [
                        'name' => $i->product->name,
                        'qty' => $i->qty,
                        'subtotal' => $i->subtotal,
                    ];
                })->toArray(),
            ],
        ];
    }
}


