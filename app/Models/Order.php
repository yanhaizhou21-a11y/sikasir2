<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'order_number',
        'order_type',
        'status',
        'destination',
        'notes',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
