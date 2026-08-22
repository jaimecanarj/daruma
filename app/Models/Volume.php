<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Volume extends Model
{
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
