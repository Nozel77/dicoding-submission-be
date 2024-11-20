<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $guarded = [];

    public function anime()
    {
        return $this->belongsTo(Anime::class, 'anime_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
