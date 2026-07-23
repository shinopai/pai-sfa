<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deal_id' => Deal::factory(),
            'activity_type' => fake()->randomElement(ActivityType::cases()),
            'activity_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'content' => fake()->paragraph(),
        ];
    }
}
