<?php

namespace App\Http\Controllers;

use App\Models\Ppk;
use App\Models\User;
use Illuminate\Http\Request;

class PpkController extends Controller
{
    public function index()
    {
        $ppks = Ppk::all(); // Ambil semua data Ppk dari database
        return view('ppk.index', compact('ppks')); // Kirim data ke view
    }

    public function create()
    {
        $data = User::all(); // Ambil data user untuk dropdown
        return view('ppk.create', compact('data'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'jenisketidaksesuaian' => 'nullable|array',
            'pembuat' => 'required|string|max:255',
            'emailpembuat' => 'required|email|max:255',
            'divisipembuat' => 'required|string|max:255',
            'penerima' => 'required|string|max:255',
            'emailpenerima' => 'required|email|max:255',
            'divisipenerima' => 'required|string|max:255',
            'ccemail' => 'nullable|email',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,xlsx,xls,doc,docx',
        ]);

        // Simpan data termasuk file evidence jika ada
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('evidence', 'public');
        }

        try {
            Ppk::create([
                'judul' => $request->judul,
                'jenisketidaksesuaian' => is_array($request->jenisketidaksesuaian) ? implode(',', $request->jenisketidaksesuaian) : null,
                'pembuat' => $request->pembuat,
                'emailpembuat' => $request->emailpembuat,
                'divisipembuat' => $request->divisipembuat,
                'penerima' => $request->penerima,
                'emailpenerima' => $request->emailpenerima,
                'divisipenerima' => $request->divisipenerima,
                'ccemail' => $request->ccemail,
                'evidence' => isset($evidencePath) ? $evidencePath : null,
            ]);

            return redirect()->route('ppk.index')->with('success', 'Data PPK berhasil disimpan.✅');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }
}
