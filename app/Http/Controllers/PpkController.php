<?php

namespace App\Http\Controllers;

use App\Models\Ppk;
use App\Models\Ppkkedua;
use App\Models\Ppkketiga;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
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
                    ->with(['formppk2','formppk3', 'pembuatUser', 'penerimaUser'])
                    ->get();
        return view('ppk.index', compact('ppks'));
    }

    public function detail($id)
    {
        $userId = auth()->id();
        $ppk = Ppk::with(['pembuatUser', 'penerimaUser', 'formppk2.pic1User', 'formppk2.pic2User', 'formppk3'])
            ->findOrFail($id);
            $ppks = Ppk::where('pembuat', $userId)
                    ->orWhere('penerima', $userId)
                    ->with(['formppk2','formppk3', 'pembuatUser', 'penerimaUser'])
                    ->get();

        return view('ppk.detail', compact('ppk','ppks'));
    }

    public function create()
    {
        $data = User::all();
        return view('ppk.create', compact('data'));
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
            'evidence' => 'nullable|array',
            'evidence.*' => 'file|mimes:jpg,jpeg,png,xlsx,xls,doc,docx|max:5120', // ensure proper validation for each file
            'signature' => 'nullable|string',
            'signature_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'identifikasi' => 'nullable|string|max:1000',
            'signaturepenerima' => 'nullable|string',
            'signaturepenerima_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Store other data
        $ccEmails = $request->cc_email ? implode(',', $request->cc_email) : null;
        $signatureFileName = $this->handleSignature($request, 'signature', 'signature_file');

        try {
            DB::beginTransaction();

             // Handling evidence files
            $evidences = [];
            if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/evidence', $filename);
                $evidences[] = 'evidence/' . $filename;
            }
        }
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
                'evidence' => json_encode($evidences),  // Store the evidence paths here
                'nomor_surat' => $nomorSurat,
                'signature' => $signatureFileName,
            ]);

            Ppkkedua::create(['id_formppk' => $buatppk->id]);
            Ppkketiga::create(['id_formppk' => $buatppk->id]);

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

    public function update(Request $request, $id)
{
    // Validate the input data
    $request->validate([
        'judul' => 'required|string|max:255',
        'jenisketidaksesuaian' => 'nullable|array',
        'pembuat' => 'required|string|max:255',
        'emailpembuat' => 'nullable|email',
        'divisipembuat' => 'nullable|string|max:255',
        'penerima' => 'required|integer|exists:user,id',
        'emailpenerima' => 'nullable|email',
        'divisipenerima' => 'nullable|string|max:255',
        'signature' => 'nullable|string',
        'signature_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'cc_email' => 'nullable|array',
        'cc_email.*' => 'nullable|email',
        'evidence' => 'nullable|array',
        'evidence.*' => 'file|mimes:jpg,jpeg,png,xlsx,xls,doc,docx|max:2048',
        'delete_evidence' => 'nullable|array',
    ]);

    // Find the PPK record
    $ppk = Ppk::findOrFail($id);

    // Handle evidence files
    $evidences = $ppk->evidence ? json_decode($ppk->evidence, true) : [];

    // Remove evidence if requested
    if ($request->filled('delete_evidence')) {
        foreach ($request->delete_evidence as $deleteEvidence) {
            // Remove file from storage
            Storage::delete('public/' . $deleteEvidence);
            // Remove from array
            $evidences = array_filter($evidences, fn($evidence) => $evidence !== $deleteEvidence);
        }
    }

    // Add new evidence files
    if ($request->hasFile('evidence')) {
        foreach ($request->file('evidence') as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/evidence', $filename);
            $evidences[] = 'evidence/' . $filename;
        }
    }

    // Update the evidence field
    $ppk->evidence = json_encode(array_values($evidences));

    // Process CC Emails if provided
    $ccEmails = $request->input('cc_email', []);
    $ppk->cc_email = implode(',', $ccEmails);

    // Update signature file if provided
    if ($request->hasFile('signature_file')) {
        if ($ppk->signature_file) {
            Storage::delete('public/' . $ppk->signature_file);
        }
        $ppk->signature_file = $request->file('signature_file')->store('public/signatures');
    }

    // Update other fields
    $ppk->judul = $request->input('judul');
    $ppk->jenisketidaksesuaian = $request->input('jenisketidaksesuaian') ? implode(',', $request->input('jenisketidaksesuaian')) : null;
    $ppk->pembuat = $request->input('pembuat');
    $ppk->emailpembuat = $request->input('emailpembuat');
    $ppk->divisipembuat = $request->input('divisipembuat');
    $ppk->penerima = $request->input('penerima');
    $ppk->emailpenerima = $request->input('emailpenerima');
    $ppk->divisipenerima = $request->input('divisipenerima');

    // Save the updated PPK record
    $ppk->save();

    return redirect()->route('ppk.index')->with('success', 'PPK updated successfully!');
}


    public function edit($id)
    {
        $ppk = Ppk::findOrFail($id);

        // Cek apakah evidence ada dan tidak kosong, kemudian ubah menjadi array
        $evidenceFiles = $ppk->evidence ? explode(',', $ppk->evidence) : [];

        $users = User::all();
        // dd($evidenceFiles);

        return view('ppk.edit', compact('ppk', 'users', 'evidenceFiles'));
    }

    public function create2($id)
    {
        $data = User::all();
        $ppk = Ppk::findOrFail($id);  // This will throw an exception if not found
        if (!$ppk) {
            // Handle the case where Ppk is not found (optional)
            return redirect()->route('ppk.index')->with('error', 'Record not found!');
        }
        return view('ppk.create2', compact('data', 'ppk', 'id'));
    }

    public function store2(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'identifikasi' => 'nullable|string|max:1000', // Hanya penerima yang bisa isi
            'penanggulangan' => 'nullable|string|max:1000',
            'pencegahan' => 'nullable|string|max:1000',
            'tgl_penanggulangan' => 'nullable|date',
            'tgl_pencegahan' => 'nullable|date',
            'pic1' => 'nullable|string',
            'pic2' => 'nullable|string',
            'signaturepenerima' => 'nullable|string',
            'signaturepenerima_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $signatureFileName = $this->handleSignature($request, 'signaturepenerima', 'signature_file');
        try {
            // Data yang akan diupdate

            $updateData = [
                'identifikasi' => $request->identifikasi,
                'penanggulangan' => $request->penanggulangan,
                'pencegahan' => $request->pencegahan,
                'tgl_penanggulangan' => $request->tgl_penanggulangan,
                'tgl_pencegahan' => $request->tgl_pencegahan,
                'pic1' => $request->pic1,
                'pic2' => $request->pic2,
                'signaturepenerima' => $signatureFileName,
            ];
            Ppkkedua::where('id_formppk', $request->id_formppk)->update($updateData);

            return redirect()->route('ppk.index')->with('success', 'Data berhasil diperbarui.✅');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit2($id)
    {
        // Retrieve the PPK record by ID
        $ppk = Ppkkedua::findOrFail($id);

        // Check if there are any evidence files, split them into an array
        $evidenceFiles = $ppk->evidence ? explode(',', $ppk->evidence) : [];

        // Get all users for PIC dropdowns
        $users = User::all();

        // Pass the PPK, users, and evidence files to the view
        return view('ppk.edit2', compact('ppk', 'users', 'evidenceFiles'));
    }

    public function update2(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'identifikasi' => 'nullable|string|max:1000',
            'penanggulangan' => 'nullable|string|max:1000',
            'pencegahan' => 'nullable|string|max:1000',
            'tgl_penanggulangan' => 'nullable|date',
            'tgl_pencegahan' => 'nullable|date',
            'pic1' => 'nullable|string',
            'pic2' => 'nullable|string',
            'signaturepenerima' => 'nullable|string',
            'signaturepenerima_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Handle file upload for signature if present
            if ($request->hasFile('signaturepenerima_file')) {
                $signatureFile = $request->file('signaturepenerima_file');
                $filePath = $signatureFile->store('signatures', 'public');
            } else {
                $filePath = null;
            }

            // If a signature was drawn, capture the base64 data
            $signature = $request->signaturepenerima ?: $filePath;

            // Data to update
            $updateData = [
                'identifikasi' => $request->identifikasi,
                'penanggulangan' => $request->penanggulangan,
                'pencegahan' => $request->pencegahan,
                'tgl_penanggulangan' => $request->tgl_penanggulangan,
                'tgl_pencegahan' => $request->tgl_pencegahan,
                'pic1' => $request->pic1,
                'pic2' => $request->pic2,
                // 'signaturepenerima' => $signature,
            ];

            // Update the PPK record
            Ppkkedua::where('id', $id)->update($updateData);

            // Return success message
            return redirect()->route('ppk.index')->with('success', 'Data berhasil diperbarui.✅');
        } catch (\Exception $e) {
            // Handle errors
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function create3($id)
    {
        $data = User::all();
        $ppk = Ppk::findOrFail($id);
        $users = User::all();
        return view('ppk.create3', ['id' => $id], compact('data', 'ppk','users'));
    }

    public function store3(Request $request)
    {
        // Validasi input
        // dd($request->all());
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id', // Memastikan id_formppk ada di tabel formppk
            'verifikasi' => 'nullable|string|max:1000',
            'tinjauan' => 'nullable|string|max:1000',
            'status' => 'required|in:TRUE,FALSE', // Validasi status dengan nilai TRUE atau FALSE
        ]);

        try {
            // Perbarui data pada tabel Ppkketiga berdasarkan id_formppk
            Ppkketiga::where('id_formppk', $request->id_formppk)->update([
                'verifikasi' => $request->verifikasi,
                'tinjauan' => $request->tinjauan,
                'status' => $request->status,
            ]);

            // Kirim email notifikasi
            $data_email = [
                'subject' => 'Notifikasi Pembaruan Form Keempat',
                'sender_name' => auth()->user()->email, // Pengirim dari email pengguna yang sedang login
                'isi' => "Form Keempat dengan ID {$request->id_formppk} telah diperbarui.",
            ];

            // Ambil email penerima dari Ppk berdasarkan id_formppk
            $penerima = Ppk::find($request->id_formppk)->emailpenerima;

            // Kirim email ke penerima
            Mail::to($penerima)->send(new KirimEmail2($data_email));

            // Kembalikan response jika sukses
            return redirect()->route('ppk.index')->with('success', 'Form keempat berhasil disimpan.✅');
        } catch (\Exception $e) {
            // Kembalikan response jika terjadi error
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit3($id)
    {
        // Ambil data berdasarkan id_formppk
        $ppk = Ppkketiga::findOrFail($id); // Menemukan data berdasarkan id_formppk
        $users = User::all(); // Ambil semua data pengguna untuk dropdown PIC
        return view('ppk.edit3', compact('ppk', 'users'));
    }
    public function update3(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id',
            'verifikasi' => 'nullable|string|max:1000',
            'tinjauan' => 'nullable|string|max:1000',
            'status' => 'required|in:TRUE,FALSE', // Validasi status dengan nilai TRUE atau FALSE
        ]);

        try {
            // Cari data berdasarkan id_formppk
            $ppk = Ppkketiga::findOrFail($id);

            // Update data
            $ppk->update([
                'verifikasi' => $request->verifikasi,
                'tinjauan' => $request->tinjauan,
                'status' => $request->status,
            ]);

            // Kirim email notifikasi
            $data_email = [
                'subject' => 'Notifikasi Pembaruan Form Keempat',
                'sender_name' => auth()->user()->email, // Pengirim dari email pengguna yang sedang login
                'isi' => "Form Keempat dengan ID {$request->id_formppk} telah diperbarui.",
            ];

            // Ambil email penerima dari Ppk berdasarkan id_formppk
            $penerima = Ppk::find($request->id_formppk)->emailpenerima;

            // Kirim email ke penerima
            Mail::to($penerima)->send(new KirimEmail2($data_email));

            // Kembalikan response jika sukses
            return redirect()->route('ppk.index')->with('success', 'Form keempat berhasil diperbarui.✅');
        } catch (\Exception $e) {
            // Kembalikan response jika terjadi error
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function exportSingle($id)
    {
        $ppk = Ppk::with('pembuatUser', 'penerimaUser')->findOrFail($id);
        $ppkdua = Ppkkedua::where('id_formppk', $id)->first();
        $ppktiga = Ppkketiga::where('id_formppk', $id)->first();

        $cleanedNomorSurat = preg_replace('/[\/\\\:*?"<>|]/', '_', $ppk->nomor_surat);
        $fileName = '' . $cleanedNomorSurat . '.xlsx';

        return Excel::download(new PpkExport($ppk, $ppkdua, $ppktiga), $fileName);
    }

    // public function email()
    // {
    //     $pesan= "<b>HALLO IKY</b>";
    //     $pesan .= "assalamualaikum";
    //     $data_email=[
    //         'subject' => 'test',
    //         'sender_name' => 'rifkyfrimanda@gmail.com',
    //         'isi' => $pesan
    //     ];
    //     Mail::to("odanuartha@gmail.com")->send(new kirimemail($data_email));
    //     return '<h1>SUCCESS MENGIRIM EMAIL</h1>';
    // }
}
