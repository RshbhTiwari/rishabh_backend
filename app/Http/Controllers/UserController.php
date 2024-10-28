<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Fetch the authenticated user's data
    public function show(Request $request)
    {
        try {
            $user = $request->user(); // Access user from Sanctum middleware

            return response()->json([
                'status' => true,
                'message' => 'User data retrieved successfully.',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve user data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        // Access user from Sanctum middleware
        $user = $request->user();

        // Validate request data with a unique email rule, excluding the current user's email
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|max:15',
            'contact' => 'nullable|string|max:15',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return a structured error response with all validation errors
            return response()->json([
                'status' => false,
                'message' => 'User data update failed.',
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        // Retrieve validated data from the validator instance
        $validatedData = $validator->validated();

        // Update the user with validated data
        $user->update($validatedData);

        // Return a success response with the updated user data
        return response()->json([
            'status' => true,
            'message' => 'User data updated successfully.',
            'user' => $user,  // The $user object is already updated, so you can return it directly
        ], 200);
    }

    public function getUserAccountDetails(Request $request, $user_id)
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($user_id);

            // Load related addresses and orders with items
            $user->load('addresses', 'orders.items');

            // Initialize variables for shipping and billing addresses
            $shippingAddress = null;
            $billingAddress = null;

            // Loop through the addresses and assign the appropriate ones
            foreach ($user->addresses as $address) {
                $formattedAddress = [
                    'id' => $address->id,
                    'name' => $address->name,
                    'contact' => $address->contact,
                    'landmarkname' => $address->landmarkname,
                    'addressname' => $address->addressname,
                    'pincode' => $address->pincode,
                    'locality' => $address->locality,
                    'state' => $address->state,
                    'city' => $address->city,
                    'addresstype' => $address->addresstype,
                    'email' => $address->email,
                    'default_billing_address' => (bool) $address->default_billing_address,
                    'default_shipping_address' => (bool) $address->default_shipping_address,
                    'is_billing' => (bool) $address->is_billing,
                    'is_shipping' => (bool) $address->is_shipping,
                ];
                // If this address is both the default billing and shipping address, assign it to both
                if ($address->default_billing_address && $address->default_shipping_address) {
                    $billingAddress = $formattedAddress;
                    $shippingAddress = $formattedAddress;
                    break;
                }

                // Assign to billing or shipping based on flags
                if ($address->is_shipping) {
                    $shippingAddress = $formattedAddress;
                }
                if ($address->is_billing) {
                    $billingAddress = $formattedAddress;
                }
            }

            // Format orders data
            $orders = $user->orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $order->total,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'items' => $order->items->map(function ($item) {
                        $item->product->additional_images = json_decode($item->product->additional_images);

                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'discount_price' => $item->discount,
                            'total_price' => $item->total_price,
                            'product_image' => $item->product->additional_images[0],
                            'description' => $item->product->description,
                            'short_description' => $item->product->short_description,
                        ];
                    }),
                ];
            });

            // Prepare response data
            $responseData = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'orders' => $orders,
            ];

            // Return response
            return response()->json([
                'status' => true,
                'message' => 'User account details retrieved successfully.',
                'data' => $responseData,
            ], 200);
        } catch (\Exception $e) {
            // Handle exception and return error response
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve user account details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
