<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    //
    protected $fillable = [
        'title',
    ];

    public function actors()
    {
        return $this->morphedByMany(Actor::class, 'genreable');
    }

    public function movies()
    {
        return $this->morphedByMany(Movie::class, 'genreable');
    }
}
