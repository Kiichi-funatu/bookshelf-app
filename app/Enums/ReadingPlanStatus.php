<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case Planned = 'planned';
    case Completed = 'completed';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Planned => '計画中',
            self::Completed => '読了',
            self::Expired => '期限切れ',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Planned => 'bg-blue-100 text-blue-700',
            self::Completed => 'bg-green-100 text-green-700',
            self::Expired => 'bg-red-100 text-red-700',
        };
    }
}
