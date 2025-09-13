<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the booking.
     */
    public function view(User $user, Booking $booking): bool
    {
        // The tasker or client can view the booking
        return $user->id === $booking->tasker_id || $user->id === $booking->client_id;
    }

    /**
     * Determine whether the user can update the booking.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Only the tasker or client can update the booking
        return $user->id === $booking->tasker_id || $user->id === $booking->client_id;
    }

    /**
     * Determine whether the user can update the booking status.
     */
    public function updateStatus(User $user, Booking $booking): bool
    {
        // Only the tasker can update the status (start, complete, cancel)
        return $user->id === $booking->tasker_id;
    }

    /**
     * Determine whether the user can update the payment status.
     */
    public function updatePayment(User $user, Booking $booking): bool
    {
        // Only the client can update payment status
        return $user->id === $booking->client_id;
    }

    /**
     * Determine whether the user can delete the booking.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Only the client can delete the booking (if it's not started)
        return $user->id === $booking->client_id && $booking->status === Booking::STATUS_SCHEDULED;
    }
}
