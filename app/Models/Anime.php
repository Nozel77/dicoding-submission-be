<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    protected $guarded = [];

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'anime_id');
    }

}
