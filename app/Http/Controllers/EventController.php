<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\Tickets;
use App\Models\TypeTickets;
use App\Models\User;
use App\Models\Orders;
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
        try {
            $events = Events::with('categories')->orderBy('created_at', 'desc')->get();

            return response()->json($events->map(function ($event) {
                return [
                    'id' => $event->id,
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
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }),
                ];
            }), 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
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
        try {
            $event = Events::with(['categories'])->findOrFail($id);
            if (!$event) {
                return response()->json([
                    'message' => 'Event not found',
                ], 404);
            }
            $eventTickets = Tickets::addSelect([
                'ticket_type_name' => TypeTickets::select('name')
                    ->whereColumn('id', 'tickets.type_ticket_id')
                    ->limit(1)
            ])->where('event_id', $id)->get();

            return response()->json([
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
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                }),
                'tickets' => $eventTickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'name' => $ticket->name,
                        'price' => $ticket->price,
                        'ticket_type_name' =>
                        $ticket->ticket_type_name,
                        'quantity' => $ticket->quantity
                    ];
                }),

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }

    public function updateEvents(Request $request, $id)
    {
        try {
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

            if ($event->user_id !== $user->id) {
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
                'title' => 'string|max:255',
                'date' => 'date_format:Y-m-d',
                'time' => 'date_format:H:i',
                'location' => 'string|max:255',
                'description' => 'string',
                'categories' => 'array',
                'categories.*' => 'exists:categories,id'
            ]);

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
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateCoverEvents(Request $request, $id)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user || $user->role->name !== 'organizer') {
                return response()->json([
                    'error' => 'Only organizers can update events.',
                ], 403);
            }

            $event = Events::find($id);

            if (!$event) {
                return response()->json([
                    'message' => 'Event not found'
                ], 404);
            }

            if (!$request->hasFile('cover')) {
                return response()->json([
                    'message' => 'Image not update'
                ], 400);
            }

            $request->validate([
                'cover' => 'mimes:jpeg,png,jpg,gif|max:2000'
            ]);

            $uploadedCoverUrl = cloudinary()->upload($request->file('cover')->getRealPath(), ['folder' => 'evenly', 'verify' => false])->getSecurePath();

            $event->cover = $uploadedCoverUrl;
            $event->save();

            return response()->json([
                'message' => 'Image update successfully',
                'cover_url' => $uploadedCoverUrl
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
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
