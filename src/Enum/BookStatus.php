<?php

declare(strict_types=1);

namespace App\Enum;

enum BookStatus: string
{
    case Available   = 'available';
    case CheckedOut  = 'checked_out';
    case DigitalOnly = 'digital_only';

    public function label(): string
    {
        return match ($this) {
            self::Available   => 'Доступна',
            self::CheckedOut  => 'На руках',
            self::DigitalOnly => 'Електронна',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Available   => 'available',
            self::CheckedOut  => 'checked-out',
            self::DigitalOnly => 'digital-only',
        };
    }
}
