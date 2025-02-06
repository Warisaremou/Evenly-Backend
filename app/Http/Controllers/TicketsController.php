<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use App\Models\TypeTickets;
use Exception;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function getTickets()
    {
        $tickets = Tickets::all();
        return response()->json($tickets, 200);
    }

    public function addTickets(Request $request)
    {
        try {

            if ($request->user()->role->name !== 'organizer') {
                return response()->json([
                    'error' => 'Only organizers can add tickets.',
                ], 403);
            }

            $id = $request->user()->id;

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'quantity' => 'required|numeric',
                'price' => 'required|numeric',
                'event_id' => 'required|uuid|exists:events,id',
                'type_ticket_id' => 'required|uuid|exists:type_tickets,id',
            ]);
            // dd($validated);

            $ticket = Tickets::create([
                'name' => $validated['name'],
                'quantity' => $validated['quantity'],
                'price' => $validated['price'],
                'user_id' => $id,
                'event_id' => $validated['event_id'],
                'type_ticket_id' => $validated['type_ticket_id'],
            ]);

            return response()->json([
                'message' => 'Ticket added successfully',
                'data' => $ticket
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }


    public function getTicketsById($id)
    {
        $ticket = Tickets::findOrFail($id);

        return response()->json($ticket, 200);
    }

    public function getTicketsByOrganizer(Request $request)
    {
        try {

            if ($request->user()->role->name !== 'organizer') {
                return response()->json([
                    'error' => 'Only organizers can access their tickets.',
                ], 403);
            }

            $id = $request->user()->id;
            $organizerTickets = Tickets::addSelect([
                'ticket_type_name' => TypeTickets::select('name')
                    ->whereColumn('id', 'tickets.type_ticket_id')
                    ->limit(1)
            ])->where('user_id', $id)->get();

            return response()->json(
                $organizerTickets,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }

    public function getTicketsByEvent(Request $request, $id)
    {
        try {

            if ($request->user()->role->name !== 'organizer') {
                return response()->json([
                    'error' => 'Only organizers can access their event tickets.',
                ], 403);
            }

            $eventTickets = Tickets::where('event_id', $id)->get();

            return response()->json(
                $eventTickets,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }

    public function updateTickets(Request $request, $id)
    {
        try {
            if ($request->user()->role->name !== 'organizer') {
                return response()->json([
                    'error' => 'Only organizers can add tickets.',
                ], 403);
            }

            $userID = $request->user()->id;

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'quantity' => 'required|numeric',
                'price' => 'required|numeric',
                'type_ticket_id' => 'required|uuid|exists:type_tickets,id',
            ]);

            $ticket = Tickets::findOrFail($id);

            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket not found'
                ], 404);
            }

            $isTicketOwner = Tickets::where('user_id', $userID)->firstOrFail();

            if (!$isTicketOwner) {
                return response()->json([
                    'message' => 'Only ticket owner can update ticket',
                ], 403);
            };

            // Update Ticket
            $ticket->name = $validated['name'];
            $ticket->quantity = $validated['quantity'];
            $ticket->price = $validated['price'];
            $ticket->type_ticket_id = $validated['type_ticket_id'];
            $ticket->save();

            return response()->json([
                'message' => 'Ticket updated successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }

    public function removeTicket(Request $request, $id)
    {
        try {
            if ($request->user()->role->name !== 'organizer') {
                return response()->json([
                    'error' => 'Only organizers can add tickets.',
                ], 403);
            }

            $userID = $request->user()->id;

            $ticket = Tickets::findOrFail($id);

            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket not found'
                ], 404);
            }

            $isTicketOwner = Tickets::where('user_id', $userID)->firstOrFail();

            if (!$isTicketOwner) {
                return response()->json([
                    'message' => 'Only ticket owner can remove ticket',
                ], 403);
            };

            $ticket->delete();

            return response()->json([
                'message' => 'Ticket deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }
}
