<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    //
    protected $fillable = [
        'name',
        'link',
        'image'
    ];

    public function genres()
    {
        return $this->morphToMany(Genre::class, 'genreable');
    }
}
