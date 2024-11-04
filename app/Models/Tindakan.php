<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tindakan extends Model
{
    use HasFactory;

    protected $table = 'tindakan';

    protected $fillable = [
        'id_riskregister',
        'nama_tindakan',
        'targetpic',
        'peluang',
        'tgl_penyelesaian'
    ];

    // Relasi ke model Riskregister
    public function riskregister()
    {
        return $this->belongsTo(Riskregister::class, 'id_riskregister', 'id');
    }

    // Relasi ke model Divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'pihak', 'id'); // Menggunakan 'pihak' sebagai foreign key
    }

    public function realisasi()
    {
        return $this->hasMany(Realisasi::class, 'id_tindakan');
    }
        // Relasi ke tabel 'realisasis'
    public function realisasis()
    {
        return $this->hasMany(Realisasi::class, 'id_tindakan');
    }

}

