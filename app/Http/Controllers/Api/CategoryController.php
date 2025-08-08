<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function store(Request $request)
    {
            $user = $request->user();

        if (! $user->hasRole('admin')) {
        return response()->json([
            'status'  => 'error',
            'message' => 'You are unauthorized to create a category.'
        ], 403); // 403 Forbidden
    }
        $validated = $request->validate([
            'name' => 'required|string|unique:categories',
        ]);

        $category = Category::create($validated);
        return response()->json($category);
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
