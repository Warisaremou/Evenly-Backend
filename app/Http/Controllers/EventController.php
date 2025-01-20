<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Events::all();
        return response()->json($events);

        if($events->isEmpty()){
            return response()->json([
                'message' => 'No events found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',    
            'title' => 'required|string|max:255',
            'date_time' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        $picturePath = null;
        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('images');
        }

        $event = Events::create([
            'title' => $validated['title'],
            'date_time' => $validated['date_time'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'picture' => $picturePath,  // Sauvegarder le chemin de l'image (ou null si aucune image)
        ]);

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
    }

    public function show($id)
    {
        $event = Events::findOrFail($id);
        return response()->json($event);
    }

    public function showCategories($id)
    {
        $event = Events::findOrFail($id);
        
        if($event){
            $categories = $event->categories;
            return response()->json($categories);
        } else {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'picture' => 'nullable',    
            'title' => 'required|string|max:255',
            'date_time' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $event = Events::findOrFail($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }
        
        $event-> picture = $validated['picture'] ?? $event->picture;
        $event-> title = $validated['title'];
        $event-> date_time = $validated['date_time'];
        $event-> location = $validated['location'];
        $event-> description = $validated['description'];

        $event->save();

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event
        ], 200);
    }

    public function destroy($id)
    {
        $event = Events::findOrFail($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ], 200);
    }

}
