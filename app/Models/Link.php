<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    //
    protected $fillable = [
        'url',
        'title',
        'image',
    ];

    public function genres()
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }
}
