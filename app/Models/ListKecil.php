<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListKecil extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model ini
    protected $table = 'listkecil';

    // Kolom-kolom yang bisa diisi massal
    protected $fillable = [
        'id_tindakan',
        'realisasi',
        'responsible',
        'accountable',
        'consulted',
        'informed',
        'anumgoal',
        'anumbudget',
        'desc',
        'date',
    ];

    // Kolom-kolom yang tidak bisa diisi massal
    protected $guarded = [];

    // Jika Anda memiliki tipe data khusus pada kolom-kolom tertentu, Anda bisa mendeklarasikan tipe tersebut di sini
    protected $casts = [
        // Misalnya, jika Anda memiliki kolom yang menyimpan tanggal
        // 'created_at' => 'datetime',
    ];

    public function listForm()
{
    return $this->belongsTo(ListForm::class, 'id', 'id'); // Sesuaikan field foreign key jika perlu
}

}
