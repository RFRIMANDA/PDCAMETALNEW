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
        'pic1_other',
        'pic2_other',
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
    return $this->belongsTo(User::class, 'pic1','id');
}

public function pic2User()
{
    return $this->belongsTo(User::class, 'pic2','id');
}
public function ppk() {
    return $this->belongsTo(Ppk::class, 'id_formppk');
}

 // Pastikan kolom target_tgl di-cast sebagai tanggal
 protected $dates = ['target_tgl'];

}
