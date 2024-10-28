<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $casts = [
        'defaultaddress' => 'boolean',
        'default_billing_address' => 'boolean',
        'default_shipping_address' => 'boolean',
        'is_billing' => 'boolean',
        'is_shipping' => 'boolean',
    ];
    
    protected $fillable = [
        'cart_id',
        'customer_id',
        'contact',
        'name',
        'landmarkname',
        'addressname',
        'pincode',
        'locality',
        'state',
        'city',
        'addresstype',
        'email',
        'defaultaddress',
        'is_billing',
        'is_shipping',
        'default_billing_address',
        'default_shipping_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
