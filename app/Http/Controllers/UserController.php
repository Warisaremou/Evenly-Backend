<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function registerUsers(Request $request)
    {
        $validated = $request->validate([
            'is_Organizer' => 'required|boolean',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'firstname' => 'required_if:is_Organizer,false|string|max:255',
            'lastname' => 'required_if:is_Organizer,false|string|max:255',
            'organizer_name' => 'required_if:is_Organizer,true|string|max:255',
        ]);

        try {
            
            $roleName = $validated['is_Organizer'] ? 'organizer' : 'user';
            $roleId = Roles::where('name', $roleName)->first()->id;

            $userData = [
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $roleId,
            ];

            if ($validated['is_Organizer']) {
                $userData['organizer_name'] = $validated['organizer_name'];
            } else {
                $userData['firstname'] = $validated['firstname'];
                $userData['lastname'] = $validated['lastname'];
            }

            $user = User::create($userData);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ], 201);     
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'User registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function loginUsers(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token');

        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token->plainTextToken
        ], 200);
    }

    public function getProfile(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader) {
            return response()->json([
               'error' => 'Authorization header missing'
            ], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);

        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'error' => 'Invalid token or user not found',
                ], 401);
            }
            $userData = User::where('email', $user->email)->with('role')->first();

            if (!$userData) {
                return response()->json([
                    'error' => 'User not found in database',
                ], 404);
            }

            return response()->json([
                    'firstname' => $userData->firstname,
                    'lastname' => $userData->lastname,
                    'email' => $userData->email,
                    'organizer_name' => $userData->organizer_name, 
                    'role' => $userData->role->name, 
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to decode token',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    // public function getUsersById($id)
    // {
    //     $user = User::findOrFail($id);

    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     return response()->json($user, 200);

    // }

    
    // public function updateUsers(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'firstname' => 'required|string|max:255',
    //         'lastname' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:8',
    //         'role_id' => 'required|uuid|exists:roles,id',
    //         'organizer_name' => 'nullable|string|max:255',
    //     ]);

    //     $user = User::findOrFail($id);

    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     $user->firstname = $validated['firstname'];
    //     $user->lastname = $validated['lastname'];
    //     $user->email = $validated['email'];
    //     $user->password = Hash::make($validated['password']);
    //     $user->role_id = $validated['role_id'];
    //     $user->organizer_name = $validated['organizer_name'];
    //     $user->save();

    //     return response()->json([
    //         'message' => 'User updated successfully',
    //         'user' => $user
    //     ], 200);
    // }

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