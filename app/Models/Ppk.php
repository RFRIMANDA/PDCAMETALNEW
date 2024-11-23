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

    public function pembuatUser()
    {
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }

    public function penerimaUser()
    {
        return $this->belongsTo(User::class, 'penerima', 'id');
    }

    public function formppk2()
    {
        return $this->hasOne(Ppkkedua::class, 'id_formppk', 'id');
    }

    public function formppk3()
{
    return $this->hasOne(Ppkketiga::class, 'id_formppk');
}

public function divisi1()
{
    return $this->belongsTo(Divisi::class, 'divisipembuat');
}
public function divisi2()
{
    return $this->belongsTo(Divisi::class, 'divisipenerima');
}

}
