<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';
    protected $fillable = [
        'nama_divisi'
    ];

    public function riskregisters()
    {
        return $this->hasMany(Riskregister::class, 'id_divisi');
    }
}
