<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
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
        'client_id',
        'agreed_price',
        'start_time',
        'end_time',
        'status',
        'payment_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'agreed_price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_DISPUTED = 'disputed';

    /**
     * Payment status constants
     */
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    /**
     * Get the task associated with the booking.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the tasker associated with the booking.
     */
    public function tasker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tasker_id');
    }

    /**
     * Get the client associated with the booking.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the payments for this booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the review for this booking.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Get the bid that created this booking.
     */
    public function bid(): HasOne
    {
        return $this->hasOne(Bid::class, 'task_id', 'task_id')
                   ->where('tasker_id', $this->tasker_id)
                   ->where('status', Bid::STATUS_ACCEPTED);
    }

    /**
     * Scope a query to only include scheduled bookings.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Scope a query to only include active bookings (scheduled or in progress).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Scope a query to only include completed bookings.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Mark the booking as in progress.
     */
    public function markAsInProgress(): bool
    {
        return $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'start_time' => $this->start_time ?? now()
        ]);
    }

    /**
     * Complete the booking.
     */
    public function complete(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'end_time' => $this->end_time ?? now()
        ]);
    }

    /**
     * Cancel the booking.
     */
    public function cancel(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELED]);
    }

    /**
     * Mark payment as paid.
     */
    public function markAsPaid(): bool
    {
        return $this->update(['payment_status' => self::PAYMENT_PAID]);
    }

    /**
     * Get the duration in hours (if completed).
     */
    public function getDurationInHoursAttribute(): ?float
    {
        if (!$this->end_time || !$this->start_time) {
            return null;
        }

        return round($this->end_time->diffInMinutes($this->start_time) / 60, 2);
    }

    /**
     * Get the total amount to be paid (for hourly tasks).
     */
    public function getTotalAmountAttribute(): float
    {
        if ($this->task->budget_type === Task::BUDGET_FIXED) {
            return $this->agreed_price;
        }

        return $this->agreed_price * ($this->duration_in_hours ?? 1);
    }

    /**
     * Get the formatted agreed price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->agreed_price, 2);
    }
}