<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function getOrders()
    {
        $orders = Orders::all();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'Orders not found'
            ], 404);
        }

        return response()->json($orders, 200);
    }

    public function createOrders(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'is_canceled' => 'required|boolean',
            'is_expired' => 'required|boolean',
        ]);

        $order = Orders::create([
            'user_id' => $validated['user_id'],
            'is_canceled' => $validated['is_canceled'],
            'is_expired' => $validated['is_expired'],
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }

    public function getOrdersById($id)
    {
        $order = Orders::findOrFail($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json($order, 200);
    }

    public function updateOrders(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'is_canceled' => 'required|boolean',
            'is_expired' => 'required|boolean',
        ]);

        $order = Orders::findOrFail($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        $order->user_id = $validated['user_id'];
        $order->is_canceled = $validated['is_canceled'];
        $order->is_expired = $validated['is_expired'];
        $order->save();

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order
        ], 200);
    }

    public function destroyOrders($id)
    {
        $order = Orders::findOrFail($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ], 200);
    }
}