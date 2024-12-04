<?php

namespace App\Http\Controllers;

use App\Models\Ppk;
use App\Models\Ppkkedua;
use App\Models\Ppkketiga;
use App\Models\User;
use App\Models\Divisi;
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

            // Ambil parameter filter dari request
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $semester = $request->input('semester');
            $keyword = $request->input('keyword');
            $verifiedStatus = $request->input('status');

            // Query PPK dengan filter
            $ppks = Ppk::where(function ($query) use ($userId) {
                $query->where('pembuat', $userId)
                    ->orWhere('penerima', $userId);
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($semester, function ($query) use ($semester) {
                $query->where('nomor_surat', 'like', "%$semester");
            })
            ->when($keyword, function ($query, $keyword) {
                $query->where('nomor_surat', 'like', "%$keyword%");
            })
            ->when($verifiedStatus, function ($query, $verifiedStatus) {
                if ($verifiedStatus === 'VERIFIED') {
                    $query->whereHas('formppk3', function ($subQuery) {
                        $subQuery->whereNotNull('verifikasi');
                    });
                } elseif ($verifiedStatus === 'WAITING') {
                    $query->whereHas('formppk3', function ($subQuery) {
                        $subQuery->whereNull('verifikasi');
                    });
                }
            })

            ->with(['formppk2', 'formppk3', 'pembuatUser', 'penerimaUser'])
            ->get();

    return view('ppk.index', compact('ppks'));

    }

    public function index2(Request $request)
{
    // Periksa apakah pengguna memiliki peran 'admin'
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $semester = $request->input('semester');
    $user = $request->input('user');
    $keyword = $request->input('keyword');
    $status = $request->input('status');

    // Query semua PPK untuk role admin
    $sendingPpks = Ppk::when($startDate, function ($query, $startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        })
        ->when($endDate, function ($query, $endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        })
        ->when($semester, function ($query, $semester) {
            $query->where('nomor_surat', 'like', "%$semester");
        })
        ->when($user, function ($query, $user) {
            $query->where('penerima', $user);
        })
        ->when($keyword, function ($query, $keyword) {
            $query->where('nomor_surat', 'like', "%$keyword%");
        })
        ->when($status, function ($query, $status) {
            if ($status === 'VERIFIED') {
                $query->whereHas('formppk3', function ($subQuery) {
                    $subQuery->whereNotNull('verifikasi');
                });
            } elseif ($status === 'WAITING') {
                $query->whereHas('formppk3', function ($subQuery) {
                    $subQuery->whereNull('verifikasi');
                });
            }
        })
        ->get();

    $acceptingPpks = Ppk::when($startDate, function ($query, $startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        })
        ->when($endDate, function ($query, $endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        })
        ->when($semester, function ($query, $semester) {
            $query->where('nomor_surat', 'like', "%$semester");
        })
        ->when($user, function ($query, $user) {
            $query->where('pembuat', $user);
        })
        ->when($keyword, function ($query, $keyword) {
            $query->where('nomor_surat', 'like', "%$keyword%");
        })
        ->when($status, function ($query, $status) {
            if ($status === 'VERIFIED') {
                $query->whereHas('formppk3', function ($subQuery) {
                    $subQuery->whereNotNull('verifikasi');
                });
            } elseif ($status === 'WAITING') {
                $query->whereHas('formppk3', function ($subQuery) {
                    $subQuery->whereNull('verifikasi');
                });
            }
        })
        ->get();

    // Ambil daftar pengguna untuk dropdown
    $userList = User::pluck('nama_user', 'id');

    return view('ppk.index2', compact('sendingPpks', 'acceptingPpks', 'userList'));
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

        return $signatureFileName; // Simpan sebagai nama file untuk akses lebih mudah
    } elseif ($request->hasFile($fileField)) {
        $file = $request->file($fileField);
        $signatureFileName = $prefix . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($signaturePath, $signatureFileName);

        return $signatureFileName; // Simpan sebagai nama file
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
    try {
        DB::beginTransaction();

        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenisketidaksesuaian' => 'nullable|array',
            'jenisketidaksesuaian.*' => 'in:SISTEM,AUDIT,PRODUK,PROSES',
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
            'evidence.*' => 'file|mimes:jpg,jpeg,png,xlsx,xls,doc,docx|max:5120', // Pastikan ukuran file sesuai kebutuhan
            'delete_evidence' => 'nullable|array',
        ]);

        // Temukan data PPK berdasarkan ID
        $ppk = Ppk::findOrFail($id);

        // Ambil evidence yang sudah ada
        $evidences = $ppk->evidence ? json_decode($ppk->evidence, true) : [];

        // Hapus file evidence lama jika dipilih
        if ($request->filled('delete_evidence')) {
            foreach ($request->delete_evidence as $deleteEvidence) {
                // Hapus file dari penyimpanan
                if (Storage::exists('public/' . $deleteEvidence)) {
                    Storage::delete('public/' . $deleteEvidence);
                }

                // Hapus path dari array evidence
                $evidences = array_filter($evidences, fn($evidence) => $evidence !== $deleteEvidence);
            }
        }

        // Simpan file evidence baru
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                // Buat nama file unik dengan timestamp dan nama asli file
                $filename = time() . '_' . $file->getClientOriginalName();

                // Simpan file ke folder 'public/evidence'
                $file->storeAs('public/evidence', $filename);

                // Tambahkan path file ke array evidence
                $evidences[] = 'evidence/' . $filename;
            }
        }

        // Perbarui data evidence di database
        $ppk->evidence = json_encode(array_values($evidences));

        // Proses CC Emails jika disediakan
        $ccEmails = $request->input('cc_email', []);
        $ppk->cc_email = implode(',', $ccEmails);

        // Perbarui data lain
        $ppk->judul = $request->input('judul');
        $ppk->jenisketidaksesuaian = $request->input('jenisketidaksesuaian') ? implode(',', $request->input('jenisketidaksesuaian')) : null;
        $ppk->pembuat = $request->input('pembuat');
        $ppk->emailpembuat = $request->input('emailpembuat');
        $ppk->divisipembuat = $request->input('divisipembuat');
        $ppk->penerima = $request->input('penerima');
        $ppk->emailpenerima = $request->input('emailpenerima');
        $ppk->divisipenerima = $request->input('divisipenerima');

        $lastPpk = Ppk::latest()->first();
                $sequence = $lastPpk ? intval(substr($lastPpk->nomor_surat, 0, 3)) + 1 : 1;
                $nomor = str_pad($sequence, 3, '0', STR_PAD_LEFT);
                $bulan = date('m');
                $tahun = date('Y');
                $semester = ($bulan <= 6) ? 'SEM 1' : 'SEM 2';

                $user = User::find($request->penerima);
                $divisi = $request->divisipenerima ?? $user->divisi;
                $nomorSurat = "$nomor/MFG/$divisi/$bulan/$tahun-$semester";

            // Kirim Email
            $data_email = [
                'subject' => "Pembaharuan No PPK {$nomorSurat}",
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
        // Simpan perubahan
        $ppk->save();

        DB::commit();

        return redirect()->route('ppk.index')->with('success', 'PPK updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'Failed to update data: ' . $e->getMessage()]);
    }
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

    private function handleSignature2($request, $signatureField, $fileField, $prefix = 'signature_')
{
    $signaturePath = public_path('admin/img'); // Pastikan folder ini memiliki izin tulis
    if (!is_dir($signaturePath)) {
        mkdir($signaturePath, 0755, true); // Buat folder jika belum ada
    }

    // Jika signature berbentuk data base64
    if ($request->$signatureField) {
        list(, $signatureData) = explode(',', $request->$signatureField);
        $signatureFile = $prefix . uniqid() . '.png';
        file_put_contents($signaturePath . '/' . $signatureFile, base64_decode($signatureData));

        return $signatureFile; // Kembalikan nama file yang disimpan
    }
    // Jika signature berbentuk file yang diunggah
    elseif ($request->hasFile($fileField)) {
        $file = $request->file($fileField);
        $signatureFile = $prefix . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($signaturePath, $signatureFile);

        return $signatureFile; // Kembalikan nama file
    }

    return null; // Jika tidak ada signature
}

    public function store2(Request $request)
        {
            // Validate data
            $request->validate([
                'identifikasi' => 'nullable|string|max:1000',
                'penanggulangan' => 'nullable|string|max:1000',
                'pencegahan' => 'nullable|string|max:1000',
                'tgl_penanggulangan' => 'nullable|date',
                'tgl_pencegahan' => 'nullable|date',
                'pic1' => 'nullable|string',
                'pic2' => 'nullable|string',
                'pic1_other' => 'nullable|string|max:255',  // pic1_other as a regular string
                'pic2_other' => 'nullable|string|max:255',  // pic2_other as a regular string
                'signaturepenerima' => 'nullable|string',
                'signaturepenerima_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Process the signature penerima
            $signatureFile = $this->handleSignature2($request, 'signaturepenerima', 'signaturepenerima_file');

            // Handle "other" options for pic1 and pic2

            $pic1 = $request->filled('pic1_other') ? $request->pic1_other : $request->pic1;
            $pic2 = $request->filled('pic2_other') ? $request->pic2_other : $request->pic2;

            try {
                // Data to be updated
                $updateData = [
                    'identifikasi' => $request->identifikasi,
                    'penanggulangan' => $request->penanggulangan,
                    'pencegahan' => $request->pencegahan,
                    'tgl_penanggulangan' => $request->tgl_penanggulangan,
                    'tgl_pencegahan' => $request->tgl_pencegahan,
                    'pic1' => $pic1,  // Store the value for pic1 (either from dropdown or other)
                    'pic2' => $pic2,  // Store the value for pic2 (either from dropdown or other)
                    'signaturepenerima' => $signatureFile,  // Save signature penerima
                ];

                // Update data in Ppkkedua table
                Ppkkedua::where('id_formppk', $request->id_formppk)->update($updateData);

                return redirect()->route('ppk.index')->with('success', 'Data berhasil diperbarui.✅');
            } catch (\Exception $e) {
                // Catch and display errors
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
        'pic1_other' => 'nullable|string|max:255', // for custom input
        'pic2_other' => 'nullable|string|max:255', // for custom input
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

        // If a signature was drawn, capture the base64 data or uploaded file path
        $signature = $request->signaturepenerima ?: $filePath;

        // Handle "other" options for pic1 and pic2
        $pic1 = $request->pic1 === 'other' ? $request->pic1_other : $request->pic1;
        $pic2 = $request->pic2 === 'other' ? $request->pic2_other : $request->pic2;

        // Data to update
        $updateData = [
            'identifikasi' => $request->identifikasi,
            'penanggulangan' => $request->penanggulangan,
            'pencegahan' => $request->pencegahan,
            'tgl_penanggulangan' => $request->tgl_penanggulangan,
            'tgl_pencegahan' => $request->tgl_pencegahan,
            'pic1' => $pic1,  // Store the value for pic1 (either from dropdown or other)
            'pic2' => $pic2,  // Store the value for pic2 (either from dropdown or other)
            'signaturepenerima' => $signature,  // Store signature penerima
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
        // Validation
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id',
            'verifikasi' => 'nullable|string|max:1000',
            'verifikasi_img.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk setiap file dalam array
            'tinjauan' => 'nullable|string|max:1000',
            'status' => 'required|in:TRUE,FALSE',
            'newppk' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction(); // Start a transaction
        try {
            // Prepare data for update
            $data = [
                'verifikasi' => $request->verifikasi,
                'tinjauan' => $request->tinjauan,
                'status' => $request->status,
                'newppk' => $request->newppk,
            ];

            // Array to store image file paths
            $verif_img = [];

            // Process the uploaded images
            if ($request->hasFile('verifikasi_img')) {
                foreach ($request->file('verifikasi_img') as $file) {
                    // Generate unique filename and store it
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/verifikasi', $filename);
                    // Add file path to the array
                    $verif_img[] = 'verifikasi/' . $filename;
                }

                // Convert the image paths array to JSON
                $data['verifikasi_img'] = json_encode($verif_img);
            }

            // Update the database record with the data
            Ppkketiga::where('id_formppk', $request->id_formppk)->update($data);

            DB::commit(); // Commit the transaction

            // Send email notification
            $data_email = [
                'subject' => 'Notifikasi Pembaruan Form Keempat',
                'sender_name' => auth()->user()->email,
                'isi' => "Form Keempat dengan ID {$request->id_formppk} telah diperbarui.",
            ];

            $penerima = Ppk::find($request->id_formppk)->emailpenerima;
            Mail::to($penerima)->send(new KirimEmail2($data_email));

            // Return a success response
            return redirect()->route('ppk.index')->with('success', 'Form keempat berhasil disimpan.✅');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if an error occurs
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit3($id)
    {
        // Ambil data berdasarkan id_formppk
        $ppk = Ppkketiga::findOrFail($id); // Menemukan data berdasarkan id_formppk
        $users = User::all(); // Ambil semua data pengguna untuk dropdown PIC
        $verifikasiFiles = $ppk->verifikasi_img ? explode(',', $ppk->verifikasiFiles) : [];
        return view('ppk.edit3', compact('ppk', 'users','verifikasiFiles'));
    }
    public function update3(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'id_formppk' => 'required|exists:formppk,id',
        'verifikasi' => 'nullable|string|max:1000',
        'tinjauan' => 'nullable|string|max:1000',
        'status' => 'required|in:TRUE,FALSE', // Validasi status dengan nilai TRUE atau FALSE
        'newppk' => 'nullable|string|max:255',
        'delete_verifikasi' => 'nullable|array', // Validasi status dengan nilai TRUE atau FALSE
    ]);

    try {
        // Cari data berdasarkan id_formppk
        $ppk = Ppkketiga::findOrFail($id);

        // Ambil evidence yang sudah ada
        $verif_imgs = $ppk->verifikasi_img ? json_decode($ppk->verifikasi_img, true) : [];

        // Hapus file evidence lama jika dipilih
        if ($request->filled('delete_verifikasi')) {
            foreach ($request->delete_verifikasi as $deleteVerifikasi) {
                // Hapus file dari penyimpanan
                if (Storage::exists('public/' . $deleteVerifikasi)) {
                    Storage::delete('public/' . $deleteVerifikasi);
                }

                // Hapus path dari array evidence
                $verif_imgs = array_filter($verif_imgs, fn($verif_img) => $verif_img !== $deleteVerifikasi);
            }
        }

        // Simpan file evidence baru
        if ($request->hasFile('verifikasi_img')) {
            foreach ($request->file('verifikasi_img') as $file) {
                // Buat nama file unik dengan timestamp dan nama asli file
                $filename = time() . '_' . $file->getClientOriginalName();

                // Simpan file ke folder 'public/verifikasi'
                $file->storeAs('public/verifikasi', $filename);

                // Tambahkan path file ke array evidence
                $verif_imgs[] = 'verifikasi/' . $filename;
            }
        }

        // Perbarui data evidence di database
        $ppk->verifikasi_img = json_encode(array_values($verif_imgs));

        // Perbarui data PPK lainnya
        $ppk->update([
            'verifikasi' => $request->verifikasi,
            'tinjauan' => $request->tinjauan,
            'status' => $request->status,
            'newppk' => $request->newppk,
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

    public function generatePdf($id)
{
    // Ambil data PPK berdasarkan ID
    $ppk = Ppk::with('pembuatUser', 'penerimaUser')->findOrFail($id);

    // Ambil data dari tabel Ppkkedua berdasarkan id_formppk
    $ppkkedua = Ppkkedua::where('id_formppk', $id)->first();
    $ppkketiga = Ppkketiga::where('id_formppk', $id)->first();

    // Dapatkan path dari signature
    $signaturePath = $ppk->signature ? public_path('admin/img/' . $ppk->signature) : null;
    $signaturePath2 = $ppkkedua && $ppkkedua->signaturepenerima ? public_path('admin/img/' . $ppkkedua->signaturepenerima): null;

    // Konversi signature ke base64 jika tersedia
    $signatureBase64 = null;
    if ($signaturePath && file_exists($signaturePath)) {
        $imageData = base64_encode(file_get_contents($signaturePath));
        $extension = pathinfo($signaturePath, PATHINFO_EXTENSION);
        $signatureBase64 = "data:image/{$extension};base64,{$imageData}";
    }
    $signaturePenerimaBase64 = null;
    if ($signaturePath2 && file_exists($signaturePath2)) {
        $imageData = base64_encode(file_get_contents($signaturePath2));
        $extension = pathinfo($signaturePath2, PATHINFO_EXTENSION);
        $signaturePenerimaBase64 = "data:image/{$extension};base64,{$imageData}";
    }

    // Data tambahan untuk view
    $data = [
        'ppk' => $ppk,
        'judul' => $ppk->judul,
        'nomor_surat' => $ppk->nomor_surat,
        'pembuat' => $ppk->pembuat,
        'emailpembuat' => $ppk->emailpembuat,
        'divisipembuat' => $ppk->divisipembuat,
        'penerima' => $ppk->penerima,
        'emailpenerima' => $ppk->emailpenerima,
        'divisipenerima' => $ppk->divisipenerima,
        'jenisketidaksesuaian' => $ppk->jenisketidaksesuaian,
        'evidence' => json_decode($ppk->evidence, true),
        'created_at' => $ppk->created_at,
        'signature' => $signatureBase64, // Berisi data base64 dari signature
        'signaturepenerima' => $signaturePenerimaBase64,
    ];

    // Check if Ppkkedua data exists and add pic1 and pic1_other logic
    if ($ppkkedua) {
        $data['identifikasi'] = $ppkkedua->identifikasi;
        $data['penanggulangan'] = $ppkkedua->penanggulangan;
        $data['pencegahan'] = $ppkkedua->pencegahan;
        $data['tgl_penanggulangan'] = $ppkkedua->tgl_penanggulangan;
        $data['tgl_pencegahan'] = $ppkkedua->tgl_pencegahan;

        // Handle pic1
        if ($ppkkedua->pic1 === 'other' && !empty($ppkkedua->pic1_other)) {
            $data['pic1'] = $ppkkedua->pic1_other; // Use pic1_other if pic1 is 'other'
        } elseif (is_numeric($ppkkedua->pic1)) {
            $data['pic1'] = $ppkkedua->pic1User->nama_user ?? 'Tidak ada PIC'; // Show pic1 user name if it's a user ID
        } else {
            $data['pic1'] = '-'; // Default message if no valid value
        }

        // Handle pic2
        if ($ppkkedua->pic2 === 'other' && !empty($ppkkedua->pic2_other)) {
            $data['pic2'] = $ppkkedua->pic2_other; // Use pic2_other if pic2 is 'other'
        } elseif (is_numeric($ppkkedua->pic2)) {
            $data['pic2'] = $ppkkedua->pic2User->nama_user ?? 'Tidak ada PIC'; // Show pic2 user name if it's a user ID
        } else {
            $data['pic2'] = '-'; // Default message if no valid value
        }
        $data['signaturepenerima'] = $signaturePenerimaBase64;
        $data['created_at'] = $ppkkedua->updated_at;
    }
    if ($ppkketiga) {
        $data['verifikasi'] = $ppkketiga->verifikasi;
        $data['verifikasi_img'] = $ppkketiga->verifikasi_img;
        $data['tinjauan'] = $ppkketiga->tinjauan;
        $data['status'] = $ppkketiga->status;
        $data['created_at_ppkketiga'] = $ppkketiga->updated_at;
        $data['newppk'] = $ppkketiga->newppk;
    }

    return view('pdf.ppk', $data);
}


public function accept($id)
{
    // Ambil data PPK berdasarkan ID
    $ppk = Ppk::with('pembuatUser', 'penerimaUser')->findOrFail($id);

    // Ambil data dari tabel Ppkkedua berdasarkan id_formppk
    $ppkkedua = Ppkkedua::where('id_formppk', $id)->first();
    $ppkketiga = Ppkketiga::where('id_formppk', $id)->first();

    // Dapatkan path dari signature
    $signaturePath = $ppk->signature ? public_path('admin/img/' . $ppk->signature) : null;
    $signaturePath2 = $ppkkedua && $ppkkedua->signaturepenerima ? public_path('admin/img/' . $ppkkedua->signaturepenerima): null;

    // Konversi signature ke base64 jika tersedia
    $signatureBase64 = null;
    if ($signaturePath && file_exists($signaturePath)) {
        $imageData = base64_encode(file_get_contents($signaturePath));
        $extension = pathinfo($signaturePath, PATHINFO_EXTENSION);
        $signatureBase64 = "data:image/{$extension};base64,{$imageData}";
    }
        $signaturePenerimaBase64 = null;
    if ($signaturePath2 && file_exists($signaturePath2)) {
        $imageData = base64_encode(file_get_contents($signaturePath2));
        $extension = pathinfo($signaturePath2, PATHINFO_EXTENSION);
        $signaturePenerimaBase64 = "data:image/{$extension};base64,{$imageData}";
    }


    // Data tambahan untuk view
    $data = [
        'ppk' => $ppk,
        'judul' => $ppk->judul,
        'nomor_surat' => $ppk->nomor_surat,
        'pembuat' => $ppk->pembuat,
        'emailpembuat' => $ppk->emailpembuat,
        'divisipembuat' => $ppk->divisipembuat,
        'penerima' => $ppk->penerima,
        'emailpenerima' => $ppk->emailpenerima,
        'divisipenerima' => $ppk->divisipenerima,
        'jenisketidaksesuaian' => $ppk->jenisketidaksesuaian,
        'evidence' => json_decode($ppk->evidence, true),
        'created_at' => $ppk->created_at,
        'signature' => $signatureBase64, // Berisi data base64 dari signature
        'signaturepenerima' => $signaturePenerimaBase64,
    ];

    // Check if Ppkkedua data exists and add pic1 and pic1_other logic
    if ($ppkkedua) {
        $data['identifikasi'] = $ppkkedua->identifikasi;
        $data['penanggulangan'] = $ppkkedua->penanggulangan;
        $data['pencegahan'] = $ppkkedua->pencegahan;
        $data['tgl_penanggulangan'] = $ppkkedua->tgl_penanggulangan;
        $data['tgl_pencegahan'] = $ppkkedua->tgl_pencegahan;

        // Check if pic1 is 'other' and pass pic1_other if available
        if ($ppkkedua->pic1 == 'other' && !empty($ppkkedua->pic1_other)) {
            $data['pic1'] = $ppkkedua->pic1; // Show pic1_other if pic1 is 'other'
        } else {
            $data['pic1'] = $ppkkedua->pic1User->nama_user ?? 'Tidak ada PIC'; // Default pic1 user name
        }

        $data['pic2'] = $ppkkedua->pic2User->nama_user ?? 'Tidak ada PIC';
        $data['signaturepenerima'] = $signaturePenerimaBase64;
        $data['created_at'] = $ppkkedua->updated_at;
    }
    if ($ppkketiga) {
        $data['verifikasi'] = $ppkketiga->verifikasi;
        $data['verifikasi_img'] = $ppkketiga->verifikasi_img;
        $data['tinjauan'] = $ppkketiga->tinjauan;
        $data['status'] = $ppkketiga->status;
        $data['created_at_ppkketiga'] = $ppkketiga->updated_at;
        $data['newppk'] = $ppkketiga->newppk;
    }

    return view('pdf.ppk', $data);
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
