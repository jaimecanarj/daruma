<?php

namespace App\Enums;

enum MangaTitleType: string
{
    case Japanese = 'japanese';
    case Spanish = 'spanish';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Japanese => 'Japonés',
            self::Spanish => 'Español',
            self::Other => 'Otro',
        };
    }
}
