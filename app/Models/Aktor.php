<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aktor extends Model
{
    protected $fillable = [
        'nama_aktor',
        'jenis_kelamin',
        'tanggal_lahir',
        'umur',
        'foto',
    ];
    public $timestamps = true;
}
