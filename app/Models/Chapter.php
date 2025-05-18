<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }
}
