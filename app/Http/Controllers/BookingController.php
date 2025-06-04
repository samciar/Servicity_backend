<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['task', 'tasker', 'client'])
            ->where(function($query) {
                $query->where('tasker_id', Auth::id())
                    ->orWhere('client_id', Auth::id());
            })
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'agreed_price' => 'required|numeric|min:0',
            'start_time' => 'required|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $task = Task::findOrFail($validated['task_id']);
        $this->authorize('create-booking', $task);

        $booking = Booking::create([
            ...$validated,
            'tasker_id' => $task->assigned_tasker_id,
            'client_id' => $task->user_id,
            'status' => Booking::STATUS_SCHEDULED,
            'payment_status' => Booking::PAYMENT_PENDING
        ]);

        return response()->json($booking, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['task', 'tasker', 'client', 'payments', 'review'])->findOrFail($id);
        $this->authorize('view', $booking);

        return response()->json($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'agreed_price' => 'sometimes|numeric|min:0',
            'start_time' => 'sometimes|date|after:now',
            'end_time' => 'sometimes|date|after:start_time',
        ]);

        $booking->update($validated);

        return response()->json($booking);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('delete', $booking);

        $booking->delete();

        return response()->json(null, 204);
    }

    /**
     * Mark booking as in progress
     */
    public function markAsInProgress(string $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('update-status', $booking);

        if ($booking->markAsInProgress()) {
            return response()->json(['message' => 'Booking marked as in progress']);
        }

        return response()->json(['message' => 'Failed to update status'], 500);
    }

    /**
     * Complete booking
     */
    public function complete(string $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('update-status', $booking);

        if ($booking->complete()) {
            return response()->json(['message' => 'Booking completed successfully']);
        }

        return response()->json(['message' => 'Failed to complete booking'], 500);
    }

    /**
     * Cancel booking
     */
    public function cancel(string $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('update-status', $booking);

        if ($booking->cancel()) {
            return response()->json(['message' => 'Booking canceled successfully']);
        }

        return response()->json(['message' => 'Failed to cancel booking'], 500);
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(string $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('update-payment', $booking);

        if ($booking->markAsPaid()) {
            return response()->json(['message' => 'Payment marked as paid']);
        }

        return response()->json(['message' => 'Failed to update payment status'], 500);
    }

    /**
     * Get scheduled bookings
     */
    public function scheduled()
    {
        $bookings = Booking::with(['task', 'tasker'])
            ->where('tasker_id', Auth::id())
            ->scheduled()
            ->get();

        return response()->json($bookings);
    }

    /**
     * Get active bookings (scheduled or in progress)
     */
    public function active()
    {
        $bookings = Booking::with(['task', 'tasker'])
            ->where('tasker_id', Auth::id())
            ->active()
            ->get();

        return response()->json($bookings);
    }

    /**
     * Get completed bookings
     */
    public function completed()
    {
        $bookings = Booking::with(['task', 'tasker'])
            ->where('tasker_id', Auth::id())
            ->completed()
            ->get();

        return response()->json($bookings);
    }
}
