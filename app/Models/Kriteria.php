<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';

    protected $fillable = [
        'id_resiko',
        'nama_kriteria',
        'desc_kriteria',
        'nilai_kriteria',
    ];
}
