<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'booking_id',
        'message',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who sent the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the associated booking (if message is booking-related).
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope a query to only include messages between two users.
     */
    public function scopeBetweenUsers(Builder $query, int $userId1, int $userId2): Builder
    {
        return $query->where(function($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)
              ->where('receiver_id', $userId2);
        })->orWhere(function($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)
              ->where('receiver_id', $userId1);
        });
    }

    /**
     * Scope a query to only include messages for a specific booking.
     */
    public function scopeForBooking(Builder $query, int $bookingId): Builder
    {
        return $query->where('booking_id', $bookingId);
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * Mark the message as read.
     */
    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            return $this->update(['read_at' => now()]);
        }
        return false;
    }

    /**
     * Check if the message has been read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if the message is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Get the conversation between two users.
     */
    public static function getConversation(int $userId1, int $userId2, ?int $bookingId = null): Builder
    {
        $query = self::betweenUsers($userId1, $userId2)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc');

        if ($bookingId) {
            $query->where('booking_id', $bookingId);
        }

        return $query;
    }

    /**
     * Get the most recent message in a conversation.
     */
    public static function getLatestMessage(int $userId1, int $userId2): ?self
    {
        return self::betweenUsers($userId1, $userId2)
            ->latest()
            ->first();
    }
}