<?php

namespace App\Enums;

enum AuthorRole: string
{
    case Story = 'story';
    case Art = 'art';
    case StoryAndArt = 'story_and_art';

    public function label(): string
    {
        return match ($this) {
            self::Story => 'Guion',
            self::Art => 'Dibujo',
            self::StoryAndArt => 'Guion y dibujo',
        };
    }
}
