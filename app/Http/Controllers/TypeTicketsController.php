<?php

namespace App\Http\Controllers;

use App\Models\TypeTickets;
use Illuminate\Http\Request;

class TypeTicketsController extends Controller
{
    public function getTypeTickets()
    {
        $type_tickets = TypeTickets::all();

        if ($type_tickets->isEmpty()) {
            return response()->json([
                'message' => 'TypeTickets not found'
            ], 404);
        }

        return response()->json($type_tickets, 200);
    }

    public function createTypeTickets(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type_ticket = TypeTickets::create([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'TypeTicket created successfully',
            'type_ticket' => $type_ticket
        ], 201);
    }

    public function getTypeTicketsById($id)
    { 
        $type_ticket = TypeTickets::findOrFail($id);

        if (!$type_ticket) {
            return response()->json([
                'message' => 'TypeTicket not found'
            ], 404);
        }

        return response()->json([
            'type_ticket' => $type_ticket
        ], 200);
    }


    // public function showTypeTicketDetails($id)
    // {
    //     $type_ticket = TypeTickets::with(['ticket'])->find($id);

    //     if ($type_ticket) {
    //         return response()->json([
    //             'type_ticket' => [
    //                 'id' => $type_ticket->id,
    //                 'name' => $type_ticket->name,
    //                 'created_at' => $type_ticket->created_at,
    //                 'updated_at' => $type_ticket->updated_at,
    //             ],
    //             'tickets' => $type_ticket->tickets->map(function ($ticket) {
    //                 return [
    //                     'id' => $ticket->id,
    //                     'name' => $ticket->name,
    //                     'quantity' => $ticket->quantity,
    //                     'price' => $ticket->price,
    //                     'created_at' => $ticket->created_at,
    //                     'updated_at' => $ticket->updated_at,
    //                     'deleted_at' => $ticket->deleted_at,
    //                 ];
    //             }),
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'TypeTicket not found'
    //     ], 404);
    // }

    // public function showTypeTicketOrders($id)
    // {
    //     $type_ticket = TypeTickets::with('orders')->find($id);

    //     if ($type_ticket) {
    //         return response()->json([
    //             'type_ticket' => [
    //                 'id' => $type_ticket->id,
    //                 'name' => $type_ticket->name,
    //                 'created_at' => $type_ticket->created_at,
    //                 'updated_at' => $type_ticket->updated_at,
    //             ],
    //             'orders' => $type_ticket->orders->map(function ($order) {
    //                 return [
    //                     'id' => $order->id,
    //                     'name' => $order->name,
    //                     'created_at' => $order->created_at,
    //                     'updated_at' => $order->updated_at,
    //                 ];
    //             }),
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'TypeTicket not found'
    //     ], 404);
    // }

    public function updateTypeTickets(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type_ticket = TypeTickets::findOrFail($id);
        if (!$type_ticket) {
            return response()->json([
                'message' => 'TypeTicket not found'
            ], 404);
        }
        
        $type_ticket->name = $validated['name'];
        $type_ticket->save();

        return response()->json([
            'message' => 'TypeTicket updated successfully',
            'type_ticket' => $type_ticket
        ], 200);
    }

    public function destroyTypeTickets($id)
    {
        $type_ticket = TypeTickets::findOrFail($id);

        if (!$type_ticket) {
            return response()->json([
                'message' => 'TypeTicket not found'
            ], 404);
        }

        $type_ticket->delete();

        return response()->json([
            'message' => 'TypeTicket deleted successfully'
        ], 200);
    }
}
