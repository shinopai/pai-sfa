<?php

namespace Database\Factories;

use App\Enums\DealStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::query()->inRandomOrder()->value('id'),

            'title' => fake()->randomElement([
                '営業管理システム導入',
                'ホームページリニューアル',
                '勤怠管理システム導入',
                '会計システム刷新',
                'ネットワーク機器更新',
                'PC入れ替え',
                '複合機導入',
                'クラウド移行支援',
                'セキュリティ対策',
                '保守契約更新',
                'サーバーリプレイス',
                'Webサイト制作',
                'ECサイト構築',
                '業務効率化システム開発',
                'データバックアップ導入',
            ]),

            'amount' => fake()->numberBetween(100000, 10000000),

            'status' => fake()->randomElement(DealStatus::cases()),

            'expected_contract_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),

            'memo' => fake()->optional()->randomElement([
                '見積書提出済み',
                '提案資料作成中',
                '顧客回答待ち',
                '競合比較中',
                '来週訪問予定',
                'オンライン商談予定',
                '社内確認中',
                '決裁待ち',
                '追加提案予定',
                null,
            ]),
        ];
    }
}
