<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppkkeempat extends Model
{
    use HasFactory;

    protected $table = 'formppk4';

    protected $fillable = [
        'id_formppk',
        'catatan',
        'tgl_verif'
    ];
}
