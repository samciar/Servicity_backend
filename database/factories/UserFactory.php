<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'user_type' => $this->faker->randomElement([User::TYPE_CLIENT, User::TYPE_TASKER]),
            'profile_picture_url' => $this->faker->imageUrl(),
            'bio' => $this->faker->paragraph,
            'hourly_rate' => $this->faker->numberBetween(15000, 50000),
            'is_available' => $this->faker->boolean,
            'id_verified' => $this->faker->boolean,
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function client()
    {
        return $this->state([
            'user_type' => User::TYPE_CLIENT,
        ]);
    }

    public function tasker()
    {
        return $this->state([
            'user_type' => User::TYPE_TASKER,
            'hourly_rate' => $this->faker->numberBetween(20000, 80000),
        ]);
    }
}
