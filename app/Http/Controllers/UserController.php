<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'user_type' => ['sometimes', Rule::in([
                User::TYPE_CLIENT,
                User::TYPE_TASKER,
                User::TYPE_ADMIN
            ])],
            'is_available' => 'sometimes|boolean',
            'id_verified' => 'sometimes|boolean',
            'with_skills' => 'sometimes|boolean',
            'search' => 'sometimes|string|min:2'
        ]);

        $query = User::query();

        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        if ($request->has('id_verified')) {
            $query->where('id_verified', $request->boolean('id_verified'));
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        if ($request->boolean('with_skills')) {
            $query->with(['skills']);
        }

        $users = $query->latest()
            ->get();

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'user_type' => ['required', Rule::in([
                User::TYPE_CLIENT,
                User::TYPE_TASKER,
                User::TYPE_ADMIN
            ])],
            'profile_picture_url' => 'nullable|url',
            'bio' => 'nullable|string|max:500',
            'hourly_rate' => 'nullable|numeric|min:0',
            'skill_ids' => 'sometimes|array',
            'skill_ids.*' => 'exists:skills,id'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'user_type' => $validated['user_type'],
            'profile_picture_url' => $validated['profile_picture_url'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'is_available' => $validated['user_type'] === User::TYPE_TASKER,
            'id_verified' => false
        ]);

        if (!empty($validated['skill_ids']) && $user->isTasker()) {
            $user->skills()->sync($validated['skill_ids']);
        }

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with([
            'skills',
            'clientTasks',
            'assignedTasks',
            'taskerBookings',
            'clientBookings',
            'bids',
            'paymentsMade',
            'paymentsReceived',
            'reviewsGiven',
            'reviewsReceived'
        ])->findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8',
            'phone_number' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'profile_picture_url' => 'sometimes|nullable|url',
            'bio' => 'sometimes|nullable|string|max:500',
            'hourly_rate' => 'sometimes|nullable|numeric|min:0',
            'is_available' => 'sometimes|boolean',
            'id_verified' => 'sometimes|boolean',
            'skill_ids' => 'sometimes|array',
            'skill_ids.*' => 'exists:skills,id'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        if ($request->has('skill_ids') && $user->isTasker()) {
            $user->skills()->sync($validated['skill_ids']);
        }

        return response()->json($user->load(['skills']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(null, 204);
    }

    /**
     * Get all clients
     */
    public function clients()
    {
        $clients = User::clients()
            ->latest()
            ->get();

        return response()->json($clients);
    }

    /**
     * Get all taskers
     */
    public function taskers(Request $request)
    {
        $request->validate([
            'is_available' => 'sometimes|boolean',
            'id_verified' => 'sometimes|boolean',
            'with_skills' => 'sometimes|boolean',
            'min_rating' => 'sometimes|numeric|min:1|max:5'
        ]);

        $query = User::taskers();

        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        if ($request->has('id_verified')) {
            $query->where('id_verified', $request->boolean('id_verified'));
        }

        if ($request->boolean('with_skills')) {
            $query->with(['skills']);
        }

        if ($request->has('min_rating')) {
            $query->whereHas('reviewsReceived', function($q) use ($request) {
                $q->selectRaw('avg(rating) as average_rating')
                  ->having('average_rating', '>=', $request->min_rating);
            });
        }

        $taskers = $query->latest()
            ->get();

        return response()->json($taskers);
    }

    /**
     * Get all admins
     */
    public function admins()
    {
        $admins = User::admins()
            ->latest()
            ->get();

        return response()->json($admins);
    }

    /**
     * Get current authenticated user
     */
    public function me()
    {
        $user = Auth::user();
        return response()->json($user->load([
            'skills',
            'clientTasks',
            'assignedTasks',
            'taskerBookings',
            'clientBookings'
        ]));
    }

    /**
     * Update current user's profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'profile_picture_url' => 'sometimes|nullable|url',
            'bio' => 'sometimes|nullable|string|max:500',
            'hourly_rate' => 'sometimes|nullable|numeric|min:0',
            'is_available' => 'sometimes|boolean',
            'skill_ids' => 'sometimes|array',
            'skill_ids.*' => 'exists:skills,id'
        ]);

        $user->update($validated);

        if ($request->has('skill_ids') && $user->isTasker()) {
            $user->skills()->sync($validated['skill_ids']);
        }

        return response()->json($user->load(['skills']));
    }
}
