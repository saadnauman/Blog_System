<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('pages.admin.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);
        Category::create(['name' => $request->name]);
        return back()->with('status', 'Category added!');
    }

    public function update(Request $request, Category $category)
    {
try {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return back()->with('status', 'Category updated!');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Send a custom message like a success one, but for error
        return back()->with('error', 'Category name must be unique and properly filled.');
    }
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('status', 'Category deleted!');
    }
} 