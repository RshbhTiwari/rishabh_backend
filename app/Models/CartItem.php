<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id', 'item_id', 'item_title', 'sku', 'price', 'quantity', 'discount','selected'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id')->select(['id', 'description', 'short_description', 'additional_images','discount_price','name','price']);
    }
}
