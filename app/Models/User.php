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

    // Define the relationship with Divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi', 'id');
    }

    
}

