<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Categories::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Categories::create([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = Categories::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Categories::findOrFail($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
        
        $category->name = $validated['name'];
        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Categories::findOrFail($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    public function attachEvent(Request $request, $id)
    {
        $validated = $request->validate([
            'events' => 'required|array',
            'events.*' => 'exists:events,id',
        ]);

        $category = Categories::findOrFail($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->events()->attach($validated['events']);

        return response()->json([
            'message' => 'Event attached to category successfully',
            'category' => $category
        ]);
    }

    public function getEvents($id)
    {
        $category = Categories::with('events')->findOrFail($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'events' => $category->events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'picture' => $event->picture,
                    'title' => $event->title,
                    'date_time' => $event->date_time,
                    'location' => $event->location,
                    'description' => $event->description,
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at,
                ];
            }),
        ]);
    }
}
