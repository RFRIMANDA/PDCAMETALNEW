<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPpk extends Model
{
    use HasFactory;

    protected $table = 'status'; // Nama tabel yang digunakan

    protected $fillable = [
        'nama_statusppk'
    ];

    public function ppks()
    {
        return $this->hasMany(Ppk::class, 'statusppk');
    }
}
