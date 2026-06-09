<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-product');

        $categories = Category::orderBy('name')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('manage-product');

        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-product');

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'brand' => ['nullable', 'string', 'max:255'],
        ]);

        Category::create([
            'name' => $request->name,
            'brand' => $request->brand,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        Gate::authorize('manage-product');

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        Gate::authorize('manage-product');

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'brand' => ['nullable', 'string', 'max:255'],
        ]);

        $category->update([
            'name' => $request->name,
            'brand' => $request->brand,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        Gate::authorize('manage-product');

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
