<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fisrtname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|uuid|exists:roles,id',
            'organizer_name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'firstname' => $validated['name'],
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

    public function show($id)
    {
        $user = User::with(['role', 'ticket'])->find($id);

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
                        'created_at' => $ticket->created_at,
                        'updated_at' => $ticket->updated_at,
                        'deleted_at' => $ticket->deleted_at,
                    ];
                }),
            ]);
        }

        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    public function getUserOrders($id)
    {
        $user = User::with('orders')->find($id);

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
                'orders' => $user->orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'name' => $order->name,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ];
                }),
            ]);
        }

        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|uuid|exists:roles,id',
            'organizer_name' => 'required|string|max:255',
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
        ]);
    }

}