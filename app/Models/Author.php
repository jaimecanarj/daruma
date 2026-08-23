<?php

namespace App\Models;

use App\Models\Pivots\AuthorManga;
use Database\Factories\AuthorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    /**
     * @use HasFactory<AuthorFactory>
     */
    use HasFactory;

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
