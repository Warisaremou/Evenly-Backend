<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

            return response()->json([
                'message' => 'User registered successfully',
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
               'message' => 'Authorization header missing'
            ], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);

        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid token or user not found',
                ], 401);
            }
            $userData = User::where('email', $user->email)->with('role')->first();

            if (!$userData) {
                return response()->json([
                    'message' => 'User not found in database',
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
                'message' => 'Failed to decode token',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $validated = $request->validate([
            'firstname' => 'required_if:is_Organizer,false|nullable|string|max:255',
            'lastname' => 'required_if:is_Organizer,false|nullable|string|max:255',
            'organizer_name' => 'required_if:is_Organizer,true|nullable|string|max:255',
        ]);

        if ($user->role && $user->role->name === 'organizer') {
            if (isset($validated['organizer_name'])) {
                $user->organizer_name = $validated['organizer_name'];
            }
        } else {
            if (isset($validated['firstname'])) {
                $user->firstname = $validated['firstname'];
            }
            if (isset($validated['lastname'])) {
                $user->lastname = $validated['lastname'];
            }
        }
        
        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
        
        return response()->json([
            'message' => 'Profile updated successfully',
        ], 200); 
    }

    public function deleteProfile(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        try {
            $user->delete(); 
            return response()->json([
                'message' => 'Your profile has been deleted successfully'
            ], 200);
        } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to delete profile',
            'error' => $e->getMessage()
        ], 500);
        }
    }

}