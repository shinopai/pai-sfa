<?php

namespace Database\Factories;

use App\Enums\DealStatus;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'amount' => fake()->numberBetween(10000, 1000000),
            'status' => fake()->randomElement(DealStatus::cases()),
            'expected_contract_date' => fake()->dateTimeBetween('now', '+1 year'),
            'memo' => fake()->optional()->paragraph(),
        ];
    }
}
