<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\DealStatus;

class Deal extends Model
{
    use HasFactory;
    /**
     * 一括代入可能な属性
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'user_id',
        'title',
        'amount',
        'status',
        'expected_contract_date',
        'memo',
    ];

    /**
     * キャスト
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'expected_contract_date' => 'date',
            'status' => DealStatus::class
        ];
    }

    /**
     * 顧客
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * 担当営業
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 営業活動
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * タスク
     */
    // public function tasks(): HasMany
    // {
    //     return $this->hasMany(Task::class);
    // }
}
