<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Tickets;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    public function getOrders()
    {
        $orders = Orders::all();

        return response()->json(['data' => $orders], 200);
    }

    public function createOrders(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user || $user->role->name !== 'user') {
                return response()->json([
                    'message' => 'Only users can place orders'
                ], 403);
            }

            $validated = $request->validate([
                'ticket_id' => 'required|exists:tickets,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $ticket = Tickets::with('event', 'type_ticket')->findOrFail($validated['ticket_id']);

            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket not found'
                ], 404);
            }

            if ($validated['quantity'] > $ticket->quantity) {
                return response()->json([
                    'message' => 'Not enough tickets available',
                    'available_quantity' => $ticket->quantity
                ], 400);
            }

            $order = Orders::create([
                'user_id' => $user->id,
                'ticket_id' => $validated['ticket_id'],
                'is_canceled' => false
            ]);

            $ticket->quantity -= $validated['quantity'];
            $ticket->save();

            $updatedTicket = Tickets::find($ticket->id); 

            return response()->json([
                'data' => [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'ticket_id' => $updatedTicket->id,
                    'ticket_name' => $updatedTicket->name,
                    'remaining_quantity' => $updatedTicket->quantity,
                    'ticket_price' => $updatedTicket->price,
                    'event_id' => $updatedTicket->event->id,
                    'type_ticket' => $updatedTicket->type_ticket->name,
                    'ordered_quantity' => $validated['quantity']
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrdersByUser(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user || $user->role->name !== 'user') {
                return response()->json([
                    'message' => 'Only users can place orders'
                ], 403);
            }

            $order = Orders::where('user_id', $user->id)->get();

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json(['data' => $order], 200);
        }catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }

    public function cancelOrders(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || $user->role->name !== 'user') {
            return response()->json([
                'message' => 'Only users can place orders'
            ], 403);
        }

        $order = Orders::findOrFail($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->user_id !== $user->id) {
            return response()->json([
                'message' => 'You can only cancel your own orders'
            ], 403);
        }

        if ($order->is_canceled) {
            return response()->json([
                'message' => 'This order is already canceled'
            ], 400);
        }

        $order->is_canceled = true;
        $order->save();

        $ticket = $order->ticket;

        $ticket->quantity += $order->quantity;
        $ticket->save();

        return response()->json([
            'message' => 'Order canceled successfully',
            'data' => $order
        ], 200);
    }

    public function destroyOrders($id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || $user->role->name !== 'user') {
            return response()->json([
                'message' => 'Only users can place orders'
            ], 403);
        }
        
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