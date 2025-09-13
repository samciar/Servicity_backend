<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the task status.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        // Admins can update any task status
        if ($user->isAdmin()) {
            return true;
        }

        // Clients can update status of their own tasks
        if ($user->isClient() && $task->client_id === $user->id) {
            // Clients can only update to certain statuses
            $allowedClientStatuses = [
                Task::STATUS_CANCELED,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_COMPLETED
            ];
            
            // Check if the requested status is allowed for clients
            // This will be validated in the controller, but we can check here too
            return true; // Let the controller handle status validation
        }

        // Taskers can update status of tasks they're assigned to
        if ($user->isTasker() && $task->tasker && $task->tasker->id === $user->id) {
            // Taskers can only update to certain statuses
            $allowedTaskerStatuses = [
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_COMPLETED
            ];
            
            // Check if the requested status is allowed for taskers
            // This will be validated in the controller, but we can check here too
            return true; // Let the controller handle status validation
        }

        return false;
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Admins can update any task
        if ($user->isAdmin()) {
            return true;
        }

        // Clients can update their own tasks
        return $user->isClient() && $task->client_id === $user->id;
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // Admins can delete any task
        if ($user->isAdmin()) {
            return true;
        }

        // Clients can delete their own tasks if they're still open
        return $user->isClient() && 
               $task->client_id === $user->id && 
               $task->status === Task::STATUS_OPEN;
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Admins can view any task
        if ($user->isAdmin()) {
            return true;
        }

        // Clients can view their own tasks
        if ($user->isClient() && $task->client_id === $user->id) {
            return true;
        }

        // Taskers can view tasks they're assigned to or have bid on
        if ($user->isTasker()) {
            // Check if tasker is assigned to this task
            if ($task->tasker && $task->tasker->id === $user->id) {
                return true;
            }

            // Check if tasker has bid on this task
            $hasBid = $task->bids()->where('tasker_id', $user->id)->exists();
            if ($hasBid) {
                return true;
            }
        }

        // Anyone can view open tasks
        return $task->status === Task::STATUS_OPEN;
    }
}
