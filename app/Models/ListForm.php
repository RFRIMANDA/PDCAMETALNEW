<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListForm extends Model
{
    use HasFactory;

    protected $table = 'listform';

    protected $fillable = [
        'id_divisi',
        'issue',
        'pihak',   // Kolom pihak disimpan dalam bentuk JSON
        'resiko',
        'peluang',
        'tingkatan',
        'tindakan',
        'pic',
        'status',
        'risk',
    ];

    // Relasi ke model Divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }

    // Accessor untuk mendapatkan nama pihak berdasarkan ID yang disimpan dalam bentuk JSON
    public function getPihakNamesAttribute()
    {
        $pihakIds = json_decode($this->pihak, true); // Ambil ID dari JSON
        if (is_array($pihakIds)) {
            return Divisi::whereIn('id', $pihakIds)->pluck('nama_divisi')->toArray();
        }
        return [];
    }
}
