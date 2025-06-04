<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'sometimes|in:new_bid,task_accepted,payment_received,booking_confirmed,review_received,message_received',
            'read' => 'sometimes|boolean'
        ]);

        $query = Notification::where('user_id', Auth::id())
            ->with('user');

        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        if ($request->has('read')) {
            $request->boolean('read') 
                ? $query->read()
                : $query->unread();
        }

        $notifications = $query->latest()
            ->get();

        return response()->json($notifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => ['required', Rule::in([
                Notification::TYPE_NEW_BID,
                Notification::TYPE_TASK_ACCEPTED,
                Notification::TYPE_PAYMENT_RECEIVED,
                Notification::TYPE_BOOKING_CONFIRMED,
                Notification::TYPE_REVIEW_RECEIVED,
                Notification::TYPE_MESSAGE_RECEIVED
            ])],
            'data' => 'nullable|array',
            'mark_as_read' => 'sometimes|boolean'
        ]);

        $notification = Notification::createNotification(
            $validated['user_id'],
            $validated['type'],
            $validated['data'] ?? [],
            $validated['mark_as_read'] ?? false
        );

        return response()->json($notification->load('user'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $notification = Notification::with('user')->findOrFail($id);
        $this->authorize('view', $notification);

        return response()->json($notification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('update', $notification);

        $validated = $request->validate([
            'data' => 'sometimes|array',
        ]);

        $notification->update([
            'data' => $validated['data'] ?? $notification->data
        ]);

        return response()->json($notification->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('delete', $notification);

        $notification->delete();

        return response()->json(null, 204);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $id)
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('markAsRead', $notification);

        if ($notification->markAsRead()) {
            return response()->json(['message' => 'Notification marked as read']);
        }

        return response()->json(['message' => 'Notification was already read']);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(string $id)
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('markAsUnread', $notification);

        $notification->markAsUnread();

        return response()->json(['message' => 'Notification marked as unread']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }
}
