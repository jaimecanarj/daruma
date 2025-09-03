<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manga extends Model
{
    protected $fillable = [
        'name',
        'cover',
        'sinopsis',
        'volumes',
        'tankoubon',
        'chapters',
        'start_date',
        'end_date',
        'reading_direction',
        'finished',
        'mal',
        'listado_manga',
        'language',
        'magazine_id',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)->withPivot('job');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function magazine(): BelongsTo
    {
        return $this->belongsTo(Magazine::class);
    }

    public function volumesData(): HasMany
    {
        return $this->hasMany(Volume::class);
    }

    public function chaptersData(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function names(): HasMany
    {
        return $this->hasMany(MangaName::class);
    }

    public function mangasRelated(): BelongsToMany
    {
        return $this->belongsToMany(Manga::class, 'manga_related', 'manga_id', 'related_manga_id')->withPivot('relation');
    }
}
