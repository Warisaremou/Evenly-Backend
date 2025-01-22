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

    public function showTicketDetails($id)
    {
        $ticket = Tickets::with(['type_ticket', 'event', 'user'])->find($id);

        if ($ticket) {
            return response()->json([
                'ticket' => [
                    'id' => $ticket->id,
                    'name' => $ticket->name,
                    'quantity' => $ticket->quantity,
                    'price' => $ticket->price,
                    'user_id' => $ticket->user_id,
                    'event_id' => $ticket->event_id,
                    'type_ticket_id' => $ticket->type_ticket_id,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'deleted_at' => $ticket->deleted_at,
                ],
                'type_ticket' => [
                    'id' => $ticket->type_ticket->id,
                    'name' => $ticket->type_ticket->name,
                    'created_at' => $ticket->type_ticket->created_at,
                    'updated_at' => $ticket->type_ticket->updated_at,
                ],
                'event' => [
                    'id' => $ticket->event->id,
                    'title' => $ticket->event->title,
                    'date_time' => $ticket->event->date_time,
                    'location' => $ticket->event->location,
                    'description' => $ticket->event->description,
                    'picture' => $ticket->event->picture,
                    'created_at' => $ticket->event->created_at,
                    'updated_at' => $ticket->event->updated_at,
                ],
                'user' => [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                    'email' => $ticket->user->email,
                    'email_verified_at' => $ticket->user->email_verified_at,
                    'created_at' => $ticket->user->created_at,
                    'updated_at' => $ticket->user->updated_at,
                ],
            ]);
        }else{
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }
    }

    public function getOders($id)
    {
        $ticket = Tickets::with('orders')->find($id);

        if ($ticket) {
            return response()->json([
                'ticket' => [
                    'id' => $ticket->id,
                    'name' => $ticket->name,
                    'quantity' => $ticket->quantity,
                    'price' => $ticket->price,
                    'user_id' => $ticket->user_id,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'deleted_at' => $ticket->deleted_at,
                ],
                'orders' => $ticket->orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'name' => $order->name,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ];
                }),
            ]);
        } else {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }
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
