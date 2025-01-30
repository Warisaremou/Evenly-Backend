<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use App\Models\User;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function getTickets()
    {
        $tickets = Tickets::paginate(10);
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

    public function getTicketsByUser($id)
    {
        $user = User::with('tickets')->find($id);

        if ($user) {
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'role' => $user->role,
                    'organizer_name' => $user->organizer_name,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'tickets' => $user->tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'name' => $ticket->name,
                        'quantity' => $ticket->quantity,
                        'price' => $ticket->price,
                        'event_id' => $ticket->event_id,
                        'type_ticket_id' => $ticket->type_ticket_id,
                        'created_at' => $ticket->created_at,
                        'updated_at' => $ticket->updated_at,
                    ];
                }),
            ], 200);
        }

        return response()->json([
            'message' => 'User not found'
        ], 404);
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
