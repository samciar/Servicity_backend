<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskerController extends Controller
{
    /**
     * Get tasker profile with reviews and completed tasks count
     */
    public function show($id)
    {
        // Get the tasker user
        $tasker = User::with(['skills', 'department', 'municipality'])
            ->where('id', $id)
            ->where('user_type', User::TYPE_TASKER)
            ->first();
            
        if (!$tasker) {
            return response()->json([
                'success' => false,
                'message' => 'Tasker not found'
            ], 404);
        }

        // Get reviews for this tasker
        $reviews = Review::with(['reviewer:id,name'])
            ->where('reviewee_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'client_name' => $review->reviewer->name,
                    'created_at' => $review->created_at
                ];
            });

        // Count completed tasks for this tasker
        $completedTasks = Booking::where('tasker_id', $id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'tasker' => $tasker,
                'reviews' => $reviews,
                'completed_tasks' => $completedTasks
            ]
        ]);
    }

    /**
     * Get all taskers with optional filters
     */
    public function index(Request $request)
    {
        $request->validate([
            'is_available' => 'sometimes|boolean',
            'id_verified' => 'sometimes|boolean',
            'with_skills' => 'sometimes|boolean',
            'min_rating' => 'sometimes|numeric|min:1|max:5',
            'search' => 'sometimes|string|min:2'
        ]);

        $query = User::where('user_type', User::TYPE_TASKER);

        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        if ($request->has('id_verified')) {
            $query->where('id_verified', $request->boolean('id_verified'));
        }

        if ($request->boolean('with_skills')) {
            $query->with(['skills']);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('bio', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('min_rating')) {
            $query->whereHas('reviewsReceived', function($q) use ($request) {
                $q->selectRaw('avg(rating) as average_rating')
                  ->having('average_rating', '>=', $request->min_rating);
            });
        }

        $taskers = $query->with(['skills', 'department', 'municipality'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $taskers
        ]);
    }
}
