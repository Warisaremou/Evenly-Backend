<?php

namespace App\Http\Controllers;

use App\Models\TypeTickets;
use Illuminate\Http\Request;

class TypeTicketsController extends Controller
{
    public function index()
    {
        $typeTickets = TypeTickets::all();
        return response()->json($typeTickets);

        if (!$typeTickets) {
            return response()->json([
                'message' => 'TypeTickets not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $typeTicket = TypeTickets::create([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'TypeTicket created successfully',
            'typeTicket' => $typeTicket
        ], 201);
    }

    public function show($id)
    { 
        $typeTicket = TypeTickets::with('tickets')->find($id);

        if ($typeTicket) {
            return response()->json([
                'type_ticket' => [
                    'id' => $typeTicket->id,
                    'name' => $typeTicket->name,
                    'created_at' => $typeTicket->created_at,
                    'updated_at' => $typeTicket->updated_at,
                ],
                'tickets' => $typeTicket->tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'name' => $ticket->name,
                        'quantity' => $ticket->quantity,
                        'price' => $ticket->price,
                        'user_id' => $ticket->user_id,
                        'event_id' => $ticket->event_id,
                        'created_at' => $ticket->created_at,
                        'updated_at' => $ticket->updated_at,
                        'deleted_at' => $ticket->deleted_at,
                    ];
                }),
            ]);
        } else {
            return response()->json([
                'message' => 'Type de ticket introuvable.'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $typeTicket = TypeTickets::findOrFail($id);
        if (!$typeTicket) {
            return response()->json([
                'message' => 'TypeTicket not found'
            ], 404);
        }
        
        $typeTicket->name = $validated['name'];
        $typeTicket->save();

        return response()->json([
            'message' => 'TypeTicket updated successfully',
            'typeTicket' => $typeTicket
        ]);
    }

    public function destroy($id)
    {
        $typeTicket = TypeTickets::findOrFail($id);

        if (!$typeTicket) {
            return response()->json([
                'message' => 'TypeTicket not found'
            ], 404);
        }

        $typeTicket->delete();

        return response()->json([
            'message' => 'TypeTicket deleted successfully'
        ]);
    }
}
