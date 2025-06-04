<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->boolean('with_skills')) {
            $query->withSkills();
        }

        if ($request->boolean('with_active_tasks')) {
            $query->withActiveTasks();
        }

        $categories = $query->with(['skills', 'tasks'])->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|image|max:2048',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('public/category-icons');
            $category->update(['icon_url' => Storage::url($path)]);
        }

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with(['skills', 'tasks'])->findOrFail($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'sometimes|nullable|string|max:500',
            'icon' => 'sometimes|nullable|image|max:2048',
        ]);

        $category->update([
            'name' => $validated['name'] ?? $category->name,
            'description' => $validated['description'] ?? $category->description,
        ]);

        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($category->icon_url) {
                $oldPath = str_replace('/storage', 'public', parse_url($category->icon_url, PHP_URL_PATH));
                Storage::delete($oldPath);
            }

            $path = $request->file('icon')->store('public/category-icons');
            $category->update(['icon_url' => Storage::url($path)]);
        }

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);

        // Delete associated icon if exists
        if ($category->icon_url) {
            $path = str_replace('/storage', 'public', parse_url($category->icon_url, PHP_URL_PATH));
            Storage::delete($path);
        }

        $category->delete();

        return response()->json(null, 204);
    }

    /**
     * Search categories by name
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $categories = Category::search($validated['query'])
            ->with(['skills', 'tasks'])
            ->get();

        return response()->json($categories);
    }

    /**
     * Get categories with skills
     */
    public function withSkills()
    {
        $categories = Category::withSkills()
            ->with(['skills', 'tasks'])
            ->get();

        return response()->json($categories);
    }

    /**
     * Get categories with active tasks
     */
    public function withActiveTasks()
    {
        $categories = Category::withActiveTasks()
            ->with(['skills', 'tasks'])
            ->get();

        return response()->json($categories);
    }
}
