<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Bid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        return [
            'bid_id' => Bid::factory(),
            'task_id' => function (array $attributes) {
                return Bid::find($attributes['bid_id'])->task_id;
            },
            'tasker_id' => function (array $attributes) {
                return Bid::find($attributes['bid_id'])->tasker_id;
            },
            'client_id' => function (array $attributes) {
                return Bid::find($attributes['bid_id'])->task->client_id;
            },
            'agreed_price' => function (array $attributes) {
                return Bid::find($attributes['bid_id'])->bid_amount;
            },
            'status' => $this->faker->randomElement([
                Booking::STATUS_SCHEDULED,
                Booking::STATUS_IN_PROGRESS,
                Booking::STATUS_COMPLETED,
                Booking::STATUS_CANCELED
            ]),
            'payment_status' => $this->faker->randomElement([
                Booking::PAYMENT_PENDING,
                Booking::PAYMENT_PAID,
                Booking::PAYMENT_REFUNDED
            ]),
            'start_time' => Carbon::now()->addDays(rand(1, 14))->format('Y-m-d H:i:s'),
            'end_time' => function (array $attributes) {
                return $attributes['status'] === Booking::STATUS_COMPLETED
                    ? Carbon::parse($attributes['start_time'])->addHours(rand(1, 8))->format('Y-m-d H:i:s')
                    : null;
            }
        ];
    }
}
