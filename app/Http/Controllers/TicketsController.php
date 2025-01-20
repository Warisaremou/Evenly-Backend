<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index()
    {
        $tickets = Tickets::all();
        return response()->json($tickets);

        if (!$tickets) {
            return response()->json([
                'message' => 'Tickets not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'user_id' => 'required|uuid|exists:users,id',
            'event_id' => 'required|uuid|exists:events,id',
            'type_ticket_id' => 'required|uuid|exists:type_tickets,id',  
        ]);

        $ticket = Tickets::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'user_id' => $validated['user_id'],
            'event_id' => $validated['event_id'],
            'type_ticket_id' => $validated['type_ticket_id'],
        ]);

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => $ticket
        ], 201);
    }

    public function show($id)
    {
        $ticket = Tickets::findOrFail($id);
        return response()->json($ticket);
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
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
        $ticket->price = $validated['price'];
        $ticket->quantity = $validated['quantity'];
        $ticket->user_id = $validated['user_id'];
        $ticket->event_id = $validated['event_id'];
        $ticket->type_ticket_id = $validated['type_ticket_id'];
        $ticket->save();

        return response()->json([
            'message' => 'Ticket updated successfully',
            'ticket' => $ticket
        ]);
    }

    public function destroy($id)
    {
        $ticket = Tickets::findOrFail($id);

        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }
    }
}
