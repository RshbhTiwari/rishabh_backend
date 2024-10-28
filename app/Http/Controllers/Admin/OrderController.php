<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use App\Models\Order;

class OrderController extends Controller
{
    public function viewOrder($id): View
    {
        $order = Order::with('items', 'address', 'user', 'addresses')->findOrFail($id);

        // dd($order);
        return view('admin.order-view', ['id' => $id])->with(compact('order'));
    }

    public function deleteOrder($id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('orders')->with('success', 'Order deleted successfully.');
    }

    public function getOrdersDetails($orderId)
    {
        try {
            // Fetch the order along with related data
            $order = Order::with(['items.product', 'user', 'addresses', 'transaction'])
                ->findOrFail($orderId);

            // Format order details response
            $orderDetails = [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'shipping' => $order->shipping,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'razorpay_order_id' => $order->razorpay_order_id,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'description' => $item->product->description ?? null, // Include product description from product table
                        'price' => $item->price,
                        'total_price' => $item->total_price,
                    ];
                }),
                'billing_address' => $order->addresses->where('type', 'billing')->first(),
                'shipping_address' => $order->addresses->where('type', 'shipping')->first(),
                'transaction' => $order->transaction ? [
                    'transaction_id' => $order->transaction->id,
                    'payment_method' => $order->transaction->method,
                    'status' => $order->transaction->payment_status,
                    'amount' => $order->transaction->amount,
                    'transaction_date' => $order->transaction->created_at->format('Y-m-d H:i:s'),
                ] : null,
                'user' => $order->user ? [
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                    'gender' => $order->user->mail
                ] : null,

            ];

            return response()->json([
                'status' => true,
                'order_details' => $orderDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve order details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOrdersList($userId)
    {
        try {
            $orders = Order::where('user_id', $userId)->get();

            return response()->json([
                'status' => 'success',
                'orders' => $orders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve orders: ' . $e->getMessage()
            ], 500);
        }
    }
}
