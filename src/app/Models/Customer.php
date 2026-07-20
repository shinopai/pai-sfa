<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    /**
     * 一括代入可能な属性
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'industry',
        'memo',
    ];

    /**
     * 担当営業
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
