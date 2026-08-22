<?php

namespace App\Enums;

enum ReadingDirection: string
{
    case Ltr = 'ltr';
    case Rtl = 'rtl';

    public function label(): string
    {
        return match ($this) {
            self::Ltr => 'Occidental',
            self::Rtl => 'Oriental',
        };
    }
}
