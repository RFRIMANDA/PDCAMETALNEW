<?php

namespace App\Http\Controllers;

use App\Models\Ppk;
use App\Models\Ppkkedua;
use App\Models\Ppkketiga;
use App\Models\Ppkkeempat;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PpkExport;
use App\Mail\KirimEmail;
use App\Mail\KirimEmail2;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PpkController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $ppks = Ppk::where('pembuat', $userId)
                    ->orWhere('penerima', $userId)
                    ->with(['formppk2','formppk3','formppk4', 'pembuatUser', 'penerimaUser'])
                    ->get();

        return view('ppk.index', compact('ppks'));
    }

    public function create()
    {
        $data = User::all();
        return view('ppk.create', compact('data'));

    }

    public function create2($id)
    {
        $data = User::all();
        $ppk = Ppk::findOrFail($id);
        return view('ppk.create2', ['id' => $id], compact('data', 'ppk'));
    }

    public function create3($id)
    {
        $data = User::all();
        $ppk = Ppk::findOrFail($id);
        $users = User::all();
        return view('ppk.create3', ['id' => $id], compact('data', 'ppk','users'));
    }


    public function create4($id)
    {
        $data = User::all();
        $ppk = Ppk::findOrFail($id);
        return view('ppk.create4', ['id' => $id], compact('data', 'ppk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'nullable|string|max:1000',
            'jenisketidaksesuaian' => 'nullable|array',
            'jenisketidaksesuaian.*' => 'in:SISTEM,AUDIT,PRODUK,PROSES',
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
        ]);

        $ccEmails = $request->cc_email ? implode(',', $request->cc_email) : null;
        $evidence = $this->handleFileUpload($request, 'evidence', 'dokumen', 'TML');

        $signatureFileName = $this->handleSignature($request, 'signature', 'signature_file');

        try {
            DB::beginTransaction();

            $lastPpk = Ppk::latest()->first();
            $sequence = $lastPpk ? intval(substr($lastPpk->nomor_surat, 0, 3)) + 1 : 1;
            $nomor = str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $bulan = date('m');
            $tahun = date('Y');
            $semester = ($bulan <= 6) ? 'SEM 1' : 'SEM 2';

            $user = User::find($request->penerima);
            $divisi = $request->divisipenerima ?? $user->divisi;
            $nomorSurat = "$nomor/MFG/$divisi/$bulan/$tahun-$semester";

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

            Ppkkedua::create(['id_formppk' => $buatppk->id]);
            Ppkketiga::create(['id_formppk' => $buatppk->id]);
            Ppkkeempat::create(['id_formppk' => $buatppk->id]);

            // Kirim Email
            $data_email = [
                'subject' => "Penerbitan No PPK {$nomorSurat}",
                'sender_name' => "{$request->emailpembuat}, {$request->divisipembuat}",
                'paragraf1' => "Dear {$request->emailpenerima}, {$divisi}",
                'paragraf2' => "Berikut Terlampir PPK",
                'paragraf3' => $nomorSurat,
                'paragraf4' => $request->judul,
                'paragraf5' => "yang diajukan oleh",
                'paragraf6' => "Video panduan copy link berikut->bit.ly/pengajuanppk",
                'paragraf7' => "Untuk menambahkan Evidence dan update progress silahkan klik link di bawah ini",
                'paragraf8' => route('ppk.index'),
            ];

            Mail::to($request->emailpenerima)->cc($request->cc_email)->send(new kirimemail($data_email));

            DB::commit();
            return redirect()->route('ppk.index')->with('success', 'Data PPK berhasil disimpan.✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    private function handleFileUpload($request, $inputName, $path, $prefix)
    {
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $filename = $prefix . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($path), $filename);
            return $filename;
        }
        return null;
    }

    private function handleSignature($request, $signatureField, $fileField, $prefix = 'signature_')
    {
        $signaturePath = public_path('admin/img');
        if ($request->$signatureField) {
            list(, $signatureData) = explode(',', $request->$signatureField);
            $signatureFileName = $prefix . uniqid() . '.png';
            file_put_contents($signaturePath . '/' . $signatureFileName, base64_decode($signatureData));
            return $signatureFileName;
        } elseif ($request->hasFile($fileField)) {
            $file = $request->file($fileField);
            $signatureFileName = $prefix . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($signaturePath, $signatureFileName);
            return $signatureFileName;
        }
        return null;
    }

    public function store2(Request $request)
    {
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id',
            'identifikasi' => 'nullable|string|max:1000',
            'signaturepenerima' => 'nullable|string',
            'signaturepenerima_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $signaturePenerimaFileName = $this->handleSignature($request, 'signaturepenerima', 'signaturepenerima_file', 'signature_penerima_');

        try {
            Ppkkedua::where('id_formppk', $request->id_formppk)->update([
                'identifikasi' => $request->identifikasi,
                'signaturepenerima' => $signaturePenerimaFileName,
            ]);

             // Kirim Email
            $data_email = [
                'subject' => 'Notifikasi Pembaruan PPK',
                'sender_name' => auth()->user()->email,
                'isi' => "PPK telah diupdate dengan NO PPK {$request->nomorSurat} telah diperbarui.",
            ];
            $penerima = Ppk::find($request->id_formppk)->emailpenerima; // Ambil email penerima dari database
            Mail::to($penerima)->send(new kirimemail2($data_email));

            return redirect()->route('ppk.index')->with('success', 'Form kedua berhasil disimpan.✅');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function store3(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id',
            'penanggulangan' => 'nullable|string|max:1000',
            'pencegahan' => 'nullable|string|max:1000',
            'tgl_penanggulangan' => 'nullable|date',
            'tgl_pencegahan' => 'nullable|date',
            'pic1' => 'nullable|string',
            'pic2' => 'nullable|string',
        ]);

        try {
            Ppkketiga::where('id_formppk', $request->id_formppk)->update([
                'penanggulangan' => $request->penanggulangan,
                'pencegahan' => $request->pencegahan,
                'tgl_penanggulangan' => $request->tgl_penanggulangan,
                'tgl_pencegahan' => $request->tgl_pencegahan,
                'pic1' => $request->pic1,
                'pic2' => $request->pic2,
            ]);

            // Kirim Email
            $data_email = [
                'subject' => 'Notifikasi Pembaruan Form Ketiga',
                'sender_name' => auth()->user()->email,
                'isi' => "Form ketiga dengan ID {$request->id_formppk} telah diperbarui.",
            ];
            $penerima = Ppk::find($request->id_formppk)->emailpenerima; // Ambil email penerima dari database
            Mail::to($penerima)->send(new KirimEmail2($data_email));

            return redirect()->route('ppk.index')->with('success', 'Form ketiga berhasil disimpan.✅');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function store4 (Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id',
            'catatan' => 'nullable|string|max:1000',
            'tgl_verif' => 'nullable|date',
        ]);

        try {
            Ppkkeempat::where('id_formppk', $request->id_formppk)->update([
                'catatan' => $request->catatan,
                'tgl_verif' => $request->tgl_verif,
            ]);

            // Kirim Email
            $data_email = [
                'subject' => 'Notifikasi Pembaruan Form Keempat',
                'sender_name' => auth()->user()->email,
                'isi' => "Form Keempat dengan ID {$request->id_formppk} telah diperbarui.",
            ];
            $penerima = Ppk::find($request->id_formppk)->emailpenerima; // Ambil email penerima dari database
            Mail::to($penerima)->send(new KirimEmail2($data_email));

            return redirect()->route('ppk.index')->with('success', 'Form keempat berhasil disimpan.✅');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function exportSingle($id)
    {
        $ppk = Ppk::with('pembuatUser', 'penerimaUser')->findOrFail($id);
        $ppkdua = Ppkkedua::where('id_formppk', $id)->first();
        $ppktiga = Ppkketiga::where('id_formppk', $id)->first();
        $ppkempat = Ppkkeempat::where('id_formppk', $id)->first();

        $cleanedNomorSurat = preg_replace('/[\/\\\:*?"<>|]/', '_', $ppk->nomor_surat);
        $fileName = '' . $cleanedNomorSurat . '.xlsx';

        return Excel::download(new PpkExport($ppk, $ppkdua, $ppktiga, $ppkempat), $fileName);
    }

    public function email()
    {
        $pesan= "<b>HALLO IKY</b>";
        $pesan .= "assalamualaikum";
        $data_email=[
            'subject' => 'test',
            'sender_name' => 'rifkyfrimanda@gmail.com',
            'isi' => $pesan
        ];
        Mail::to("odanuartha@gmail.com")->send(new kirimemail($data_email));
        return '<h1>SUCCESS MENGIRIM EMAIL</h1>';
    }
}
