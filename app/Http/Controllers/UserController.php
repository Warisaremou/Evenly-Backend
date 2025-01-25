<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'No users found'
            ], 404);
        }

        return response()->json($users, 200);
    }

    public function createUsers(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|uuid|exists:roles,id',
            'organizer_name' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'organizer_name' => $validated['organizer_name'],
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function getUsersById($id)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json($user, 200);

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


    public function updateUsers(Request $request, $id)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|uuid|exists:roles,id',
            'organizer_name' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->firstname = $validated['firstname'];
        $user->lastname = $validated['lastname'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role_id = $validated['role_id'];
        $user->organizer_name = $validated['organizer_name'];
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }

}