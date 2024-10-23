<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    use HasFactory;

    protected $table = 'realisasi';

    protected $fillable = [
        'id_tindakan',
        'id_riskregister',
        'nama_realisasi',
        'target', // pic
        'desc', // noted
        'tgl_realisasi',
        'status',
        'presentase'
    ];

    protected $casts = [
        'nama_realisasi' => 'array', // Casting sebagai array (JSON)
    ];

    public function tindakan()
    {
        return $this->belongsTo(Tindakan::class, 'id_tindakan');
    }

    public function resiko()
    {
        return $this->belongsTo(Resiko::class, 'id_tindakan');
    }
}
