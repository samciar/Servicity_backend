<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'booking_id' => Booking::factory(),
            'amount' => function (array $attributes) {
                return Booking::find($attributes['booking_id'])->bid->bid_amount;
            },
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'method' => $this->faker->randomElement(['credit_card', 'debit_card', 'bank_transfer', 'cash']),
            'transaction_id' => 'PAY-' . strtoupper($this->faker->bothify('??????####')),
            'completed_at' => $this->faker->optional()->dateTimeThisMonth(),
            'client_id' => function (array $attributes) {
                return Booking::find($attributes['booking_id'])->bid->task->client_id;
            },
            'tasker_id' => function (array $attributes) {
                return Booking::find($attributes['booking_id'])->bid->tasker_id;
            },
        ];
    }
}
