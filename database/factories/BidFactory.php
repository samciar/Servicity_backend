<?php

namespace Database\Factories;

use App\Models\Bid;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BidFactory extends Factory
{
    protected $model = Bid::class;

    public function definition()
    {
        return [
            'task_id' => Task::factory(),
            'tasker_id' => User::factory()->state(['user_type' => User::TYPE_TASKER]),
            'bid_amount' => $this->faker->numberBetween(20000, 200000),
            'message' => $this->faker->paragraph,
            'status' => Bid::STATUS_PENDING,
        ];
    }
}
