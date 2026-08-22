<?php

namespace App\Models\Pivots;

use App\Enums\MangaRelationType;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MangaRelation extends Pivot
{
    protected $table = 'manga_relations';

    public $incrementing = true;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'type' => MangaRelationType::class,
        ];
    }
}
