<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppkkedua extends Model
{
    use HasFactory;

    protected $table = 'formppkkedua';

    protected $fillable = [
        'id_formppk',
        'identifikasi',
        'signaturepenerima'
    ];

}
