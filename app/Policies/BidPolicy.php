<?php

namespace App\Policies;

use App\Models\Bid;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BidPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can accept the bid.
     */
    public function accept(User $user, Bid $bid): bool
    {
        // Only the client who owns the task can accept a bid
        return $user->id === $bid->task->client_id;
    }

    /**
     * Determine whether the user can reject the bid.
     */
    public function reject(User $user, Bid $bid): bool
    {
        // Only the client who owns the task can reject a bid
        return $user->id === $bid->task->client_id;
    }

    /**
     * Determine whether the user can withdraw the bid.
     */
    public function withdraw(User $user, Bid $bid): bool
    {
        // Only the tasker who made the bid can withdraw it
        return $user->id === $bid->tasker_id;
    }

    /**
     * Determine whether the user can view the bid.
     */
    public function view(User $user, Bid $bid): bool
    {
        // The client who owns the task or the tasker who made the bid can view it
        return $user->id === $bid->task->client_id || $user->id === $bid->tasker_id;
    }

    /**
     * Determine whether the user can update the bid.
     */
    public function update(User $user, Bid $bid): bool
    {
        // Only the tasker who made the bid can update it (if it's still pending)
        return $user->id === $bid->tasker_id && $bid->isPending();
    }

    /**
     * Determine whether the user can delete the bid.
     */
    public function delete(User $user, Bid $bid): bool
    {
        // Only the tasker who made the bid can delete it (if it's still pending)
        return $user->id === $bid->tasker_id && $bid->isPending();
    }
}
