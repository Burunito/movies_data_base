<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    //
    protected $fillable = [
        'link',
        'title',
        'image'
    ];

    public function genres()
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }
}
