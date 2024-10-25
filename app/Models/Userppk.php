<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userppk extends Model
{
    use HasFactory;

    protected $table = 'userppk';

    protected $fillable = [
        'nama',
        'divisi',
        'email',
    ];
}
