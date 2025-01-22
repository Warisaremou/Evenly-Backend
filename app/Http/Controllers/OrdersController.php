<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::all();
        return response()->json($orders);

        if (!$orders) {
            return response()->json([
                'message' => 'Orders not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $order = Orders::create([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }

    public function show($id)
    {
        $order = Orders::with(['tickets', 'user'])->find($id);

        if ($order) {
            return response()->json([
                'order' => [
                    'id' => $order->id,
                    'name' => $order->name,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ],
                'tickets' => $order->tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'name' => $ticket->name,
                        'quantity' => $ticket->quantity,
                        'price' => $ticket->price,
                        'created_at' => $ticket->created_at,
                        'updated_at' => $ticket->updated_at,
                        'deleted_at' => $ticket->deleted_at,
                    ];
                }),
                'user' => [
                    'id' => $order->user->id,
                    'firstname' => $order->user->firstname,
                    'lastname' => $order->user->lastname,
                    'email' => $order->user->email,
                    'role' => $order->user->role,
                    'organizer_name' => $order->user->organizer_name,
                    'created_at' => $order->user->created_at,
                    'updated_at' => $order->user->updated_at,
                ],
            ]);
        }else{
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $order = Orders::findOrFail($id);
        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }
        
        $order->name = $validated['name'];
        $order->save();

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order
        ]);
    }

    public function destroy($id)
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
        ]);
    }
}