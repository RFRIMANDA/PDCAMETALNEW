<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Resiko;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::all();
        return view('admin.kriteria', compact('kriteria'));
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

        // Simpan data kriteria
        Kriteria::create([
            'nama_kriteria' => $request->nama_kriteria,
            'desc_kriteria' => json_encode($request->desc_kriteria), // Simpan sebagai JSON
            'nilai_kriteria' => json_encode($request->nilai_kriteria), // Simpan sebagai JSON
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

        $kriteria = Kriteria::findOrFail($id);
        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'desc_kriteria' => json_encode($request->desc_kriteria),
            'nilai_kriteria' => json_encode($request->nilai_kriteria),
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
