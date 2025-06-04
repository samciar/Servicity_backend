<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'receiver_id' => 'sometimes|required|exists:users,id',
            'booking_id' => 'sometimes|nullable|exists:bookings,id'
        ]);

        $query = Message::query();

        if ($request->has('receiver_id')) {
            $query->betweenUsers(Auth::id(), $request->receiver_id);
        }

        if ($request->has('booking_id')) {
            $query->forBooking($request->booking_id);
        }

        $messages = $query->with(['sender', 'receiver', 'booking'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'booking_id' => $validated['booking_id'] ?? null,
            'message' => $validated['message']
        ]);

        return response()->json($message->load(['sender', 'receiver']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $message = Message::with(['sender', 'receiver', 'booking'])->findOrFail($id);
        $this->authorize('view', $message);

        return response()->json($message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $message = Message::findOrFail($id);
        $this->authorize('update', $message);

        $validated = $request->validate([
            'message' => 'sometimes|required|string|max:1000',
        ]);

        $message->update($validated);

        return response()->json($message->load(['sender', 'receiver']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = Message::findOrFail($id);
        $this->authorize('delete', $message);

        $message->delete();

        return response()->json(null, 204);
    }

    /**
     * Get conversation between two users
     */
    public function conversation(Request $request, int $userId)
    {
        $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = Message::getConversation(Auth::id(), $userId, $request->booking_id);

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $messages = $query->get();

        return response()->json($messages);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(string $id)
    {
        $message = Message::findOrFail($id);
        $this->authorize('markAsRead', $message);

        if ($message->markAsRead()) {
            return response()->json(['message' => 'Message marked as read']);
        }

        return response()->json(['message' => 'Message was already read']);
    }

    /**
     * Get unread messages for current user
     */
    public function unread()
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->unread()
            ->with(['sender', 'booking'])
            ->get();

        return response()->json($messages);
    }

    /**
     * Get latest message in a conversation
     */
    public function latestMessage(int $userId)
    {
        $message = Message::getLatestMessage(Auth::id(), $userId);

        return response()->json($message);
    }
}
