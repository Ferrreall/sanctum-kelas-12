<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aktor;

class Film extends Model
{
    protected $fillable = [
        'judul',
        'durasi',
        'rating',
        'deskripsi',
        'tanggal_rilis',
        'poster',
        'genre_id',
        'sutradara',
    ];
    public $timestamps = true;

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    public function aktors()
    {
        return $this->belongsToMany(
            Aktor::class,
            'aktor__films',
            'film_id',
            'aktor_id'
        );
    }
    
}
