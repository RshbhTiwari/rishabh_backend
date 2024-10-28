<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OrderAddress;

class PaymentController extends Controller
{

    public function createOrder(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'nullable|integer',
            'cart_id' => 'required|integer',
        ]);

        try {
            $cart = Cart::with('items', 'billingAddress', 'shippingAddress')->findOrFail($validatedData['cart_id']);
            $customerId = $validatedData['customer_id'] ?? null;

            if ($customerId && $cart->customer_id !== $customerId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart does not belong to the customer.',
                ], 403);
            }

            // Check if there's an existing order with 'pending' or 'failed' status for this cart/customer
            $existingOrder = Order::where('user_id', $customerId)
                ->where('payment_status', '!=', 'captured')
                ->where('payment_method', '!=', 'cod')
                ->first();

            // Calculate order amount
            $subtotal = $cart->grand_total_cart;
            $tax = $subtotal * 0.18; // Assume 18% tax
            $shipping = 50; // Flat shipping rate
            $total = $subtotal + $tax + $shipping;

            // If an existing order is found with failed or pending payment, reuse its Razorpay order ID
            if ($existingOrder) {
                $order = $existingOrder;
                $razorpayOrderId = $existingOrder->razorpay_order_id;

                // If Razorpay order ID is missing or invalid, create a new one
                if (!$razorpayOrderId) {
                    // Initialize Razorpay API
                    $api = new Api('rzp_test_9L3JL3GPuXD0YO', 'BQvc3JHL7TFBMxOx9EIRHzQJ');

                    // Create a new Razorpay order
                    $razorpayOrder = $api->order->create([
                        'receipt' => 'order_rcpt_' . time(),
                        'amount' => (int) round($total * 100), // Ensure the amount is an integer
                        'currency' => 'INR',
                    ]);

                    // Update the existing order with the new Razorpay order ID
                    $order->update([
                        'razorpay_order_id' => $razorpayOrder['id'],
                        'subtotal' => $subtotal,
                        'tax' => $tax,
                        'shipping' => $shipping,
                        'total' => $total,
                    ]);
                    $razorpayOrderId = $razorpayOrder['id'];
                } else {
                    // Use the existing total values if they already exist
                    $subtotal = $order->subtotal;
                    $tax = $order->tax;
                    $shipping = $order->shipping;
                    $total = $order->total;
                }
            } else {
                // No existing order, create a new one
                // Initialize Razorpay API
                $api = new Api('rzp_test_9L3JL3GPuXD0YO', 'BQvc3JHL7TFBMxOx9EIRHzQJ');

                // Create a new Razorpay order
                $razorpayOrder = $api->order->create([
                    'receipt' => 'order_rcpt_' . time(),
                    'amount' => (int) round($total * 100), // Ensure the amount is an integer
                    'currency' => 'INR',
                ]);

                DB::beginTransaction();

                // Create a new order in the database
                $order = Order::create([
                    'user_id' => $customerId,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => $total,
                    'payment_status' => 'pending',
                    'payment_method' => 'razorpay',
                    'razorpay_order_id' => $razorpayOrder['id'],
                ]);

                $razorpayOrderId = $razorpayOrder['id'];

                // Save billing and shipping addresses, and add order items (like your existing code here)

                if ($cart->billingAddress) {
                    OrderAddress::create([
                        'order_id' => $order->id,
                        'type' => 'billing',
                        'addressname' => $cart->billingAddress->addressname,
                        'addrestype' => $cart->billingAddress->addresstype,
                        'city' => $cart->billingAddress->city,
                        'state' => $cart->billingAddress->state,
                        'postal_code' => $cart->billingAddress->pincode,
                        'landmarkname' => $cart->billingAddress->landmarkname,
                        'contact' => $cart->billingAddress->contact,
                        'email' => $cart->billingAddress->email,
                        'locality' => $cart->billingAddress->locality,
                    ]);
                }
                // Save the shipping address if present
                if ($cart->shippingAddress) {
                    OrderAddress::create([
                        'order_id' => $order->id,
                        'type' => 'shipping',
                        'addressname' => $cart->shippingAddress->addressname,  // Use shippingAddress here
                        'addrestype' => $cart->shippingAddress->addresstype,
                        'city' => $cart->shippingAddress->city,
                        'state' => $cart->shippingAddress->state,
                        'postal_code' => $cart->shippingAddress->pincode,
                        'landmarkname' => $cart->shippingAddress->landmarkname,
                        'contact' => $cart->shippingAddress->contact,
                        'email' => $cart->shippingAddress->email,
                        'locality' => $cart->shippingAddress->locality,
                    ]);
                }

                // Add order items
                foreach ($cart->items as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->item_id,
                        'product_name' => $cartItem->item_title,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->price,
                        'discount' => $cartItem->discount,
                        'total_price' => $cartItem->price,
                    ]);
                }
                DB::commit();
            }

            // Return response with order details
            return response()->json([
                'status' => true,
                'message' => 'Order created successfully.',
                'data' => [
                    'order_id' => $order->id,
                    'razorpay_order_id' => $razorpayOrderId,
                    'amount' => $total,
                    'currency' => 'INR',
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function handlePaymentCallback(Request $request)
    {
        $validatedData = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            // Verify payment signature
            $attributes = [
                'razorpay_order_id' => $validatedData['razorpay_order_id'],
                'razorpay_payment_id' => $validatedData['razorpay_payment_id'],
                'razorpay_signature' => $validatedData['razorpay_signature'],
            ];

            $api->utility->verifyPaymentSignature($attributes);
            $payment = $api->payment->fetch($validatedData['razorpay_payment_id']);

            // Update order status and transaction details
            $order = Order::where('razorpay_order_id', $validatedData['razorpay_order_id'])->firstOrFail();
            $order->update([
                'payment_status' => $payment->status,
            ]);

            // If payment is successful, empty the cart
            if ($payment->status === 'captured') {
                // Find the cart for the customer using the customer_id from the order
                $cart = Cart::with('items')->where('customer_id', $order->user_id)->first();

                if ($cart) {
                    // Delete the cart items
                    $cart->items()->where('selected', 1)->delete();
                } else {
                    Log::warning('Cart not found for customer with ID: ' . $order->user_id);
                }
            }


            $transaction = Transaction::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'payment_status' => $payment->status,
                    'amount' => $payment->amount / 100, // Convert paise to rupees
                    'currency' => $payment->currency,
                    'international' => $payment->international,
                    'method' => $payment->method,
                    'amount_refunded' => $payment->amount_refunded / 100,
                    'captured' => $payment->captured,
                    'description' => $payment->description,
                    'card_details' => isset($payment->card) ? json_encode($payment->card->toArray()) : null,
                    'bank' => isset($payment->bank) ? json_encode($payment->bank->toArray()) : null,
                    'wallet' => isset($payment->wallet) ? json_encode($payment->wallet->toArray()) : null,
                    'vpa' => $payment->vpa,
                    'token_id' => $payment->token_id ?? null,
                    'fee' => $payment->fee / 100, // Convert paise to rupees
                    'tax' => $payment->tax / 100,
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Payment verified successfully.',
                'order' => $order,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Razorpay Payment Verification Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function paymentFailed(Request $request)
    {
        $request->validate([
            'error_description' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
        ]);

        try {
            // Fetch the payment details from Razorpay
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $payment = $api->payment->fetch($request->razorpay_payment_id);

            // Update the order status to failed
            $order = Order::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();
            $order->update([
                'payment_status' => $payment->status,
            ]);
            $transaction = Transaction::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'payment_status' => $payment->status,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'international' => $payment->international,
                    'method' => $payment->method,
                    'amount_refunded' => $payment->amount_refunded,
                    'captured' => $payment->captured,
                    'description' => $payment->description,
                    'card_details' => $payment->card,
                    'bank' => $payment->bank,
                    'wallet' => $payment->wallet,
                    'vpa' => $payment->vpa,
                    'token_id' => $payment->token_id ?? null,
                    'fee' => $payment->fee,
                    'tax' => $payment->tax,
                    'error_code' => $payment->error_code,
                    'error_description' => $payment->error_description,
                    'error_source' => $payment->error_source,
                    'error_step' => $payment->error_step,
                    'error_reason' => $payment->error_reason,
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Payment Failed.',
                'order' => $order,
            ], 200);
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            return response()->json([
                'status' => false,
                'message' => 'Bad request to Razorpay: ' . $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    //for cash on delivery
    public function createCodOrder(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'nullable|integer',
            'cart_id' => 'required|integer',
        ]);

        try {
            $cart = Cart::with('items')->findOrFail($validatedData['cart_id']);
            $customerId = $validatedData['customer_id'] ?? null;

            // return $customerId;
            // dd($customerId);
            if ($customerId && $cart->customer_id !== $customerId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart does not belong to the customer.',
                ], 403);
            }

            // Calculate order amount
            $subtotal = $cart->grand_total_cart;
            $tax = $subtotal * 0.18; // Assume 18% tax
            $shipping = 50; // Flat shipping rate
            $total = $subtotal + $tax + $shipping;

            DB::beginTransaction();

            // Create a new order
            $order = Order::create([
                'user_id' => $customerId,
                'cart_id' => $validatedData['cart_id'], // Add cart_id for tracking
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'payment_status' => 'pending', // Set as pending for now
                'payment_method' => 'cod',
                'razorpay_order_id' => null, // No Razorpay order ID for COD
            ]);
            
            $cart->items()->where('selected', 1)->delete();

            // Save the billing address if present
            if ($cart->billingAddress) {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'type' => 'billing',
                    'addressname' => $cart->billingAddress->addressname,
                    'addrestype' => $cart->billingAddress->addresstype,
                    'city' => $cart->billingAddress->city,
                    'state' => $cart->billingAddress->state,
                    'postal_code' => $cart->billingAddress->pincode,
                    'landmarkname' => $cart->billingAddress->landmarkname,
                    'contact' => $cart->billingAddress->contact,
                    'email' => $cart->billingAddress->email,
                    'locality' => $cart->billingAddress->locality,
                ]);
            }
            // Save the shipping address if present
            if ($cart->shippingAddress) {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'type' => 'shipping',
                    'addressname' => $cart->shippingAddress->addressname,  // Use shippingAddress here
                    'addrestype' => $cart->shippingAddress->addresstype,
                    'city' => $cart->shippingAddress->city,
                    'state' => $cart->shippingAddress->state,
                    'postal_code' => $cart->shippingAddress->pincode,
                    'landmarkname' => $cart->shippingAddress->landmarkname,
                    'contact' => $cart->shippingAddress->contact,
                    'email' => $cart->shippingAddress->email,
                    'locality' => $cart->shippingAddress->locality,
                ]);
            }


            // Add order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->item_id,
                    'product_name' => $cartItem->item_title,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'discount' => $cartItem->discount,
                    'total_price' => $cartItem->price,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'COD order created successfully.',
                'order_id' => $order->id,
                'amount' => $total,
                'currency' => 'INR',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('COD Order Creation Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to create COD order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function confirmCodPayment(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|string|in:success,failed',
        ]);

        try {
            $order = Order::findOrFail($validatedData['order_id']);

            if ($validatedData['status'] === 'success') {
                $order->update(['payment_status' => 'completed']);

                // Clear the cart after successful payment
                $cart = Cart::findOrFail($order->cart_id);

                $cart->items()->where('selected', 1)->delete();
            } else {
                $order->update(['payment_status' => 'failed']);
            }

            return response()->json([
                'status' => true,
                'message' => 'COD payment status updated successfully.',
                'order' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update COD payment status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
