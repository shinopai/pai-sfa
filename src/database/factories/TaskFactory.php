<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'deal_id' => Deal::query()->inRandomOrder()->value('id'),

            'title' => fake()->randomElement([
                '見積書作成',
                '見積書送付',
                '電話フォロー',
                'メール送信',
                '訪問準備',
                '顧客訪問',
                'オンライン商談',
                '提案資料作成',
                '契約書作成',
                '契約書送付',
                '導入日調整',
                '進捗確認',
                '社内打ち合わせ',
                '議事録作成',
                '請求書発行',
            ]),

            'description' => fake()->optional()->randomElement([
                '顧客からの要望を反映する',
                '最新見積を送付する',
                '導入スケジュールを確認する',
                '担当者へ進捗確認を行う',
                '社内レビュー後に送付する',
                null,
            ]),

            'due_date' => fake()->dateTimeBetween('-1 month', '+1 month'),

            'priority' => fake()->randomElement(TaskPriority::cases()),

            'is_completed' => fake()->boolean(30),
        ];
    }
}
