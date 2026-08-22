<?php

namespace App\Models\Pivots;

use App\Enums\AuthorRole;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AuthorManga extends Pivot
{
    protected $table = 'author_manga';

    public $incrementing = true;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'role' => AuthorRole::class,
        ];
    }
}
