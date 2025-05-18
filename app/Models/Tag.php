<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    public function mangas(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class);
    }
}
