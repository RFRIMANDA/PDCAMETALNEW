<?php

namespace App\Http\Controllers;

use App\Models\Ppk;
use App\Models\Ppkkedua;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PpkExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PpkController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $ppks = Ppk::where('pembuat', $userId)
                    ->orWhere('penerima', $userId)
                    ->with(['formppkkedua', 'pembuatUser', 'penerimaUser']) // Tambahkan relasi pengguna
                    ->get();

        return view('ppk.index', compact('ppks'));
    }

    public function create()
    {
        $data = User::all(); // Ambil data user untuk dropdown
        return view('ppk.create', compact('data'));
    }

    public function create2($id)
    {
        $data = User::all(); // Ambil data user untuk dropdown
        $ppk = Ppk::findOrFail($id); // Ambil data PPK berdasarkan ID
        return view('ppk.formppkkedua', ['id' => $id],compact('data', 'ppk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'nullable|string|max:1000',
            'jenisketidaksesuaian' => 'nullable|array',
            'jenisketidaksesuaian.*' => 'in:SISTEM,AUDIT,PRODUK,PROSES', // Validasi untuk elemen di dalam array
            'pembuat' => 'required|string|max:255',
            'emailpembuat' => 'required|email|max:255',
            'divisipembuat' => 'required|string|max:255',
            'penerima' => 'required|string|max:255',
            'emailpenerima' => 'required|email|max:255',
            'divisipenerima' => 'required|string|max:255',
            'cc_email' => 'nullable|array',
            'cc_email.*' => 'email',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,xlsx,xls,doc,docx',
            'signature' => 'nullable|string',
            'signature_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'identifikasi' => 'nullable|string|max:1000',
            'signaturepenerima' => 'nullable|string',
            'signaturepenerima_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ], [
            'signature.required_without' => 'Tanda tangan diperlukan, baik dari canvas atau file.',
            'signature_file.required_without' => 'Tanda tangan diperlukan, baik dari canvas atau file.',
            'signaturepenerima.required_without' => 'Tanda tangan diperlukan, baik dari canvas atau file.',
            'signaturepenerima_file.required_without' => 'Tanda tangan diperlukan, baik dari canvas atau file.',
        ]);

        $ccEmails = $request->cc_email ? implode(',', $request->cc_email) : null;

    if ($request->hasFile('evidence')) {
        $file = $request->file('evidence');
        $filename = 'TML' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('dokumen'), $filename);
        $evidence = $filename; // Simpan nama file ke variable $evidence
    }

        $signatureFileName = null;
        if ($request->signature) {
            $signaturePath = public_path('admin/img');
            $signatureData = $request->signature;
            list(, $signatureData) = explode(',', $signatureData);
            $signatureFileName = 'signature_' . uniqid() . '.png';
            file_put_contents($signaturePath . '/' . $signatureFileName, base64_decode($signatureData));
        } elseif ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $signatureFileName = 'signature_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin/img'), $signatureFileName);
        }

        // Menyimpan tanda tangan penerima (signaturepenerima)
        $signaturePenerimaFileName = null;
        if ($request->signaturepenerima) {
            $signaturePenerimaPath = public_path('admin/img');
            $signaturePenerimaData = $request->signaturepenerima;
            list(, $signaturePenerimaData) = explode(',', $signaturePenerimaData);
            $signaturePenerimaFileName = 'signature_penerima_' . uniqid() . '.png';
            file_put_contents($signaturePenerimaPath . '/' . $signaturePenerimaFileName, base64_decode($signaturePenerimaData));
        } elseif ($request->hasFile('signaturepenerima_file')) {
            $file = $request->file('signaturepenerima_file');
            $signaturePenerimaFileName = 'signature_penerima_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin/img'), $signaturePenerimaFileName);
        }

        try {
            $lastPpk = Ppk::latest()->first();
            $sequence = $lastPpk ? intval(substr($lastPpk->nomor_surat, 0, 3)) + 1 : 1;
            $nomor = str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $bulan = date('m');
            $tahun = date('Y');
            $semester = ($bulan <= 6) ? 'SEM 1' : 'SEM 2';

            $user = User::find($request->penerima);
            $divisi = $request->divisipenerima ?? $user->divisi;

            $nomorSurat = "$nomor/MFG/$divisi/$bulan/$tahun-$semester";

            DB::beginTransaction();

            // Simpan data PPK
            $buatppk = Ppk::create([
                'judul' => $request->judul,
                'jenisketidaksesuaian' => is_array($request->jenisketidaksesuaian) ? implode(',', $request->jenisketidaksesuaian) : null,
                'pembuat' => auth()->id(),
                'emailpembuat' => $request->emailpembuat,
                'divisipembuat' => $request->divisipembuat,
                'penerima' => $request->penerima,
                'emailpenerima' => $request->emailpenerima,
                'divisipenerima' => $request->divisipenerima,
                'cc_email' => $ccEmails,
                'evidence' => $evidence,
                'nomor_surat' => $nomorSurat,
                'signature' => $signatureFileName,
            ]);

            // Membuat entri untuk PPK kedua secara otomatis
            Ppkkedua::create([
                'id_formppk' => $buatppk->id, // Mengaitkan ID PPK ke PPK kedua
                'identifikasi' => null, // Ini akan diisi oleh penerima
                'signaturepenerima' => null,
            ]);

            DB::commit();

            return redirect()->route('ppk.index')->with('success', 'Data PPK berhasil disimpan.✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function storeFormPpkkedua(Request $request)
    {
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id',
            'identifikasi' => 'nullable|string|max:1000',
            'signaturepenerima' => 'nullable|string',
            'signaturepenerima_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $signaturePenerimaFileName = null;
        if ($request->signaturepenerima) {
            $signaturePenerimaPath = public_path('admin/img');
            $signaturePenerimaData = $request->signaturepenerima;
            list(, $signaturePenerimaData) = explode(',', $signaturePenerimaData);
            $signaturePenerimaFileName = 'signature_penerima_' . uniqid() . '.png';
            file_put_contents($signaturePenerimaPath . '/' . $signaturePenerimaFileName, base64_decode($signaturePenerimaData));
        } elseif ($request->hasFile('signaturepenerima_file')) {
            $file = $request->file('signaturepenerima_file');
            $signaturePenerimaFileName = 'signature_penerima_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin/img'), $signaturePenerimaFileName);
        }

        try {
            // Mengupdate data PPK kedua
            Ppkkedua::where('id_formppk', $request->id_formppk)->update([
                'identifikasi' => $request->identifikasi,
                'signaturepenerima' => $signaturePenerimaFileName,
            ]);

            return redirect()->route('ppk.index')->with('success', 'Form kedua berhasil disimpan.✅');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }
    public function exportSingle($id)
    {

        $ppk = Ppk::with('pembuatUser', 'penerimaUser')->findOrFail($id);
        $ppkdua = Ppkkedua::where('id_formppk', $id)->first(); // Ambil data Ppkkedua

        // Membersihkan nomor_surat dari karakter yang tidak diizinkan
        $cleanedNomorSurat = preg_replace('/[\/\\\:*?"<>|]/', '_', $ppk->nomor_surat);

        // Menggunakan nomor surat yang sudah dibersihkan sebagai nama file
        $fileName = '' . $cleanedNomorSurat . '.xlsx';

        return Excel::download(new PpkExport($ppk, $ppkdua), $fileName);
    }

}
