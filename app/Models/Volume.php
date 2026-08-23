<?php

namespace App\Models;

use App\Models\Concerns\HasCover;
use Database\Factories\VolumeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\InteractsWithMedia;

class Volume extends Model
{
    /**
     * @use HasFactory<VolumeFactory>
     */
    use HasCover, HasFactory, InteractsWithMedia{
        HasCover::registerMediaCollections insteadof InteractsWithMedia;
        HasCover::registerMediaConversions insteadof InteractsWithMedia;
    }

    protected $fillable = [
        'title',
        'number',
        'pages',
        'owned',
        'manga_id',
    ];

    protected function casts(): array
    {
        return [
            'owned' => 'boolean',
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
