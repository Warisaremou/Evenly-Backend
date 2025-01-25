<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function getTickets()
    {
        $tickets = Tickets::all()->paginate(10);
        
        if ($tickets->isEmpty()) {
            return response()->json([
                'message' => 'Tickets not found'
            ], 404);
        }

        return response()->json($tickets, 200);
    }

    public function createTickets(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
            'user_id' => 'required|uuid|exists:users,id',
            'event_id' => 'required|uuid|exists:events,id',
            'type_ticket_id' => 'required|uuid|exists:type_tickets,id',  
        ]);

        $ticket = Tickets::create([
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'user_id' => $validated['user_id'],
            'event_id' => $validated['event_id'],
            'type_ticket_id' => $validated['type_ticket_id'],
        ]);

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => $ticket
        ], 201);
    }


    public function getTicketsById($id)
    {
        $ticket = Tickets::findOrFail($id);

        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }

        return response()->json($ticket, 200);
    }

    public function updateTickets(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
            'user_id' => 'required|uuid|exists:users,id',
            'event_id' => 'required|uuid|exists:events,id',
            'type_ticket_id' => 'required|uuid|exists:type_tickets,id',  
        ]);

        $ticket = Tickets::findOrFail($id);

        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }
        
        $ticket->name = $validated['name'];
        $ticket->quantity = $validated['quantity'];
        $ticket->price = $validated['price'];
        $ticket->user_id = $validated['user_id'];
        $ticket->event_id = $validated['event_id'];
        $ticket->type_ticket_id = $validated['type_ticket_id'];
        $ticket->save();

        return response()->json([
            'message' => 'Ticket updated successfully',
            'ticket' => $ticket,
        ], 200);
    }

    public function destroyTickets($id)
    {
        $ticket = Tickets::findOrFail($id);

        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted successfully',
        ], 200);
    }
}
