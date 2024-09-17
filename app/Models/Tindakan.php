<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tindakan extends Model
{
    use HasFactory;

    protected $table = 'tindakan';

    protected $fillable = [
        'id_listform',
        'nama_tindakan',
        'pic',
        'resiko',
        'pihak',
    ];

    // Kolom-kolom yang tidak bisa diisi massal
    protected $guarded = [];

    public function divisi()
{
    return $this->belongsTo(Divisi::class, 'pihak', 'id');
}


}
