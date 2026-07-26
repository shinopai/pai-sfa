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
     */
    public function definition(): array
    {
        return [
            'deal_id' => Deal::query()->inRandomOrder()->value('id'),

            'activity_type' => fake()->randomElement(ActivityType::cases()),

            'activity_date' => fake()->dateTimeBetween('-6 months', 'now'),

            'content' => fake()->randomElement([
                '初回訪問を実施',
                'ヒアリングを実施',
                '製品デモを実施',
                '見積書を提出',
                '提案資料を送付',
                'オンライン商談を実施',
                '電話フォローを実施',
                'メールで進捗確認',
                '契約条件を調整',
                '次回訪問日を調整',
                '競合製品との比較説明',
                '社内打ち合わせを実施',
                '導入スケジュールを説明',
                '契約締結',
                '保留案件として管理',
            ]),
        ];
    }
}
