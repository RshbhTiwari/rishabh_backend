<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function getCart(Request $request, $id)
    {

        try {
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE); // Get the status parameter from the request

            // Determine whether to use the customer_id or cart_id based on the status
            if ($status == 'true') {
                // Retrieve the cart by customer_id
                $cart = Cart::with('items')
                    ->where('customer_id', $id)
                    ->firstOrFail();
            } else {
                // Retrieve the cart by cart_id and ensure customer_id is null (for guest users)
                $cart = Cart::with('items')
                    ->where('id', $id)
                    ->whereNull('customer_id')
                    ->firstOrFail();
            }

            $cart->selectLength = $cart->items()->where('selected', true)->count();

            // Transform items to include product information
            $cart->items->transform(function ($item) {

                $price = $item->price;
                $discount = $item->discount ?? 0;
                $sellingPrice = $discount > 0 ? $discount : $price;
                $totalPrice = $sellingPrice * $item->quantity;

                return array_merge($item->toArray(), [
                    'description' => $item->product->description ?? '',
                    'short_description' => $item->product->short_description ?? '',
                    'additional_images' => isset($item->product->additional_images) ? json_decode($item->product->additional_images) : [],
                    'price' =>  $sellingPrice ?? '',
                    'name' => $item->product->name ?? '',
                    'discount' => $item->product->discount_price > 0 ? $item->product->discount_price : null,
                    // 'discount' => $discount > 0 ? $discount : null,
                    'totalPrice' =>  $totalPrice ?? '',
                    'selected' => (bool) $item->selected ?? '',

                ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Cart retrieved successfully.',
                'cart' => $cart,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cart not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Add item to cart
    public function addToCart(Request $request)
    {
        try {
            $item = Product::findOrFail($request->item_id);
            $price = $item->price;
            $name = $item->name;
            $discount = $item->discount_price ?? 0;
            $sellingPrice = $discount > 0 ? $discount : $price;

            $cartM = new Cart();
            $cart = $cartM->getUserCart($request->cart_id, $request->customer_id);
            $cartItem = $cart->items()->updateOrCreate(
                ['item_id' => $request->item_id],
                [
                    'item_title' => $name,
                    'price' => $sellingPrice,
                    'discount' => $discount,
                    'quantity' => $request->has('quantity') ? $request->quantity : 1,
                    'cart_id' => $cart->id,
                    'selected' => true
                ]
            );

            $cartM->updateCartTotals($cart);

            return response()->json([
                'status' => true,
                'message' => 'Item added to cart successfully.',
                'cart_id' => $cart->id,
                'cart' => $cart->load('items')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add item to cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // Update cart item
    public function updateCartItem(Request $request)
    {
        try {
            $cart_itemId = $request->item_id;
            $cartItem  = CartItem::find($cart_itemId);
            // dd($cartItem );
            $cartItem->update([
                'quantity' => $request->quantity,
            ]);

            $cartM = new Cart();
            $cart = $cartM->getUserCart($request->cart_id, $request->customer_id);
            $cartM->updateCartTotals($cart);

            return response()->json([
                'status' => true,
                'message' => 'Cart item updated successfully.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update cart item.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Remove item from cart
    public function removeCartItem(Request $request, $cart_itemId)
    {
        try {
            $cartItem  = CartItem::find($cart_itemId);

            $cart = $cartItem->cart;

            $cartItem->delete();

            $cartM = new Cart();
            $cartM->updateCartTotals($cart);

            return response()->json([
                'status' => true,
                'message' => 'Item removed from cart successfully.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove item from cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get a specific address
    public function show($id)
    {
        try {
            $address = CustomerAddress::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Address retrieved successfully.',
                'address' => $address
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve address.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // API to select/unselect a specific item
    public function toggleSelectItem(Request $request, $cart_itemId)
    {
        try {
            $cartItem = CartItem::findOrFail($cart_itemId);
            $cartItem->update(['selected' => $request->selected]);

            // Recalculate the totals after updating selected state
            $cart = $cartItem->cart;
            $cart->updateCartTotals($cart);

            return response()->json([
                'status' => true,
                'message' => 'Cart item selection updated successfully.',
                'cart' => $cart->load('items')
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update item selection.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // API to select/unselect all items in the cart
    public function toggleSelectAllItems(Request $request, $cart_id)
    {
        try {
            $cart = Cart::with('items')->findOrFail($cart_id);

            // Update the 'selected' field for all items in the cart
            $cart->items()->update(['selected' => $request->selected]);

            // Recalculate the totals
            $cart->updateCartTotals($cart);

            return response()->json([
                'status' => true,
                'message' => 'All cart items selection updated successfully.',
                'cart' => $cart->load('items')
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cart not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update all items selection.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API to remove all items from the cart
    public function removeAllItemsFromCart(Request $request, $cart_id)
    {
        try {
            $cart = Cart::with('items')->findOrFail($cart_id);

            // Delete all items in the cart
            $cart->items()->delete();

            // Recalculate the totals
            $cart->updateCartTotals($cart);

            return response()->json([
                'status' => true,
                'message' => 'All items removed from cart successfully.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cart not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove items from cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }













    // Determine default billing and shipping addresses
    public function index($customer_id)
    {
        try {
            // Retrieve addresses for the specified customer_id
            $addresses = CustomerAddress::where('customer_id', $customer_id)->orderBy('updated_at', 'desc')->get();

            // Determine default billing and shipping addresses
            $defaultBillingAddressId = $addresses->where('is_billing', true)->where('default_billing_address', true)->first()?->id;
            $defaultShippingAddressId = $addresses->where('is_shipping', true)->where('default_shipping_address', true)->first()?->id;

            // Transform the addresses to include default_billing_address and default_shipping_address fields
            $addresses = $addresses->map(function ($address) use ($defaultBillingAddressId, $defaultShippingAddressId) {
                return [
                    'id' => $address->id,
                    'cart_id' => $address->cart_id,
                    'customer_id' => $address->customer_id,
                    'contact' => $address->contact,
                    'landmarkname' => $address->landmarkname,
                    'addressname' => $address->addressname,
                    'pincode' => $address->pincode,
                    'locality' => $address->locality,
                    'state' => $address->state,
                    'city' => $address->city,
                    'addresstype' => $address->addresstype,
                    'email' => $address->email,
                    'is_billing' => filter_var($address->is_billing, FILTER_VALIDATE_BOOLEAN),
                    'is_shipping' => filter_var($address->is_shipping, FILTER_VALIDATE_BOOLEAN),
                    'created_at' => $address->created_at,
                    'updated_at' => $address->updated_at,
                    'name' => $address->name,
                    'default_billing_address' => $address->id === $defaultBillingAddressId,
                    'default_shipping_address' => $address->id === $defaultShippingAddressId
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Addresses retrieved successfully.',
                'addresses' => $addresses
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve addresses.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction(); // Begin a transaction

        try {
            // Validate the request data
            $validatedData = $request->validate([
                'customer_id' => 'nullable|integer',
                'name' => 'required|string|max:255',
                'contact' => 'required|string|max:15',
                'landmarkname' => 'nullable|string|max:255',
                'addressname' => 'required|string|max:255',
                'pincode' => 'required|string|max:10',
                'locality' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'addresstype' => 'required|string|max:50',
                'email' => 'nullable|email|max:255',
                'cart_id' => 'nullable|integer|exists:carts,id',
                'is_billing' => 'nullable|boolean',
                'is_shipping' => 'nullable|boolean',
                'default_billing_address' => 'nullable|boolean',
                'default_shipping_address' => 'nullable|boolean',
            ]);

            // Prepare data for insertion
            $data = $validatedData;
            $data['default_billing_address'] = $request->boolean('default_billing_address', false);
            $data['default_shipping_address'] = $request->boolean('default_shipping_address', false);
            $data['is_billing'] = $request->boolean('is_billing', false);
            $data['is_shipping'] = $request->boolean('is_shipping', false);

            $newAddress = null;

            // // Handle guest user case (i.e., no customer_id)
            // if (!isset($validatedData['customer_id']) || is_null($validatedData['customer_id'])) {
            //     // Guest user logic: Check if an address with the same details already exists for the same cart
            //     $existingAddress = CustomerAddress::where('cart_id', $validatedData['cart_id'])
            //         ->where('addressname', $validatedData['addressname'])
            //         ->where('pincode', $validatedData['pincode'])
            //         ->where('locality', $validatedData['locality'])
            //         ->where('city', $validatedData['city'])
            //         ->where('state', $validatedData['state'])
            //         ->first();

            //     if ($existingAddress) {
            //         // If the address already exists, reuse it
            //         $newAddress = $existingAddress;
            //     } else {
            //         // Create the new customer address if it doesn't exist
            //         $newAddress = CustomerAddress::create($data);
            //     }
            // } 

            // If the customer is logged in (i.e., has customer_id), proceed with creation

            // Ensure only one default billing address per customer
            if ($data['default_billing_address']) {
                CustomerAddress::where('customer_id', $data['customer_id'])
                    ->where('is_billing', true)
                    ->update(['default_billing_address' => false]);
            }

            // Ensure only one default shipping address per customer
            if ($data['default_shipping_address']) {
                CustomerAddress::where('customer_id', $data['customer_id'])
                    ->where('is_shipping', true)
                    ->update(['default_shipping_address' => false]);
            }

            // Create the new customer address for logged-in users
            $newAddress = CustomerAddress::create($data);


            // If a cart ID is provided, attach the address to the cart
            if ($request->filled('cart_id')) {
                // Retrieve the cart
                $cart = Cart::findOrFail($validatedData['cart_id']);

                // Check if it should be attached as a billing or shipping address
                if ($data['is_billing']) {
                    $cart->billing_address_id = $newAddress->id;
                }

                if ($data['is_shipping']) {
                    $cart->shipping_address_id = $newAddress->id;
                }

                // else {
                //     $cart->shipping_address_id = $newAddress->id;
                // }

                // Save the cart with the updated address information
                $cart->save();
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => true,
                'message' => 'Address created successfully.',
                'address' => $newAddress
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back in case of an error

            return response()->json([
                'status' => false,
                'message' => 'Failed to create address.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction(); // Start a transaction

        try {
            // Validate the incoming data
            $validatedData = $request->validate([
                'customer_id' => 'nullable|integer|exists:users,id',
                'name' => 'required|string|max:255',
                'contact' => 'required|string|max:15',
                'landmarkname' => 'nullable|string|max:255',
                'addressname' => 'required|string|max:255',
                'pincode' => 'required|string|max:10',
                'locality' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'addresstype' => 'required|string|max:50',
                'email' => 'nullable|email|max:255',
                'cart_id' => 'nullable|integer|exists:carts,id',
                'is_billing' => 'nullable|boolean',
                'is_shipping' => 'nullable|boolean',
                'default_billing_address' => 'nullable|boolean',
                'default_shipping_address' => 'nullable|boolean',
            ]);

            // Fetch the existing address record
            $address = CustomerAddress::findOrFail($id);

            // Prepare the updated data
            $data = $validatedData;
            $data['default_billing_address'] = $request->boolean('default_billing_address', false);
            $data['default_shipping_address'] = $request->boolean('default_shipping_address', false);
            $data['is_billing'] = $request->boolean('is_billing', false);
            $data['is_shipping'] = $request->boolean('is_shipping', false);

            // Ensure only one default billing address per customer
            if ($data['default_billing_address']) {
                CustomerAddress::where('customer_id', $data['customer_id'])
                    ->where('is_billing', true)
                    ->where('id', '!=', $id) // Ensure the current address is not updated
                    ->update(['default_billing_address' => false]);
            }

            // Ensure only one default shipping address per customer
            if ($data['default_shipping_address']) {
                CustomerAddress::where('customer_id', $data['customer_id'])
                    ->where('is_shipping', true)
                    ->where('id', '!=', $id) // Ensure the current address is not updated
                    ->update(['default_shipping_address' => false]);
            }

            // Update the existing customer address
            $address->update($data);

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => true,
                'message' => 'Address updated successfully.',
                'address' => $address
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back in case of an error

            return response()->json([
                'status' => false,
                'message' => 'Failed to update address.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Delete an address
    public function destroy($id)
    {
        try {
            $address = CustomerAddress::findOrFail($id);
            $address->delete();
            return response()->json([
                'status' => true,
                'message' => 'Address deleted successfully.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete address.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function attachAddressToCart(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'cart_id' => 'required|integer|exists:carts,id',
            'address_id' => 'required|integer|exists:customer_addresses,id',
        ]);

        try {
            // Retrieve the cart based on cart_id
            $cart = Cart::findOrFail($validatedData['cart_id']);

            // Retrieve the address based on address_id
            $address = CustomerAddress::findOrFail($validatedData['address_id']);

            // Determine if it's a billing address or a shipping address
            if ($request->boolean('is_billing')) {
                // If billing address already exists, update it; if not, create it
                $cart->updateOrCreate(
                    ['id' => $cart->id], // Condition to check
                    ['billing_address_id' => $validatedData['address_id']] // Update the billing address
                );
                $message = 'Billing address attached successfully.';
            } else {
                // If shipping address already exists, update it; if not, create it
                $cart->updateOrCreate(
                    ['id' => $cart->id], // Condition to check
                    ['shipping_address_id' => $validatedData['address_id']] // Update the shipping address
                );
                $message = 'Shipping address attached successfully.';
            }

            // Save the cart with the updated address information
            $cart->save();

            return response()->json([
                'status' => true,
                'message' => $message,
                'cart' => $cart
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json([
                'status' => false,
                'message' => 'Failed to attach address to the cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Add item to wishlist
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $request->user(); // Access user from Sanctum middleware

        $wishlistItem = Wishlist::firstOrCreate([
            'user_id' => $user->id, // Use user ID from Sanctum
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Item added to wishlist successfully.',
            'wishlistItem' => $wishlistItem
        ], 200);
    }

    // Get all wishlist items for the logged-in user
    public function getWishlist()
    {
        $wishlistItems = Wishlist::with('product')->where('user_id', Auth::id())->get();

        $wishlistItems->transform(function ($item) {
            $item->product->additional_images = json_decode($item->product->additional_images);
            return $item;
        });
        return response()->json([
            'status' => true,
            'message' => 'Wishlist retrieved successfully.',
            'wishlistItems' => $wishlistItems
        ], 200);
    }

    // Remove item from wishlist
    public function removeFromWishlist(Request $request, $productId)
    {
        $user = $request->user(); // Access user from Sanctum middleware

        $wishlistItem = Wishlist::where('user_id', $user->id) // Use user ID from Sanctum
            ->where('product_id', $productId)
            ->firstOrFail();

        $wishlistItem->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from wishlist successfully.'
        ], 200);
    }

    //move wishlist item to cart
    public function moveToCart(Request $request)
    {
        try {
            // Get the wishlist item
            $wishlistItem = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->firstOrFail();

            // Get the product details
            $product = Product::findOrFail($request->product_id);
            $price = $product->price;
            $discount = $product->discount_price ?? 0;
            $sellingPrice = $discount > 0 ? $discount : $price;

            // Add item to the cart
            $cartM = new Cart();
            $cart = $cartM->getUserCart($request->cart_id, Auth::id());
            $cartItem = $cart->items()->updateOrCreate(
                ['item_id' => $product->id],
                [
                    'item_title' => $product->name,
                    'price' => $sellingPrice,
                    'discount' => $discount,
                    'quantity' => 1, // Default to 1, adjust as needed
                    'cart_id' => $cart->id,
                ]
            );
            // Remove item from the wishlist
            $wishlistItem->delete();

            // Update cart totals
            $cartM->updateCartTotals($cart);

            return response()->json([
                'status' => true,
                'message' => 'Item moved to cart successfully.',
                'cart_id' => $cart->id,
                'cart' => $cart->load('items')
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Wishlist item not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to move item to cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
