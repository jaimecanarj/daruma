<?php

namespace App\Models;

use App\Enums\MangaLanguage;
use App\Enums\ReadingDirection;
use App\Models\Concerns\HasCover;
use App\Models\Pivots\AuthorManga;
use App\Models\Pivots\MangaRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Manga extends Model implements HasMedia
{
    use HasCover, InteractsWithMedia{
        HasCover::registerMediaCollections insteadof InteractsWithMedia;
        HasCover::registerMediaConversions insteadof InteractsWithMedia;
    }

    protected $fillable = [
        'title',
        'slug',
        'nas_folder_name',
        'synopsis',
        'start_date',
        'end_date',
        'reading_direction',
        'volumes_edition',
        'volumes_tankoubon',
        'chapters',
        'finished',
        'language',
        'magazine',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'reading_direction' => ReadingDirection::class,
            'finished' => 'boolean',
            'language' => MangaLanguage::class,
        ];
    }

    /**
     * @return HasMany<MangaTitle, $this>
     */
    public function titles(): HasMany
    {
        return $this->hasMany(MangaTitle::class);
    }

    /**
     * @return HasMany<MangaExternalLink, $this>
     */
    public function externalLinks(): HasMany
    {
        return $this->hasMany(MangaExternalLink::class);
    }

    /**
     * @return HasMany<Volume, $this>
     */
    public function volumes(): HasMany
    {
        return $this->hasMany(Volume::class);
    }

    /**
     * @return BelongsToMany<Author, $this, AuthorManga>
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class)
            ->using(AuthorManga::class)
            ->withPivot('role');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'manga_tag');
    }

    /**
     * @return BelongsToMany<Manga, $this, MangaRelation>
     */
    public function relatedMangas(): BelongsToMany
    {
        return $this->belongsToMany(
            Manga::class,
            'manga_relations',
            'manga_id',
            'related_manga_id',
        )->using(MangaRelation::class)->withPivot('relation');
    }
}
