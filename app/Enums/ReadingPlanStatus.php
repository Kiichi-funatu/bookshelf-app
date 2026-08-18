<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case Planned = 'planned';
    case in_progress = 'in_progress';
    case Completed = 'completed';
    case Expired = 'expired';

    /**
     * 日本語ラベル
     */
    public function label(): string
    {
        return match ($this) {
            self::Planned     => '計画中',
            self::in_progress => '進行中',
            self::Completed   => '読了',
            self::Expired     => '期限切れ',
        };
    }

    /**
     * 状態バッジの Tailwind CSS クラス
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Planned     => 'bg-blue-100 text-blue-700',
            self::in_progress => 'bg-yellow-100 text-yellow-700',
            self::Completed   => 'bg-green-100 text-green-700',
            self::Expired     => 'bg-red-100 text-red-700',
        };
    }
}
