<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'client_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'budget_type' => $this->faker->randomElement([Task::BUDGET_FIXED, Task::BUDGET_HOURLY]),
            'budget_amount' => $this->faker->numberBetween(20000, 200000),
            'location' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'preferred_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'preferred_time' => $this->faker->time('H:i'),
            'status' => Task::STATUS_OPEN,
        ];
    }

    public function fixedBudget()
    {
        return $this->state([
            'budget_type' => Task::BUDGET_FIXED,
        ]);
    }

    public function hourlyBudget()
    {
        return $this->state([
            'budget_type' => Task::BUDGET_HOURLY,
        ]);
    }
}
