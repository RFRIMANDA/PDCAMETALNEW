<?php

namespace App\Http\Controllers;

use App\Models\Ppk;
use App\Models\Ppkkedua;
use App\Models\Ppkketiga;
use App\Models\StatusPpk;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\PpkExport;
use App\Mail\KirimEmail;
use App\Mail\KirimEmail2;
use Illuminate\Support\Facades\Queue;
use App\Mail\KirimEmail3;
use Carbon\Carbon;
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
    // Periksa apakah ada filter input dari pengguna
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $semester = $request->input('semester');
    $user = $request->input('user'); // Ambil ID pengguna
    $keyword = $request->input('keyword');
    $status = $request->input('status');

    $statusPpkList = StatusPpk::all();

    // Query data PPK berdasarkan filter
    $ppks = Ppk::query()
        ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
        ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
        ->when($semester, fn($query) => $query->where('nomor_surat', 'like', "%$semester%"))
        ->when($user, function ($query) use ($user) {
            $query->where(function ($query) use ($user) {
                $query->where('pembuat', $user)
                      ->orWhere('penerima', $user); // Filter untuk sending & accepting
            });
        })
        ->when($keyword, fn($query) => $query->where('nomor_surat', 'like', "%$keyword%"))
        ->when($status, fn($query) => $query->where('statusppk', $status))
        ->get();

    $user = auth()->user();
    // Ambil daftar pengguna untuk dropdown
    $userList = User::orderBy('nama_user', 'asc')->pluck('nama_user', 'id');


    // Kirim data ke view
    return view('ppk.index2', compact('ppks', 'userList','statusPpkList', 'status', 'user'));
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
        $data = User::all()->sortBy('nama_user');
        $status = StatusPpk::all();
        return view('ppk.create', compact('data','status'));
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
            // dd($request->all());
            $request->validate([
                'judul' => 'nullable|string|max:1000',
                'statusppk' => 'nullable|exists:status,nama_statusppk', // Pastikan sesuai dengan nama tabel StatusPpk
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
                    'statusppk' => 'BELUM DIJAWAB',
                    'jenisketidaksesuaian' => is_array($request->jenisketidaksesuaian) ? implode(',', $request->jenisketidaksesuaian) : null,
                    'pembuat' => auth()->id(),
                    'emailpembuat' => $request->emailpembuat,
                    'divisipembuat' => $request->divisipembuat,
                    'penerima' => $request->penerima, // Pastikan ID User dikirim melalui request
                    'emailpenerima' => $request->emailpenerima,
                    'divisipenerima' => $request->divisipenerima,
                    'cc_email' => $ccEmails,
                    'evidence' => json_encode($evidences), // Menyimpan evidence sebagai JSON
                    'nomor_surat' => $nomorSurat,
                    'signature' => $signatureFileName,
                ]);

                // Membuat Data Terkait di Tabel Lain
                Ppkkedua::create(['id_formppk' => $buatppk->id]);
                Ppkketiga::create(['id_formppk' => $buatppk->id]);


                $penerimaUser = User::find($request->penerima); // Ambil user berdasarkan ID penerima
                if (!$penerimaUser) {
                    return response()->json(['error' => 'Penerima tidak ditemukan'], 404);
                }

                $pembuatUser = auth()->user(); // Gunakan pengguna yang sedang login
                if (!$pembuatUser) {
                    return response()->json(['error' => 'Pengguna tidak ditemukan'], 404);
                }
                // Kirim Email
                $data_email = [
                    'subject' => "Penerbitan No PPK {$nomorSurat}",
                    'sender_name' => "{$request->emailpembuat}, {$request->divisipembuat}",
                    'paragraf1' => "Dear {$penerimaUser->nama_user}, {$request->divisipenerima}", // Menggunakan nama_user dari model User
                    'paragraf2' => "Berikut Terlampir PPK",
                    'paragraf3' => $nomorSurat,
                    'paragraf4' => $request->judul,
                    'paragraf5' => "yang diajukan oleh {$pembuatUser->nama_user}",
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
            $request->validate([
                'judul' => 'required|string|max:255',
                'statusppk' => 'required|string',
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

            $ppk->evidence = json_encode(array_values($evidences));

            // Proses CC Emails jika disediakan
            $ccEmails = $request->input('cc_email', []);
            $ppk->cc_email = implode(',', $ccEmails);

            // Perbarui data lain
            $ppk->judul = $request->input('judul');
            $ppk->statusppk = $request->input('statusppk');
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
            // Simpan perubahan
            $ppk->save();

            DB::commit();

            return redirect()->route('ppk.index')->with('success', 'PPK updated successfully! ✅');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update data: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $ppk = Ppk::findOrFail($id);
        $status = StatusPpk::all();

        // Cek apakah evidence ada dan tidak kosong, kemudian ubah menjadi array
        $evidenceFiles = $ppk->evidence ? explode(',', $ppk->evidence) : [];

        $users = User::all()->sortBy('nama_user'); // Ganti 'name' dengan kolom yang ingin diurutkan

        return view('ppk.edit', compact('ppk', 'users', 'evidenceFiles', 'status'));
    }


    public function create2($id)
    {
        $data = User::all()->sortBy('nama_user');
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
        // Validasi input
        $request->validate([
            'identifikasi' => 'nullable|string|max:1000',
            'penanggulangan' => 'nullable|string|max:1000',
            'pencegahan' => 'nullable|string|max:1000',
            'tgl_penanggulangan' => 'nullable|date',
            'tgl_pencegahan' => 'nullable|date',
            'pic1' => 'nullable|array',
            'pic2' => 'nullable|array',
            'pic1_other' => 'nullable|string',
            'pic2_other' => 'nullable|string',
            'signaturepenerima' => 'nullable|string',
            'signaturepenerima_file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Menangani file tanda tangan
        $signatureFile = $this->handleSignature2($request, 'signaturepenerima', 'signaturepenerima_file');

        try {
            // Mengubah pic1 dan pic2 menjadi string
            $pic1 = is_array($request->pic1) ? implode(',', $request->pic1) : $request->pic1;
            $pic2 = is_array($request->pic2) ? implode(',', $request->pic2) : $request->pic2;

            // Menyimpan data yang diperbarui
            $updateData = [
                'identifikasi' => $request->identifikasi,
                'penanggulangan' => $request->penanggulangan,
                'pencegahan' => $request->pencegahan,
                'tgl_penanggulangan' => $request->tgl_penanggulangan,
                'tgl_pencegahan' => $request->tgl_pencegahan,
                'pic1' => $pic1,  // Menyimpan pic1 sebagai string
                'pic2' => $pic2,  // Menyimpan pic2 sebagai string
                'pic1_other' => $request->pic1_other,
                'pic2_other' => $request->pic2_other,
                'signaturepenerima' => $signatureFile,  // Menyimpan tanda tangan penerima
            ];

            // Update data di Ppkkedua
            Ppkkedua::where('id_formppk', $request->id_formppk)->update($updateData);

            // Cek apakah semua field terkait tidak null
            $ppkkedua = Ppkkedua::where('id_formppk', $request->id_formppk)->first();

            if ($ppkkedua && (
                $ppkkedua->penanggulangan ||
                $ppkkedua->pencegahan ||
                $ppkkedua->signaturepenerima ||
                $ppkkedua->signaturepenerima_file)) {
                // Update status Ppk terkait menjadi 'OPEN'
                Ppk::where('id', $request->id_formppk)->update(['statusppk' => 'OPEN']);
            }

            // Tambahkan sebelum pengiriman email
            if ($ppkkedua->identifikasi &&
            $ppkkedua->penanggulangan &&
            $ppkkedua->pencegahan &&
            $ppkkedua->tgl_penanggulangan &&
            $ppkkedua->tgl_pencegahan) {

            // Cek jika pencegahan, penanggulangan, dan tgl_pencegahan tidak null
            if ($ppkkedua->pencegahan && $ppkkedua->penanggulangan && $ppkkedua->tgl_pencegahan) {
                $formPpk = Ppk::find($request->id_formppk);

                if ($formPpk && ($formPpk->created_at->gt($ppkkedua->tgl_pencegahan) || $formPpk->updated_at->gt($ppkkedua->tgl_pencegahan))) {
                    $statusLewatTanggal = StatusPpk::where('nama_statusppk', 'OPEN (Lewat Tanggal)')->first();

                    if ($statusLewatTanggal) {
                        $formPpk->update(['statusppk' => 'OPEN (Lewat Tanggal)']);
                    }
                }

            }

            $data_email = [
                'subject' => 'VERIFIKASI',
                'sender_name' => "{$request->emailpembuat}, {$request->divisipembuat}",
                'isi' => "Dear PIC Departemen Inisiator, Mohon segera memverifikasi & Close PPK.",
            ];

            $penerima = Ppk::find($request->id_formppk)->emailpenerima;
            Mail::to($penerima)->send(new KirimEmail2($data_email));
            }

            return redirect()->route('ppk.index')->with('success', 'Data berhasil diperbarui.✅');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit2($id)
    {
        // Retrieve the PPK record by ID
        $ppk = Ppkkedua::findOrFail($id);
        // Get all users for PIC dropdowns
        // $data = User::all();
        $data = User::all()->sortBy('nama_user');
        $pic1 = $ppk->pic1; // Assuming this contains a comma-separated list of PIC ids
        $pic2 = $ppk->pic2;
    // dd($data;

        // Pass the PPK, users, and decoded values to the view
        return view('ppk.edit2', compact( 'data','ppk', ));
    }


    public function update2(Request $request, $id)
{
    // Validasi data
    $request->validate([
        'identifikasi' => 'nullable|string|max:1000',
        'penanggulangan' => 'nullable|string|max:1000',
        'pencegahan' => 'nullable|string|max:1000',
        'tgl_penanggulangan' => 'nullable|date',
        'tgl_pencegahan' => 'nullable|date',
        'pic1' => 'nullable|array',
        'pic2' => 'nullable|array',
        'pic1_other' => 'nullable|string',
        'pic2_other' => 'nullable|string',
    ]);

    try {
        $ppk = Ppkkedua::findOrFail($id);

        $pic1 = is_array($request->pic1) ? implode(',', $request->pic1) : $request->pic1;
        $pic2 = is_array($request->pic2) ? implode(',', $request->pic2) : $request->pic2;

        $updateData = [
            'identifikasi' => $request->identifikasi,
            'penanggulangan' => $request->penanggulangan,
            'pencegahan' => $request->pencegahan,
            'tgl_penanggulangan' => $request->tgl_penanggulangan,
            'tgl_pencegahan' => $request->tgl_pencegahan,
            'pic1' => $pic1,
            'pic2' => $pic2,
            'pic1_other' => $request->pic1_other,
            'pic2_other' => $request->pic2_other,
        ];

        $ppk->update($updateData);

        // Cek jika semua data terkait terisi dan update status jika melebihi tanggal
        if ($ppk->penanggulangan && $ppk->pencegahan && $ppk->tgl_pencegahan) {
            $formPpk = Ppk::find($ppk->id_formppk);

            if ($formPpk && ($formPpk->created_at->gt($ppk->tgl_pencegahan) || $formPpk->updated_at->gt($ppk->tgl_pencegahan))) {
                $statusLewatTanggal = StatusPpk::where('nama_statusppk', 'OPEN (Lewat Tanggal)')->first();

                if ($statusLewatTanggal) {
                    $formPpk->update(['statusppk' => 'OPEN (Lewat Tanggal)']);
                }
            }
        }

        // Kirim email jika semua data sudah terisi
        if ($ppk->identifikasi &&
            $ppk->penanggulangan &&
            $ppk->pencegahan &&
            $ppk->tgl_penanggulangan &&
            $ppk->tgl_pencegahan) {
            $data_email = [
                'subject' => "VERIFIKASI",
                'sender_name' => "",
                'isi' => "Dear PIC Departemen Inisiator, Mohon segera memverifikasi & Close PPK.",
            ];

            $pembuat = Ppk::find($ppk->id_formppk)->emailpembuat;

            if ($pembuat) {
                Mail::to($pembuat)->later(now()->addMinute(), new KirimEmail2($data_email));
            }
        }

        return redirect()->route('ppk.index')->with('success', 'Data berhasil diperbarui.✅');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
    }
}

public function kirimEmailVerifikasi(Request $request, $id)
{
    // Ambil data PPK berdasarkan ID yang diberikan
    $ppk = Ppk::find($id);

    // Pastikan data PPK ditemukan
    if ($ppk) {
        // Siapkan data untuk email
        $data_email = [
            'subject' => "VERIFIKASI",
            'sender_name' => "", // Bisa disesuaikan dengan nama pengirim
            'isi' => "Dear PIC Departemen Inisiator, Mohon segera memverifikasi & Close PPK.",
        ];

        // Ambil email penerima dari data PPK
        if ($ppk->emailpembuat) {
            // Kirim email langsung
            Mail::to($ppk->emailpembuat)->send(new KirimEmail2($data_email));

            // Jika berhasil, kembali ke halaman /adminppk
            return redirect()->route('ppk.index2')->with('success', 'Email berhasil dikirim! ✅');
        } else {
            // Jika email penerima tidak ditemukan
            return back()->with('error', 'Email penerima tidak ditemukan.');
        }
    } else {
        // Jika data PPK tidak ditemukan
        return back()->with('error', 'Data PPK tidak ditemukan.');
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
        // Validasi
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id',
            'verifikasi' => 'required|string|max:1000',
            'verifikasi_img.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk setiap file dalam array
            'tinjauan' => 'required|string|max:1000',
            'status' => 'required|in:TRUE,FALSE',
            'newppk' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction(); // Start a transaction
        try {
            // Persiapkan data untuk update
            $data = [
                'verifikasi' => $request->verifikasi,
                'tinjauan' => $request->tinjauan,
                'newppk' => $request->newppk,
                'status' => $request->status,
            ];

            // Array untuk menyimpan path gambar
            $verif_img = [];

            // Proses file yang diupload
            if ($request->hasFile('verifikasi_img')) {
                foreach ($request->file('verifikasi_img') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/verifikasi', $filename);
                    $verif_img[] = 'verifikasi/' . $filename;
                }
                $data['verifikasi_img'] = json_encode($verif_img);
            }

            // Update data pada tabel formppk3
            Ppkketiga::where('id_formppk', $request->id_formppk)->update($data);

            // Update statusppk di formppk berdasarkan id_formppk
            $formppk = Ppk::find($request->id_formppk);
            if ($formppk) {
                $formppk->statusppk = $request->status == 'TRUE' ? 'Closed' : 'Closed (Tidak Efektif)';
                $formppk->save();
            }

            // Update status pada formppk jika perlu
            $status_formppk = $request->status === 'TRUE' ? 'CLOSE' : 'CLOSE (Tidak Efektif)';
            Ppk::where('id', $request->id_formppk)->update(['statusppk' => $status_formppk]);

            // Update status pada model Formketiga
            $formketiga = Ppkketiga::find($request->id_formppk);
            if ($formketiga) {
                $formketiga->status = $request->status === 'TRUE' ? 'TRUE' : 'Closed (Tidak Efektif)';
                $formketiga->save();
            }

            DB::commit(); // Commit the transaction

            // // Kirim email notifikasi
            // $data_email = [
            //     'subject' => 'Pembaruan Verifikasi PPK',
            //     'sender_name' => auth()->user()->email,
            //     'isi' => "Dear PIC Departemen Inisiator, Mohon segera memverifikasi & Close PPK.",
            // ];

            // $penerima = Ppk::find($request->id_formppk)->emailpenerima;
            // Mail::to($penerima)->send(new KirimEmail2($data_email));

            return redirect()->route('ppk.index')->with('success', 'Form keempat berhasil disimpan.✅');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if an error occurs
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function update3(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id_formppk' => 'required|exists:formppk,id',
            'verifikasi' => 'nullable|string|max:1000',
            'tinjauan' => 'nullable|string|max:1000',
            'status' => 'required|in:TRUE,FALSE',
            'newppk' => 'nullable|string|max:255',
            'delete_verifikasi' => 'nullable|array',
        ]);

        try {
            // Cari data berdasarkan id_formppk
            $ppk = Ppkketiga::findOrFail($id);

            // Ambil evidence yang sudah ada
            $verif_imgs = $ppk->verifikasi_img ? json_decode($ppk->verifikasi_img, true) : [];

            // Hapus file evidence lama jika dipilih
            if ($request->filled('delete_verifikasi')) {
                foreach ($request->delete_verifikasi as $deleteVerifikasi) {
                    if (Storage::exists('public/' . $deleteVerifikasi)) {
                        Storage::delete('public/' . $deleteVerifikasi);
                    }
                    $verif_imgs = array_filter($verif_imgs, fn($verif_img) => $verif_img !== $deleteVerifikasi);
                }
            }

            // Simpan file evidence baru
            if ($request->hasFile('verifikasi_img')) {
                foreach ($request->file('verifikasi_img') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/verifikasi', $filename);
                    $verif_imgs[] = 'verifikasi/' . $filename;
                }
            }

            // Perbarui data evidence dan PPK
            $ppk->verifikasi_img = json_encode(array_values($verif_imgs));
            $ppk->update([
                'verifikasi' => $request->verifikasi,
                'tinjauan' => $request->tinjauan,
                'newppk' => $request->newppk,
                'status' => $request->status,
            ]);

            // Update statusppk di formppk berdasarkan id_formppk
            $formppk = Ppk::find($request->id_formppk);
            if ($formppk) {
                $formppk->statusppk = $request->status == 'TRUE' ? 'Closed' : 'Closed (Tidak Efektif)';
                $formppk->save();
            }

            // Update status pada formppk
            $status_formppk = $request->status === 'TRUE' ? 'CLOSE' : 'CLOSE (Tidak Efektif)';
            Ppk::where('id', $request->id_formppk)->update(['statusppk' => $status_formppk]);

            // Update status pada model Formketiga
            $formketiga = Ppkketiga::find($request->id_formppk);
            if ($formketiga) {
                $formketiga->status = $request->status === 'TRUE' ? 'TRUE' : 'Closed (Tidak Efektif)';
                $formketiga->save();
            }

            // // Kirim email notifikasi
            // $data_email = [
            //     'subject' => 'VERIFIKASI',
            //     'sender_name' => auth()->user()->email,
            //     'isi' => "Dear PIC Departemen Inisiator, Mohon segera memverifikasi & Close PPK.",
            // ];

            // $penerima = Ppk::find($request->id_formppk)->emailpenerima;
            // Mail::to($penerima)->send(new KirimEmail2($data_email));

            return redirect()->route('ppk.index')->with('success', 'Form keempat berhasil diperbarui.✅');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
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

        $pic1Ids = $ppkkedua->pic1 ? explode(',', $ppkkedua->pic1) : [];
        $pic2Ids = $ppkkedua->pic2 ? explode(',', $ppkkedua->pic2) : [];
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

        if ($ppkkedua) {
            $data['identifikasi'] = $ppkkedua->identifikasi;
            $data['penanggulangan'] = $ppkkedua->penanggulangan;
            $data['pencegahan'] = $ppkkedua->pencegahan;
            $data['tgl_penanggulangan'] = $ppkkedua->tgl_penanggulangan;
            $data['tgl_pencegahan'] = $ppkkedua->tgl_pencegahan;
            $data['pic1'] = $pic1Ids ? User::whereIn('id', $pic1Ids)->pluck('nama_user')->implode(', ') : ($ppkkedua->pic1_other ?? '-');
            $data['pic2'] = $pic2Ids ? User::whereIn('id', $pic2Ids)->pluck('nama_user')->implode(', ') : ($ppkkedua->pic2_other ?? '-');
            $data['pic1_other'] = $ppkkedua->pic1_other;
            $data['pic2_other'] = $ppkkedua->pic2_other;
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
            $data['pic1'] = $ppkkedua->pic1User->nama_user ?? '-';
            $data['pic2'] = $ppkkedua->pic2User->nama_user ?? '-';
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


    public function destroy(Request $request, $id)
    {
        // Periksa apakah pengguna memiliki peran 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Temukan data PPK berdasarkan ID
        $ppk = Ppk::findOrFail($id);

        // Hapus data PPK
        $ppk->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('ppk.index2')->with('success', 'Data PPK berhasil dihapus.✅');
    }
        public function email()
        {
            $pesan= "HMM";
            $pesan .= "pengacoan";
            $data_email=[
                'subject' => '',
                'sender_name' => '',
                'isi' => $pesan
            ];
            Mail::to("ps315475@gmail.com")->send(new kirimemail2($data_email));
            return '<h1>SUCCESS MENGIRIM EMAIL</h1>';
        }
}
