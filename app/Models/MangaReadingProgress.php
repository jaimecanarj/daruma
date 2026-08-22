<?php

namespace App\Models;

use App\Enums\ReadingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MangaReadingProgress extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'manga_id',
        'user_id',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReadingStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Manga, $this>
     */
    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }
}
