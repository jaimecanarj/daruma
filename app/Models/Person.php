<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Person extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function mangas(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class);
    }
}
