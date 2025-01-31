<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['getAllEvents', 'getEventsDetails']]);
    }

    public function getAllEvents()
    {
        $events = Events::with('categories')->get();

        return response()->json([
            'data' => $events->map(function ($event) {
                return [
                    'cover' => $event->cover,
                    'title' => $event->title,
                    'date' => $event->date,
                    'time' => $event->time,
                    'location' => $event->location,
                    'description' => $event->description,
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at,
                    'categories' => $event->categories->map(function ($category) {
                        return [
                            'name' => $category->name,
                            'created_at' => $category->created_at,
                            'updated_at' => $category->updated_at,
                        ];
                    }),
                ];
            }),
        ], 200);
    }

    public function createEvents(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user || $user->role->name !== 'organizer') {
                return response()->json([
                    'message' => 'Only organizers can create events.',
                ], 403);
            }

            $validated = $request->validate([
                'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2000',
                'title' => 'required|string|max:255',
                'date' => 'required|date_format:Y-m-d',
                'time' => 'required|date_format:H:i',
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
                'date' => $validated['date'],
                'time' => $validated['time'],
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

    public function getEventsDetails($id)
    {
        $event = Events::with(['categories', 'user'])->find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Événement non trouvé'
            ], 404);
        }

        return response()->json([
            'data' => [
                'cover' => $event->cover,
                'title' => $event->title,
                'date' => $event->date,
                'time' => $event->time,
                'location' => $event->location,
                'description' => $event->description,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
                'organizer_name' => $event->user->organizer_name,
                'categories' => $event->categories->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'created_at' => $category->created_at,
                        'updated_at' => $category->updated_at,
                    ];
                }),
            ],
        ], 200);;
    }

    public function updateEvents(Request $request, $id)
    {
        dd([
            'all_data' => $request->all(),
            'json' => $request->json()->all(),
            'input' => $request->input(),
            'files' => $request->file()
        ]);

        try{
            $user = Auth::guard('sanctum')->user();
        
            if (!$user || $user->role->name !== 'organizer') {
                return response()->json([
                    'error' => 'Only organizers can update events.',
                ], 403);
            }

            $event = Events::findOrFail($id);

            if (!$event) {
                return response()->json([
                    'message' => 'Event not found'
                ], 404);
            }

            if ($event->user_id !== $user->id ) {
                return response()->json([
                    'message' => "Only event owner is authorized",
                ], 403);
            }

            if (!$request->all()) {
                return response()->json([
                    'message' => 'Request is empty. Make sure you are sending valid JSON or form-data.'
                ], 400);
            }

            $validated = $request->validate([
                // 'cover' => 'nullable|mimes:jpeg,png,jpg,gif|max:2000',
                'title' => 'string|max:255',
                'date' => 'date_format:Y-m-d',
                'time' => 'date_format:H:i',
                'location' => 'string|max:255',
                'description' => 'string',
                'categories' => 'array',
                'categories.*' => 'exists:categories,id'
            ]);
            
            if ($request->hasFile('cover')) {
                $uploadedCoverUrl = cloudinary()->upload($request->file('cover')->getRealPath(), ['folder' => 'evenly', 'verify' => false])->getSecurePath();
                $event->cover = $uploadedCoverUrl;
            }

            // $event->cover = $validated['cover'] ?? $event->cover;
            $event->title = $validated['title'];
            $event->date = $validated['date'];
            $event->time = $validated['time'];
            $event->location = $validated['location'];
            $event->description = $validated['description'];

            $event->categories()->sync($validated['categories']);

            $event->save();

            return response()->json([
                'message' => 'Event updated successfully',
                'data' => $event
            ], 200);
        }catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                // 'errors' => $e,
                // 'req' => $request
            ], 500);
        }
        // dd($request->all());
    }

    public function destroyEvents(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || $user->role->name !== 'organizer') {
            return response()->json([
                'message' => 'Only organizers can delete events.',
            ], 403);
        }
        
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

    public function getEventsByOrganizer($id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || $user->role->name !== 'organizer') {
            return response()->json([
                'message' => 'Only organizers can view events.',
            ], 403);
        }

        $events = Events::with('categories')->where('user_id', $user->id)->get();

        if ($events->isEmpty()) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json([
            'organizer_name' => $user->name,
            'events' => $events->map(function ($event) {
                return [
                    'cover' => $event->cover,
                    'title' => $event->title,
                    'date' => $event->date,
                    'time' => $event->time,
                    'location' => $event->location,
                    'description' => $event->description,
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at,
                    'categories' => $event->categories->map(function ($category) {
                        return [
                            'name' => $category->name,
                            'created_at' => $category->created_at,
                            'updated_at' => $category->updated_at,
                        ];
                    }),
                ];
            }),
        ], 200);

    }
}
