<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['getAllCategories', 'getCategoriesById']]);
    }

    public function getAllCategories()
    {
        $categories = Categories::all();

        return response()->json(
            $categories,
            200
        );
    }

    public function createCatergories(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user || $user->role->name !== 'organizer') {
                return response()->json([
                    'message' => 'Only organizers can add categories.',
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'events' => 'required|array',
                'events.*' => 'exists:events,id'
            ]);

            $category = Categories::create(['name' => $validated['name']]);

            collect($validated['events'])->map(function ($eventId) use ($category) {
                $category->events()->attach($eventId);
            });

            return response()->json([
                'message' => 'Category added successfully',
                'data' => $category
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }

    public function getCategoriesById($id)
    {
        $category = Categories::with('events')->find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'data' => [
                'name' => $category->name,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
                'categories' => $category->events->map(function ($event) {
                    return [
                        'cover' => $event->cover,
                        'title' => $event->title,
                        'date' => $event->date,
                        'time' => $event->time,
                        'location' => $event->location,
                        'description' => $event->description,
                        'created_at' => $event->created_at,
                        'updated_at' => $event->updated_at,
                    ];
                }),
            ],
        ], 200);
    }

    public function updateCategories(Request $request, $id)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user || $user->role->name !== 'organizer') {
                return response()->json([
                    'message' => 'Only organizers can update categories.',
                ], 403);
            }

            $category = Categories::findOrFail($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'events' => 'required|array',
                'events.*' => 'exists:events,id'
            ]);

            $category->name = $validated['name'];
            $category->events()->sync($validated['events']);
            $category->save();

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyCategories($id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || $user->role->name !== 'organizer') {
            return response()->json([
                'message' => 'Only organizers can delete categories.',
            ], 403);
        }

        $category = Categories::findOrFail($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
