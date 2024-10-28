<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppk extends Model
{
    use HasFactory;

    protected $table = 'formppk';

    protected $fillable = [
        'judul',
        'jenisketidaksesuaian',
        'pembuat',
        'emailpembuat',
        'divisipembuat',
        'penerima',
        'emailpenerima',
        'divisipenerima',
        'ccemail',
        'evidence',
    ];

    protected $casts = [
        'ccemail' => 'array', // Untuk menyimpan beberapa email sebagai array
    ];
}
