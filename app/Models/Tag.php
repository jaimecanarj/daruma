<?php

namespace App\Models;

use App\Enums\TagType;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /**
     * @use HasFactory<TagFactory>
     */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => TagType::class,
        ];
    }

    /**
     * @return BelongsToMany<Manga, $this>
     */
    public function mangas(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class, 'manga_tag');
    }
}
