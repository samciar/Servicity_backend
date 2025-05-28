<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'booking_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Minimum and maximum rating values
     */
    public const MIN_RATING = 1;
    public const MAX_RATING = 5;

    /**
     * Get the booking associated with the review.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who wrote the review.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the user who was reviewed.
     */
    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    /**
     * Scope a query to only include reviews for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('reviewee_id', $userId);
    }

    /**
     * Scope a query to only include reviews by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('reviewer_id', $userId);
    }

    /**
     * Scope a query to only include high-rated reviews.
     */
    public function scopeHighRating($query, $threshold = 4)
    {
        return $query->where('rating', '>=', $threshold);
    }

    /**
     * Check if the review has a comment.
     */
    public function hasComment(): bool
    {
        return !empty($this->comment);
    }

    /**
     * Get the star rating display.
     */
    public function getStarRatingAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', self::MAX_RATING - $this->rating);
    }

    /**
     * Get the average rating for a user.
     */
    public static function averageForUser($userId): float
    {
        return static::forUser($userId)->avg('rating') ?? 0;
    }

    /**
     * Get the review count for a user.
     */
    public static function countForUser($userId): int
    {
        return static::forUser($userId)->count();
    }
}