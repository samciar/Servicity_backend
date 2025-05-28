<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'task_id',
        'tasker_id',
        'bid_amount',
        'message',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bid_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';

    /**
     * Get the task associated with the bid.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the tasker who made the bid.
     */
    public function tasker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tasker_id');
    }

    /**
     * Scope a query to only include pending bids.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include accepted bids.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Check if the bid is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the bid was accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Accept the bid (creates a booking).
     */
    public function accept(): bool
    {
        return $this->update(['status' => self::STATUS_ACCEPTED]);
    }

    /**
     * Reject the bid.
     */
    public function reject(): bool
    {
        return $this->update(['status' => self::STATUS_REJECTED]);
    }

    /**
     * Withdraw the bid.
     */
    public function withdraw(): bool
    {
        return $this->update(['status' => self::STATUS_WITHDRAWN]);
    }

    /**
     * Get the formatted bid amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->bid_amount, 2);
    }

    /**
     * Get the booking created from this bid (if accepted).
     */
    public function booking()
    {
        return $this->hasOne(Booking::class);
    }
}