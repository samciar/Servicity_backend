<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'booking_id' => Booking::factory()->state(['status' => 'completed']),
            'reviewer_id' => function (array $attributes) {
                return Booking::find($attributes['booking_id'])->bid->task->client_id;
            },
            'reviewee_id' => function (array $attributes) {
                return Booking::find($attributes['booking_id'])->bid->tasker_id;
            },
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph,
            'type' => $this->faker->randomElement(['client_to_tasker', 'tasker_to_client']),
        ];
    }
}
