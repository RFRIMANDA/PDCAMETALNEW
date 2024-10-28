<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'user';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_user',
        'email',
        'password',
        'role',
        'type',
        'divisi',
    ];
        public function divisi()
    {
        return $this->belongsToMany(Divisi::class, 'user_divisi', 'user_id', 'divisi_id');
    }
}
