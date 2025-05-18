<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Magazine extends Model
{
    public function mangas(): HasMany
    {
        return $this->hasMany(Manga::class);
    }
}
