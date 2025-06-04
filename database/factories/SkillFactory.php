<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->sentence,
        ];
    }
}
