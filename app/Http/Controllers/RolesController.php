<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function getRoles()
    {
        $roles = Roles::all();

        if (!$roles) {
            return response()->json([
                'message' => 'Roles not found'
            ], 404);
        }
        return response()->json(['data' => $roles], 200);
    }

    public function getRolesById($id)
    {
        $roles = Roles::findOrFail($id);

        if (!$roles) {
            return response()->json([
                'message' => 'Roles not found'
            ], 404);
        }

        return response()->json(['data' => $roles], 200);
    }
}
