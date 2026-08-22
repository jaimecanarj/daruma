<?php

namespace App\Enums;

enum ReadingStatus: string
{
    case Pending = 'pending';
    case Reading = 'reading';
    case Completed = 'completed';
    case Dropped = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Reading => 'Leyendo',
            self::Completed => 'Completado',
            self::Dropped => 'Abandonado',
        };
    }
}
