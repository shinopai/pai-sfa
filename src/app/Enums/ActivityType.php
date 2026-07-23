<?php

namespace App\Enums;

enum ActivityType: string
{
    case PHONE = 'phone';
    case EMAIL = 'email';
    case VISIT = 'visit';
    case ONLINE_MEETING = 'online_meeting';
    case MEMO = 'memo';

    /**
     * プルダウン表示用
     */
    public function label(): string
    {
        return match ($this) {
            self::PHONE => '電話',
            self::EMAIL => 'メール',
            self::VISIT => '訪問',
            self::ONLINE_MEETING => 'オンラインMTG',
            self::MEMO => 'メモ',
        };
    }

    /**
     * セレクトボックス用
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $type) => [
                $type->value => $type->label(),
            ])
            ->all();
    }
}
