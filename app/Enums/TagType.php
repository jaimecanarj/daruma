<?php

namespace App\Enums;

enum TagType: string
{
    case Genre = 'genre';
    case Theme = 'theme';

    public function label(): string
    {
        return match ($this) {
            self::Genre => 'Género',
            self::Theme => 'Temática',
        };
    }
}
