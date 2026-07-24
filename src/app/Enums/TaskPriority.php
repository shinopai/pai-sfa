<?php

namespace App\Enums;

enum TaskPriority: string
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';

    /**
     * ラベル
     */
    public function label(): string
    {
        return match ($this) {
            self::HIGH => '高',
            self::MEDIUM => '中',
            self::LOW => '低',
        };
    }

    /**
     * 選択肢
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::HIGH->value => self::HIGH->label(),
            self::MEDIUM->value => self::MEDIUM->label(),
            self::LOW->value => self::LOW->label(),
        ];
    }
}
