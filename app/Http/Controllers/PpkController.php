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
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,xlsx,xls,doc,docx',
            'signature' => 'required|string', // Pastikan tanda tangan wajib diisi
        ]);

        // Simpan data termasuk file evidence jika ada
        if ($request->hasFile('evidence')) {
            // Pastikan direktori dokumen ada
            $path = public_path('dokumen');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Simpan file dengan nama unik
            $file = $request->file('evidence');
            $filename = 'FT' . date('Ymdhis') . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);
        } else {
            $filename = null;
        }

        // Simpan tanda tangan ke folder yang diinginkan
        if ($request->signature) {
            $signaturePath = public_path('admin/img'); // Folder tempat menyimpan tanda tangan
            $signatureData = $request->signature;

            // Menghapus header data URL
            list($type, $signatureData) = explode(';', $signatureData);
            list(, $signatureData)      = explode(',', $signatureData);

            // Simpan file tanda tangan
            $signatureFileName = 'signature_' . time() . '.png';
            file_put_contents($signaturePath . '/' . $signatureFileName, base64_decode($signatureData));
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
                'evidence' => $filename,
                'signature' => isset($signatureFileName) ? $signatureFileName : null, // Simpan nama file tanda tangan
            ]);

            return redirect()->route('ppk.index')->with('success', 'Data PPK berhasil disimpan.✅');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }
}
