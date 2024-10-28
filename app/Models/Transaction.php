<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'order_id',
        'payment_status',
        'amount',
        'currency',
        'international',
        'method',
        'amount_refunded',
        'captured',
        'description',
        'card_details',
        'bank',
        'wallet',
        'vpa',
        'token_id',
        'fee',
        'tax',
        'error_code',
        'error_description',
        'error_source',
        'error_step',
        'error_reason',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
