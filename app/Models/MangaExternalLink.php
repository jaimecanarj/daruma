<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MangaExternalLink extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'manga_id',
        'provider',
        'url',
    ];
}
