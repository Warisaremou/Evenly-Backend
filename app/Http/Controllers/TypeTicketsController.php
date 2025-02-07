<?php

namespace App\Http\Controllers;

use App\Models\TypeTickets;
use Illuminate\Http\Request;

class TypeTicketsController extends Controller
{
    public function getTypeTickets()
    {
        $type_tickets = TypeTickets::all();

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
            'data' => $type_ticket
        ], 201);
    }

    public function updateTypeTickets(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type_ticket = TypeTickets::findOrFail($id);

        $type_ticket->name = $validated['name'];
        $type_ticket->save();

        return response()->json([
            'message' => 'TypeTicket updated successfully',
            'data' => $type_ticket
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
