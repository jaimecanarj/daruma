<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    protected $primaryKey = ['manga_id', 'order'];
    public $incrementing = false;
    protected $guarded = [];

    public $timestamps = false;

    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class, ['manga_id', 'volume_order'], ['manga_id', 'order']);
    }

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }
}
