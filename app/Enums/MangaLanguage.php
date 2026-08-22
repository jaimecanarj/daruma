<?php

namespace App\Enums;

enum MangaLanguage: string
{
    case Spanish = 'es';
    case English = 'en';
    case Japanese = 'ja';

    public function label(): string
    {
        return match ($this) {
            self::Spanish => 'Español',
            self::English => 'Inglés',
            self::Japanese => 'Japonés',
        };
    }
}
