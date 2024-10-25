<?php

namespace App\Http\Controllers;

use App\Models\Ppk;
use App\Models\Userppk;
use Illuminate\Http\Request;

class PpkController extends Controller
{
    public function create()
    {
        return view('ppk.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'jenisketidaksesuaian' => 'nullable|in:Sistem,Proses,Produk,Audit',
            'pembuat' => 'nullable|string|max:255',
            'emailpembuat' => 'nullable|email|max:255',
            'divisipembuat' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'emailpenerima' => 'nullable|email|max:255',
            'divisipenerima' => 'nullable|string|max:255',
        ]);

        // Simpan data ke dalam database
        Ppk::create([
            'judul' => $request->judul,
            'jenisketidaksesuaian' => implode(',', $request->jenisketidaksesuaian ?? []),
            'pembuat' => $request->pembuat,
            'emailpembuat' => $request->emailpembuat,
            'divisipembuat' => $request->divisipembuat,
            'penerima' => $request->penerima,
            'emailpenerima' => $request->emailpenerima,
            'divisipenerima' => $request->divisipenerima,
        ]);

        // Redirect kembali ke form dengan pesan sukses
        return redirect()->route('ppk.create')->with('success', 'Data PPK berhasil disimpan.✅');
    }

        public function autocomplete(Request $request)
    {
        $term = $request->get('term');
        $users = Userppk::table('userppk')
                    ->where('nama', 'LIKE', '%' . $term . '%')
                    ->get();

        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'nama' => $user->nama,
                'email' => $user->email,
                'divisi' => $user->divisi
            ];
        }
        return response()->json($results);
    }

}
