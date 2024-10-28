<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'addressname',
        'addrestype',
        'city',
        'state',
        'postal_code',
        'landmarkname',
        'contact',
        'email',
        'locality'
    ];


     /**
     * Get the order that owns the address.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
