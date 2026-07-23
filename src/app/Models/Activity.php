<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;
    /**
     * 一括代入可能な属性
     *
     * @var list<string>
     */
    protected $fillable = [
        'deal_id',
        'activity_type',
        'activity_date',
        'content',
    ];

    /**
     * 型変換
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activity_type' => ActivityType::class,
        'activity_date' => 'datetime'
    ];

    /**
     * 商談
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
