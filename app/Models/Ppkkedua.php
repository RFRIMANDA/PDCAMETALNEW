<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppkkedua extends Model
{
    use HasFactory;

    protected $table = 'formppk2';

    protected $fillable = [
        'id_formppk',
        'identifikasi',
        'signaturepenerima',
        'penanggulangan',
        'pencegahan',
        'pic1',
        'pic2',
        'tgl_penanggulangan',
        'tgl_pencegahan',
    ];
    public function formppk2()
{
    return $this->hasOne(Ppkkedua::class, 'id_formppk');
}
public function picUser()
{
    return $this->belongsTo(User::class, 'pic');
}

public function pic1User()
{
    return $this->belongsTo(User::class, 'pic1');
}

public function pic2User()
{
    return $this->belongsTo(User::class, 'pic2');
}
 // Pastikan kolom target_tgl di-cast sebagai tanggal
 protected $dates = ['target_tgl'];

}
