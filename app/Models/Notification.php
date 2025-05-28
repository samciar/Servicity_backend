<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Notification types constants
     */
    public const TYPE_NEW_BID = 'new_bid';
    public const TYPE_TASK_ACCEPTED = 'task_accepted';
    public const TYPE_PAYMENT_RECEIVED = 'payment_received';
    public const TYPE_BOOKING_CONFIRMED = 'booking_confirmed';
    public const TYPE_REVIEW_RECEIVED = 'review_received';
    public const TYPE_MESSAGE_RECEIVED = 'message_received';

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to only include notifications of specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            return $this->update(['read_at' => now()]);
        }
        return false;
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): bool
    {
        return $this->update(['read_at' => null]);
    }

    /**
     * Check if the notification has been read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if the notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Get a data value from the notification.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getDataValue(string $key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Get the notification's title based on its type.
     */
    public function getTitleAttribute(): string
    {
        return match($this->type) {
            self::TYPE_NEW_BID => 'New Bid Received',
            self::TYPE_TASK_ACCEPTED => 'Task Accepted',
            self::TYPE_PAYMENT_RECEIVED => 'Payment Received',
            self::TYPE_BOOKING_CONFIRMED => 'Booking Confirmed',
            self::TYPE_REVIEW_RECEIVED => 'New Review',
            self::TYPE_MESSAGE_RECEIVED => 'New Message',
            default => 'Notification',
        };
    }

    /**
     * Get the notification's icon based on its type.
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_NEW_BID => 'fa-gavel',
            self::TYPE_TASK_ACCEPTED => 'fa-check-circle',
            self::TYPE_PAYMENT_RECEIVED => 'fa-credit-card',
            self::TYPE_BOOKING_CONFIRMED => 'fa-calendar-check',
            self::TYPE_REVIEW_RECEIVED => 'fa-star',
            self::TYPE_MESSAGE_RECEIVED => 'fa-envelope',
            default => 'fa-bell',
        };
    }

    /**
     * Create a new notification with properly formatted data.
     */
    public static function createNotification(
        int $userId,
        string $type,
        array $data = [],
        bool $markAsRead = false
    ): self {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'data' => $data,
            'read_at' => $markAsRead ? now() : null,
        ]);
    }
}