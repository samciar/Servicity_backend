<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'search' => 'sometimes|string|min:2',
            'with_taskers' => 'sometimes|boolean'
        ]);

        $query = Skill::query()
            ->with(['category']);

        if ($request->has('category_id')) {
            $query->fromCategory($request->category_id);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->boolean('with_taskers')) {
            $query->withTaskers()
                ->with(['taskers']);
        }

        $skills = $query->latest()
            ->get();

        return response()->json($skills);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:skills',
            'description' => 'nullable|string|max:500'
        ]);

        $skill = Skill::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);

        return response()->json($skill->load(['category']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $skill = Skill::with(['category', 'taskers'])->findOrFail($id);
        return response()->json($skill);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $skill = Skill::findOrFail($id);
        $this->authorize('update', $skill);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('skills')->ignore($skill->id)],
            'description' => 'sometimes|nullable|string|max:500'
        ]);

        $skill->update($validated);

        return response()->json($skill->load(['category']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $skill = Skill::findOrFail($id);
        $this->authorize('delete', $skill);

        $skill->delete();

        return response()->json(null, 204);
    }

    /**
     * Search skills by name
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'category_id' => 'sometimes|exists:categories,id'
        ]);

        $query = Skill::search($validated['query'])
            ->with(['category']);

        if ($request->has('category_id')) {
            $query->fromCategory($request->category_id);
        }

        $skills = $query->get();

        return response()->json($skills);
    }

    /**
     * Get skills by category
     */
    public function byCategory(string $categoryId)
    {
        $skills = Skill::fromCategory($categoryId)
            ->with(['category'])
            ->get();

        return response()->json($skills);
    }

    /**
     * Get skills with taskers
     */
    public function withTaskers()
    {
        $skills = Skill::withTaskers()
            ->with(['category', 'taskers'])
            ->get();

        return response()->json($skills);
    }

    /**
     * Get average proficiency for a skill
     */
    public function averageProficiency(string $id)
    {
        $skill = Skill::findOrFail($id);
        $average = $skill->averageProficiency();

        return response()->json([
            'skill_id' => $skill->id,
            'skill_name' => $skill->name,
            'average_proficiency' => round($average, 2)
        ]);
    }
}
