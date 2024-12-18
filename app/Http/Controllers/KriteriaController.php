<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Resiko;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index(Request $request)
    {
        $all = Kriteria::all();
        $nama_kriteria = $request->get('nama_kriteria');
        $desc_kriteria = $request->get('desc_kriteria');

        $kriteriaQuery = Kriteria::query();

        if ($nama_kriteria) {
            $kriteriaQuery->where('nama_kriteria', $nama_kriteria);
        }

        if ($desc_kriteria) {
            $kriteriaQuery->where('desc_kriteria', 'LIKE', '%' . $desc_kriteria . '%');
        }

        $kriteria = $kriteriaQuery->get();
        $namaKriteriaList = Kriteria::pluck('nama_kriteria')->unique();

        return view('admin.kriteria', compact('kriteria', 'namaKriteriaList','all'));
    }


    public function create()
    {
        $kriteria = Kriteria::all();
        $resiko = Resiko::all(); // Pastikan data resiko diambil

        return view('admin.kriteriacreate', compact('kriteria', 'resiko'));
    }


    public function store(Request $request)
{
    // Validasi input array
    $request->validate([
        'nama_kriteria' => 'required|string',
        'desc_kriteria' => 'required|array',
        'desc_kriteria.*' => 'required|string', // Setiap item dalam array harus string
        'nilai_kriteria' => 'required|array',
        'nilai_kriteria.*' => 'required|string', // Setiap item dalam array harus string
    ]);

    // Gabungkan array menjadi string tanpa tanda kurung siku
    $descKriteria = implode(", ", $request->desc_kriteria);
    $nilaiKriteria = implode(", ", $request->nilai_kriteria);

    // Simpan data kriteria
    Kriteria::create([
        'nama_kriteria' => $request->nama_kriteria,
        'desc_kriteria' => $descKriteria, // Simpan sebagai string tanpa []
        'nilai_kriteria' => $nilaiKriteria, // Simpan sebagai string tanpa []
    ]);

    return redirect()->route('admin.kriteria')->with('success', 'Kriteria berhasil ditambahkan dengan deskripsi dan nilai!');
}


    public function edit($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('admin.kriteriaedit', compact('kriteria'));
    }

    public function update(Request $request, $id)
{
    // Validasi input array
    $request->validate([
        'nama_kriteria' => 'required|string',
        'desc_kriteria' => 'required|array',
        'desc_kriteria.*' => 'required|string',
        'nilai_kriteria' => 'required|array',
        'nilai_kriteria.*' => 'required|string',
    ]);

    // Gabungkan array menjadi string tanpa tanda kurung siku
    $descKriteria = implode(", ", $request->desc_kriteria);
    $nilaiKriteria = implode(", ", $request->nilai_kriteria);

    // Cari data kriteria berdasarkan ID
    $kriteria = Kriteria::findOrFail($id);

    // Perbarui data kriteria
    $kriteria->update([
        'nama_kriteria' => $request->nama_kriteria,
        'desc_kriteria' => $descKriteria, // Simpan sebagai string biasa
        'nilai_kriteria' => $nilaiKriteria, // Simpan sebagai string biasa
    ]);

    return redirect()->route('admin.kriteria')->with('success', 'Kriteria berhasil diperbarui!');
}


    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->delete();
        return redirect()->route('admin.kriteria')->with('success', 'Kriteria berhasil dihapus!');
    }

}
