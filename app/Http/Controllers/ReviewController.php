<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'booking_id' => 'sometimes|exists:bookings,id',
            'min_rating' => 'sometimes|integer|min:1|max:5'
        ]);

        $query = Review::query()
            ->with(['booking', 'reviewer', 'reviewee']);

        if ($request->has('user_id')) {
            $query->forUser($request->user_id);
        }

        if ($request->has('booking_id')) {
            $query->where('booking_id', $request->booking_id);
        }

        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        $reviews = $query->latest()
            ->get();

        return response()->json($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);
        $this->authorize('create-review', $booking);

        // Determine reviewer and reviewee based on booking
        $reviewerId = Auth::id();
        $revieweeId = ($booking->client_id === $reviewerId) 
            ? $booking->tasker_id 
            : $booking->client_id;

        $review = Review::create([
            'booking_id' => $booking->id,
            'reviewer_id' => $reviewerId,
            'reviewee_id' => $revieweeId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null
        ]);

        return response()->json($review->load(['booking', 'reviewer', 'reviewee']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $review = Review::with(['booking', 'reviewer', 'reviewee'])->findOrFail($id);
        $this->authorize('view', $review);

        return response()->json($review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string|max:500'
        ]);

        $review->update($validated);

        return response()->json($review->load(['booking', 'reviewer', 'reviewee']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json(null, 204);
    }

    /**
     * Get reviews for a specific user
     */
    public function forUser(string $userId)
    {
        $reviews = Review::forUser($userId)
            ->with(['booking', 'reviewer'])
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    /**
     * Get reviews by a specific user
     */
    public function byUser(string $userId)
    {
        $reviews = Review::byUser($userId)
            ->with(['booking', 'reviewee'])
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    /**
     * Get average rating for a user
     */
    public function averageRating(string $userId)
    {
        $average = Review::averageForUser($userId);
        $count = Review::countForUser($userId);

        return response()->json([
            'average_rating' => round($average, 2),
            'review_count' => $count
        ]);
    }

    /**
     * Get high-rated reviews for a user
     */
    public function highRated(string $userId, int $threshold = 4)
    {
        $reviews = Review::forUser($userId)
            ->highRating($threshold)
            ->with(['booking', 'reviewer'])
            ->latest()
            ->get();

        return response()->json($reviews);
    }
}
