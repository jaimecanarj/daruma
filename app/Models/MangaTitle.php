<?php

namespace App\Models;

use App\Enums\MangaTitleType;
use Illuminate\Database\Eloquent\Model;

class MangaTitle extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'manga_id',
        'title',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => MangaTitleType::class,
        ];
    }
}
