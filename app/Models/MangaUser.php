<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MangaUser extends Model
{
    protected $primaryKey = ['manga_id', 'user_id'];
    public $incrementing = false;

    protected $table = 'manga_user';

    protected $fillable = ['manga_id', 'user_id', 'status', 'favorite', 'started_at', 'completed_at'];
}
