<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'status' => ['sometimes', Rule::in([
                Task::STATUS_OPEN,
                Task::STATUS_ASSIGNED,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_COMPLETED,
                Task::STATUS_CANCELED,
                Task::STATUS_DISPUTED
            ])],
            'search' => 'sometimes|string|min:2',
            'urgent' => 'sometimes|boolean',
            'with_bids' => 'sometimes|boolean'
        ]);

        $query = Task::query()
            ->with(['client', 'category', 'skills']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->boolean('urgent')) {
            $query->urgent();
        }

        if ($request->boolean('with_bids')) {
            $query->with(['bids']);
        }

        $tasks = $query->latest()
            ->get();

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget_type' => ['required', Rule::in([Task::BUDGET_FIXED, Task::BUDGET_HOURLY])],
            'budget_amount' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'deadline_at' => 'nullable|date|after:preferred_date',
            'skill_ids' => 'sometimes|array',
            'skill_ids.*' => 'exists:skills,id'
        ]);

        $task = Task::create([
            'client_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget_type' => $validated['budget_type'],
            'budget_amount' => $validated['budget_amount'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'deadline_at' => $validated['deadline_at'] ?? null,
            'status' => Task::STATUS_OPEN
        ]);

        if (!empty($validated['skill_ids'])) {
            $task->skills()->sync($validated['skill_ids']);
        }

        return response()->json($task->load(['client', 'category', 'skills']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with(['client', 'category', 'skills', 'bids', 'booking', 'tasker'])
            ->findOrFail($id);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update', $task);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'budget_amount' => 'sometimes|numeric|min:0',
            'location' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'preferred_date' => 'sometimes|date|after_or_equal:today',
            'preferred_time' => 'sometimes|date_format:H:i',
            'deadline_at' => 'sometimes|date|after:preferred_date',
            'skill_ids' => 'sometimes|array',
            'skill_ids.*' => 'exists:skills,id'
        ]);

        $task->update($validated);

        if ($request->has('skill_ids')) {
            $task->skills()->sync($validated['skill_ids']);
        }

        return response()->json($task->load(['client', 'category', 'skills']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * Search tasks by title or description
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'category_id' => 'sometimes|exists:categories,id'
        ]);

        $query = Task::search($validated['query'])
            ->with(['client', 'category']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $tasks = $query->get();

        return response()->json($tasks);
    }

    /**
     * Get urgent tasks
     */
    public function urgent()
    {
        $tasks = Task::urgent()
            ->with(['client', 'category'])
            ->get();

        return response()->json($tasks);
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update-status', $task);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Task::STATUS_OPEN,
                Task::STATUS_ASSIGNED,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_COMPLETED,
                Task::STATUS_CANCELED,
                Task::STATUS_DISPUTED
            ])]
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json($task);
    }

    /**
     * Get tasks for current client
     */
    public function clientTasks()
    {
        $tasks = Task::where('client_id', Auth::id())
            ->with(['category', 'booking'])
            ->latest()
            ->get();

        return response()->json($tasks);
    }
}
