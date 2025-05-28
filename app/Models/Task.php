<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'client_id',
        'category_id',
        'title',
        'description',
        'budget_type',
        'budget_amount',
        'location',
        'latitude',
        'longitude',
        'preferred_date',
        'preferred_time',
        'status',
        'deadline_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'budget_amount' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'preferred_date' => 'date',
        'preferred_time' => 'datetime:H:i',
        'deadline_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Budget type constants
     */
    public const BUDGET_FIXED = 'fixed';
    public const BUDGET_HOURLY = 'hourly';

    /**
     * Status constants
     */
    public const STATUS_OPEN = 'open';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_DISPUTED = 'disputed';

    /**
     * Get the client who created the task.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the category of the task.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all bids for the task.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get the accepted bid (booking).
     */
    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }

    /**
     * Get the tasker assigned to this task (through booking).
     */
    public function tasker()
    {
        return $this->hasOneThrough(
            User::class,
            Booking::class,
            'task_id', // Foreign key on bookings table
            'id',      // Foreign key on users table
            'id',      // Local key on tasks table
            'tasker_id' // Local key on bookings table
        );
    }

    /**
     * Get the skills required for this task.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'task_skills')
                   ->withTimestamps();
    }

    /**
     * Scope a query to only include open tasks.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include tasks needing immediate attention.
     */
    public function scopeUrgent($query)
    {
        return $query->where('deadline_at', '<=', now()->addDays(2))
                    ->whereIn('status', [self::STATUS_OPEN, self::STATUS_ASSIGNED]);
    }

    /**
     * Scope a query to search tasks by title or description.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Check if the task is open for bidding.
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if the task has been assigned.
     */
    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    /**
     * Get the formatted budget information.
     */
    public function getFormattedBudgetAttribute(): string
    {
        if ($this->budget_type === self::BUDGET_FIXED) {
            return '$' . number_format($this->budget_amount, 2);
        }

        return '$' . number_format($this->budget_amount, 2) . '/hour';
    }

    /**
     * Get the combined preferred date and time.
     */
    public function getPreferredDateTimeAttribute(): ?string
    {
        if (!$this->preferred_date) {
            return null;
        }

        $date = $this->preferred_date->format('M j, Y');
        $time = $this->preferred_time ? $this->preferred_time->format('g:i A') : 'Anytime';

        return "{$date} at {$time}";
    }
}