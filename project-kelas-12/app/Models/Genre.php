<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = [
        'nama_genre',
        'slug',
    ];
    public $timestamps = true;

    public function films()
    {
        return $this->hasMany(Film::class, 'genre_id');
    }
}
