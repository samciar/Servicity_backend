<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'booking_id',
        'payer_id',
        'payee_id',
        'amount',
        'currency',
        'transaction_id',
        'payment_method',
        'status',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    /**
     * Payment method constants
     */
    public const METHOD_CREDIT_CARD = 'credit_card';
    public const METHOD_PAYPAL = 'paypal';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_WALLET = 'wallet';

    /**
     * Currency constants
     */
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_GBP = 'GBP';

    /**
     * Get the booking associated with the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the payer (client) who made the payment.
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    /**
     * Get the payee (tasker) who receives the payment.
     */
    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id');
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(string $transactionId = null): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'processed_at' => now()
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'processed_at' => now()
        ]);
    }

    /**
     * Refund the payment.
     */
    public function refund(): bool
    {
        return $this->update([
            'status' => self::STATUS_REFUNDED,
            'processed_at' => now()
        ]);
    }

    /**
     * Check if payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbol = match($this->currency) {
            'EUR' => '€',
            'GBP' => '£',
            default => '$',
        };

        return $symbol . number_format($this->amount, 2);
    }

    /**
     * Get available payment methods.
     */
    public static function paymentMethods(): array
    {
        return [
            self::METHOD_CREDIT_CARD => 'Credit Card',
            self::METHOD_PAYPAL => 'PayPal',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_WALLET => 'Wallet Balance',
        ];
    }

    /**
     * Get available currencies.
     */
    public static function currencies(): array
    {
        return [
            self::CURRENCY_USD => 'US Dollar (USD)',
            self::CURRENCY_EUR => 'Euro (EUR)',
            self::CURRENCY_GBP => 'British Pound (GBP)',
        ];
    }
}