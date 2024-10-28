<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;

class Login extends Model
{
    use HasFactory;

    public function handleCartMerging($guestCartId, $userId)
    {
        // Retrieve guest cart
        $guestCart = Cart::where('id', $guestCartId)->whereNull('customer_id')->first();

        if ($guestCart) {
            // Check for an existing user cart
            $userCart = Cart::where('customer_id', $userId)->first();

            if ($userCart) {
                // Merge guest cart items into user cart
                foreach ($guestCart->items as $guestItem) {
                    $existingItem = $userCart->items->where('item_id', $guestItem->item_id)->first();

                    if ($existingItem) {
                        // Update quantity if the item already exists
                        $existingItem->quantity += $guestItem->quantity;
                        $existingItem->save();
                    } else {
                        // Add new item to user cart
                        $guestItem->cart_id = $userCart->id;
                        $guestItem->save();
                    }
                }
                // Delete guest cart
                $guestCart->delete();
            } else {
                // No existing user cart, assign guest cart to user
                $guestCart->customer_id = $userId;
                $guestCart->is_guest = false;
                $guestCart->save();
            }
        }
    }
}
