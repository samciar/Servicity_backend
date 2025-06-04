<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'latitude',
        'longitude',
        'user_type',
        'profile_picture_url',
        'bio',
        'hourly_rate',
        'is_available',
        'id_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_available' => 'boolean',
        'id_verified' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * User types constants
     */
    public const TYPE_CLIENT = 'client';
    public const TYPE_TASKER = 'tasker';
    public const TYPE_ADMIN = 'admin';

    /**
     * Get the user type options
     */
    public static function getUserTypes(): array
    {
        return [
            self::TYPE_CLIENT => 'Client',
            self::TYPE_TASKER => 'Tasker',
            self::TYPE_ADMIN => 'Admin',
        ];
    }

    /**
     * Scope for clients
     */
    public function scopeClients($query)
    {
        return $query->where('user_type', self::TYPE_CLIENT);
    }

    /**
     * Scope for taskers
     */
    public function scopeTaskers($query)
    {
        return $query->where('user_type', self::TYPE_TASKER);
    }

    /**
     * Scope for admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('user_type', self::TYPE_ADMIN);
    }

    // Relationships

    /**
     * Tasks where this user is the client
     */
    public function clientTasks()
    {
        return $this->hasMany(Task::class, 'client_id');
    }

    /**
     * Tasks where this user is the tasker (through bookings)
     */
    public function assignedTasks()
    {
        return $this->hasManyThrough(
            Task::class,
            Booking::class,
            'tasker_id', // Foreign key on bookings table
            'id',        // Foreign key on tasks table
            'id',        // Local key on users table
            'task_id'    // Local key on bookings table
        );
    }

    /**
     * Bookings where this user is the tasker
     */
    public function taskerBookings()
    {
        return $this->hasMany(Booking::class, 'tasker_id');
    }

    /**
     * Bookings where this user is the client
     */
    public function clientBookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    /**
     * Skills for taskers
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'tasker_skills', 'tasker_id')
                    ->withPivot('proficiency_level')
                    ->withTimestamps();
    }

    /**
     * Bids made by this user (if tasker)
     */
    public function bids()
    {
        return $this->hasMany(Bid::class, 'tasker_id');
    }

    /**
     * Payments where this user is the payer (client)
     */
    public function paymentsMade()
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    /**
     * Payments where this user is the payee (tasker)
     */
    public function paymentsReceived()
    {
        return $this->hasMany(Payment::class, 'payee_id');
    }

    /**
     * Reviews this user has given
     */
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Reviews this user has received
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Messages this user has sent
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Messages this user has received
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if user is a client
     */
    public function isClient(): bool
    {
        return $this->user_type === self::TYPE_CLIENT;
    }

    /**
     * Check if user is a tasker
     */
    public function isTasker(): bool
    {
        return $this->user_type === self::TYPE_TASKER;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN;
    }
}
