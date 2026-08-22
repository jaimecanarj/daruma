<?php

namespace App\Models;

use App\Models\Pivots\AuthorManga;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'name_japanese',
    ];

    /**
     * @return BelongsToMany<Manga, $this, AuthorManga>
     */
    public function mangas(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class)
            ->using(AuthorManga::class)
            ->withPivot('role');
    }
}
