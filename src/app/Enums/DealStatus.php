<?php

namespace App\Enums;

enum DealStatus: string
{
    case NEW = 'new';
    case PROPOSAL = 'proposal';
    case NEGOTIATING = 'negotiating';
    case WON = 'won';
    case LOST = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::NEW => '新規',
            self::PROPOSAL => '提案中',
            self::NEGOTIATING => '交渉中',
            self::WON => '成約',
            self::LOST => '失注',
        };
    }
}
