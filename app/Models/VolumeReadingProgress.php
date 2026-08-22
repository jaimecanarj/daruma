<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolumeReadingProgress extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'volume_id',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Volume, $this>
     */
    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }
}
