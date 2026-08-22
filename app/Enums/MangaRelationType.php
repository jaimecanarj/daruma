<?php

namespace App\Enums;

enum MangaRelationType: string
{
    case Prequel = 'prequel';
    case Sequel = 'sequel';
    case MainStory = 'main_story';
    case SpinOff = 'spin_off';

    public function label(): string
    {
        return match ($this) {
            self::Prequel => 'Precuela',
            self::Sequel => 'Secuela',
            self::MainStory => 'Historia principal',
            self::SpinOff => 'Spin-off',
        };
    }

    /**
     * Tipo de relación que debe guardarse en la fila espejo (manga_id y
     * related_manga_id invertidos) para mantener ambas direcciones en sync.
     */
    public function inverse(): self
    {
        return match ($this) {
            self::Prequel => self::Sequel,
            self::Sequel => self::Prequel,
            self::MainStory => self::SpinOff,
            self::SpinOff => self::MainStory,
        };
    }
}
