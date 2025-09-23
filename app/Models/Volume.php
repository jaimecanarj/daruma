<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volume extends Model
{
    protected $primaryKey = ['manga_id', 'order'];
    public $incrementing = false;
    protected $fillable = ['name', 'cover', 'order', 'pages', 'manga_id'];

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'manga_id', 'manga_id')->where('volume_order', $this->order);
    }
}
