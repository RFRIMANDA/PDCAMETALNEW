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
        'evidence',
        'signature',
        'created_at',
        'nomor_surat',
        'cc_email'
    ];

    public function formppkkedua()
    {
        return $this->hasOne(Ppkkedua::class, 'id_formppk', 'id');
    }

}
