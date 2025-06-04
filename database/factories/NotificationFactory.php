<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        $types = [
            'bid_received',
            'bid_accepted',
            'booking_update',
            'payment_completed',
            'new_review'
        ];

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'type' => $this->faker->randomElement($types),
            'related_id' => $this->faker->numberBetween(1, 100),
            'related_type' => $this->faker->randomElement(['bid', 'booking', 'payment', 'review']),
            'read_at' => $this->faker->optional()->dateTimeThisMonth(),
        ];
    }
}
