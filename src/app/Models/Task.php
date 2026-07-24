<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TaskPriority;

class Task extends Model
{
    /**
     * 一括代入を許可する属性
     *
     * @var list<string>
     */
    protected $fillable = [
        'deal_id',
        'title',
        'description',
        'due_date',
        'priority',
        'is_completed',
    ];

    /**
     * 型変換
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
        'priority' => TaskPriority::class,
        'is_completed' => 'boolean'
    ];

    /**
     * 商談
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
