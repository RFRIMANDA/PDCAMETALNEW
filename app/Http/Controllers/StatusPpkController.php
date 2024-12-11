<?php

namespace App\Http\Controllers;

use App\Models\StatusPpk; // Pastikan hanya satu use statement untuk model
use Illuminate\Http\Request;

class StatusPpkController extends Controller
{
    public function index(Request $request)
    {
        $data = StatusPpk::all();

        return view('admin.statusppk', compact('data')); // Tambahkan koma
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'nama_statusppk' => 'required|string|max:255'
        ]);

        // Simpan data ke database
        StatusPpk::create($validatedData);

        // Redirect dengan pesan sukses
        return redirect()->route('admin.statusppk')->with('success', 'Data status PPK berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'nama_statusppk' => 'required|string|max:255'
        ]);

        // Cari data berdasarkan ID dan perbarui
        $status = StatusPpk::findOrFail($id);
        $status->update($validatedData);

        // Redirect dengan pesan sukses
        return redirect()->route('statusppk.index')->with('success', 'Data status PPK berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Cari data berdasarkan ID dan hapus
        $status = StatusPpk::findOrFail($id);
        $status->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('statusppk.index')->with('success', 'Data status PPK berhasil dihapus.');
    }

}

