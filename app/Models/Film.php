<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $fillable = [
        'judul',
        'durasi',
        'rating',
        'deskripsi',
        'tahun_rilis',
        'poster',
        'genre_id',
        'sutradara',
    ];
    public $timestamps = true;

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }
    
}
