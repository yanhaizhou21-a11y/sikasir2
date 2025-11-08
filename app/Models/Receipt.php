<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'file_path',
        'type',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}


