<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resiko extends Model
{
    use HasFactory;

    protected $table = 'resiko'; // Nama tabel yang digunakan

    protected $fillable = [
        'id_riskregister',
        'nama_resiko',
        'kriteria',
        'probability',
        'severity',
        'tingkatan',
        'status',
        'risk',
        'probabilityrisk',
        'severityrisk',
        'before',
        'after',
    ];

    public function calculateTingkatan()
    {
        if ($this->probability && $this->severity) {
            $score = $this->probability * $this->severity;

            if (in_array($this->kriteria, ['Reputasi', 'Kinerja', 'Operational', 'Financial'])) {
                $this->tingkatan = $this->calculateNewCategories(); // Menggunakan metode baru
            } else {
                // Kategori lama
                if ($score >= 1 && $score <= 2) { // HIJAU
                    $this->tingkatan = 'LOW';
                } elseif ($score >= 3 && $score <= 4) {
                    $this->tingkatan = 'MEDIUM'; // BIRU
                } elseif ($score >= 5 && $score <= 25) {
                    $this->tingkatan = 'HIGH'; // KUNING
                }
            }
        }
    }

    public function calculateRisk()
    {
        if ($this->probabilityrisk && $this->severityrisk) {
            $scorerisk = $this->probabilityrisk * $this->severityrisk;

            if (in_array($this->kriteria, ['Reputasi', 'Kinerja', 'Operational', 'Financial'])) {
                $this->risk = $this->calculateRiskNew(); // Menggunakan metode baru
            } else {
                // Kategori lama
                if ($scorerisk >= 1 && $scorerisk <= 2) { // HIJAU
                    $this->risk = 'LOW';
                } elseif ($scorerisk >= 3 && $scorerisk <= 4) {
                    $this->risk = 'MEDIUM'; // BIRU
                } elseif ($scorerisk >= 5 && $scorerisk <= 25) {
                    $this->risk = 'HIGH'; // KUNING
                }
            }
        }
    }

    public function calculateNewCategories()
    {
        // Menggunakan severity dan probability untuk menghitung kategori baru
        if ($this->probability && $this->severity) {
            $score = $this->probability * $this->severity;

            if ($score >= 1 && $score <= 2) {
                return 'LOW'; // Kategori Low
            } elseif ($score >= 2 && $score <= 5) {
                return 'MEDIUM'; // Kategori Medium
            } elseif ($score >= 4 && $score <= 21) {
                return 'HIGH'; // Kategori High
            }
        }

        return null; // Jika tidak ada kategori yang cocok
    }

    public function calculateRiskNew()
    {
        if ($this->probabilityrisk && $this->severityrisk) {
            $scorerisk = $this->probabilityrisk * $this->severityrisk;

            if ($scorerisk >= 1 && $scorerisk <= 2) { // LOW
                $this->risk = 'LOW';
            } elseif ($scorerisk >= 2 && $scorerisk <= 5) {
                $this->risk = 'MEDIUM'; // MEDIUM
            } elseif ($scorerisk >= 4 && $scorerisk <= 21) {
                $this->risk = 'HIGH'; // HIGH
            }
        }
    }

    public function riskregister()
    {
        return $this->belongsTo(Riskregister::class, 'id_riskregister', 'id');
    }
}
