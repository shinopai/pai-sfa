<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;
use App\Enums\DealStatus;

class DealStatus extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'expected_contract_date' => 'date',
            'status' => DealStatus::class,
        ];
    }
}
