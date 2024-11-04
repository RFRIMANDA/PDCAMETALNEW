<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riskregister extends Model
{
    use HasFactory;

    protected $table = 'riskregister';
    protected $fillable = [
        'id_divisi',
        'issue',
        'target_penyelesaian',
        'peluang',
        'pihak',
        'inex',

    ];

    // Relasi ke model Divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id'); // Menggunakan 'id_divisi' untuk foreign key
    }

    // Relasi ke model Tindakan
    public function tindakan()
    {
        return $this->hasMany(Tindakan::class, 'id_riskregister', 'id'); // Menggunakan 'id_riskregister' sebagai foreign key
    }

    // Relasi ke model Resiko
    public function resikos() // Mengubah menjadi 'resikos' sesuai penamaan dalam model
    {
        return $this->hasMany(Resiko::class, 'id_riskregister', 'id'); // Menggunakan 'id_riskregister' untuk foreign key
    }
    public function realisasi()
    {
        return $this->hasOne(Realisasi::class);
    }
    public function tindakans()
    {
        return $this->hasMany(Tindakan::class, 'id_riskregister');
    }

}

