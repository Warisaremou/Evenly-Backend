<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function getEvents()
    {
        $events = Events::all();
        
        if($events->isEmpty()){
            return response()->json([
                'message' => 'No events found'
            ], 404);
        }

        return response()->json($events, 200);
    }

    public function createEvents(Request $request)
    {
        // Log::info('Requête reçue', $request->all());

        $validated = $request->validate([
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',    
            'title' => 'required|string|max:255',
            'date_time' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'required|uuid|exists:users,id',
        ]);
        
        $coverPath = null;
        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('images');
        }

        $event = Events::create([
            'cover' => $coverPath,
            'title' => $validated['title'],
            'date_time' => $validated['date_time'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'user_id' => $validated['user_id'], 
        ]);

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
    }

    public function getEventsById($id)
    {
        $event = Events::findOrFail($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json($event, 200);
    }

    public function updateEvents(Request $request, $id)
    {

        $validated = $request->validate([
            'cover' => 'nullable',    
            'title' => 'required|string|max:255',
            'date_time' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        $event = Events::findOrFail($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }
        
        $event-> cover = $validated['cover'] ?? $event->cover;
        $event-> title = $validated['title'];
        $event-> date_time = $validated['date_time'];
        $event-> location = $validated['location'];
        $event-> description = $validated['description'];
        $event-> user_id = $validated['user_id'];

        $event->save();

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event
        ], 200);
    }

    public function destroyEvents($id)
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

    public function attachCategory(Request $request, $id)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $event = Events::findOrFail($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        $event->categories()->attach($validated['categories']);
        
        return response()->json([
            'message' => 'Category attached to event successfully',
            // 'event' => $event->load('categories')
        ], 200);
    }

    public function getCategories($id)
    {
        $event = Events::with('categories')->findOrFail($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json([
            'categories' => $event->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                ];
            }),
        ], 200);
    }
}
