<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'company_name' => fake('ja_JP')->company(),
            'contact_name' => fake('ja_JP')->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake('ja_JP')->phoneNumber(),
            'address' => fake('ja_JP')->address(),
            'industry' => fake('ja_JP')->randomElement([
                'IT',
                '製造業',
                '建設業',
                '小売業',
                'サービス業',
                '医療',
                '教育',
                '不動産',
                '物流',
                '金融',
            ]),
            'memo' => fake('ja_JP')->sentence(),
        ];
    }
}
