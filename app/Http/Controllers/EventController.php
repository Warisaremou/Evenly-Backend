<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['getEvents', 'getEventsById']]);
    }

    public function getEvents()
    {
        $events = Events::all();

        return response()->json(data: $events, status: 200);
    }

    public function createEvents(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user || $user->role->name !== 'organizer') {
                return response()->json([
                    'error' => 'Only organizers can create events.',
                ], 403);
            }

            $validated = $request->validate([
                'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2000',
                'title' => 'required|string|max:255',
                'date_time' => 'required|date_format:Y-m-d H:i:s',
                'location' => 'required|string|max:255',
                'description' => 'required|string',
                'categories' => 'required|array',
                'categories.*' => 'exists:categories,id'
            ]);

            // Upload image to cloudinary
            $uploadedCoverUrl = cloudinary()->upload($request->file('cover')->getRealPath(), ['folder' => 'evenly', 'verify' => false])->getSecurePath();
            // dd($uploadedCoverUrl);

            $event = Events::create([
                'cover' => $uploadedCoverUrl,
                'title' => $validated['title'],
                'date_time' => $validated['date_time'],
                'location' => $validated['location'],
                'description' => $validated['description'],
                'user_id' => $user->id,
            ]);

            collect($validated['categories'])->map(function ($categoryId) use ($event) {
                $event->categories()->attach($categoryId);
            });

            return response()->json([
                'message' => 'Event created successfully',
                'data' => $event
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }

    public function getEventsById($id)
    {
        $event = Events::findOrFail($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json($event[0], 200);
    }

    public function updateEvents(Request $request, $id)
    {
        $user = $request->user();
        $event = Events::findOrFail($id);
        Gate::authorize('modify', $event);

        if ($user->role->name !== 'organizer') {
            return response()->json([
                'error' => 'Only organizers can update events.',
            ], 403);
        }

        $validated = $request->validate([
            'cover' => 'nullable',
            'title' => 'required|string|max:255',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // $event = Events::findOrFail($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        $event->cover = $validated['cover'] ?? $event->cover;
        $event->title = $validated['title'];
        $event->date_time = $validated['date_time'];
        $event->location = $validated['location'];
        $event->description = $validated['description'];
        $event->user_id = $user->id;

        $event->save();

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event
        ], 200);
    }

    public function destroyEvents(Request $request, $id)
    {
        $event = Events::findOrFail($id);
        Gate::authorize('modify', $event);

        $user = $request->user();
        if ($user->role->name !== 'organizer') {
            return response()->json([
                'error' => 'Only organizers can delete events.',
            ], 403);
        }

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

    public function getEventsByUser($id)
    {
        $user = User::with('events')->find($id);

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
                'events' => $user->events->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'cover' => $event->cover,
                        'title' => $event->title,
                        'date_time' => $event->date_time,
                        'location' => $event->location,
                        'created_at' => $event->created_at,
                        'updated_at' => $event->updated_at,
                    ];
                }),
            ], 200);
        }

        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    // public function attachCategory(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'categories' => 'required|array',
    //         'categories.*' => 'exists:categories,id',
    //     ]);

    //     $event = Events::findOrFail($id);

    //     if (!$event) {
    //         return response()->json([
    //             'message' => 'Event not found'
    //         ], 404);
    //     }

    //     $event->categories()->attach($validated['categories']);

    //     return response()->json([
    //         'message' => 'Category attached to event successfully',
    //         // 'event' => $event->load('categories')
    //     ], 200);
    // }

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
