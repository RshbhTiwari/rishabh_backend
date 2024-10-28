<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'shipping_method',
        'payment_method',
        'currency',
        'status',
        'grand_total_cart',
        'billing_address_id',
        'shipping_address_id',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'billing_address_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'shipping_address_id');
    }

    // Helper method to get or create a cart
    public function getUserCart($cartId = null, $userId = null)
    {
        if ($cartId) {
            // Try to find the cart by ID
            $cart = Cart::find($cartId);

            // If the cart exists and has no customer_id, update it
            if ($cart && is_null($cart->customer_id)) {
                $cart->update(['customer_id' => $userId, 'is_guest' => false]);
                return $cart;
            }
            // If the cart exists and already has a customer_id, return it
            if ($cart && $cart->customer_id == $userId) {
                return $cart;
            }
        }

        if ($userId) {
            $cart = Cart::where('customer_id', $userId)->first();

            // If a cart exists for the user, return it
            if ($cart) {
                return $cart;
            }
        }

        if ($cartId) {
            // Try to find the existing cart by ID
            $cart = Cart::find($cartId);
            if ($cart) {
                return $cart;
            }
        } else {
            return Cart::create([
                'is_guest' => $userId ? false : true,
                'customer_id' => $userId
            ]);
        }
    }

    // Update cart totals
    public function updateCartTotals(Cart $cart)
    {
        $subtotal = $cart->items->where('selected', true)->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        $grandTotal = $subtotal;

        // Handle the case when no items are selected
        if ($cart->items->where('selected', true)->isEmpty()) {
            $subtotal = 0;
            $grandTotal = 0;
        }

        $cart->update([
            'subtotal' => $subtotal,
            'grand_total_cart' => $grandTotal,
        ]);
    }
}
