<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Resiko;
use App\Models\Tindakan;
use App\Models\Riskregister;
use App\Models\Realisasi;
use App\Exports\RiskRegisterExport;
use App\Exports\RiskOpportunityExport;
use App\Exports\RiskRegisterFilteredExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        $acces = Auth::user()->type;
        // dd($acces);

        $accesArray = json_decode($acces, true) ?? [];

        $divisi = Divisi::whereIn('id', $accesArray)->get();
        // $divisi = Divisi::whereIn('id',$acces)->get();

        foreach ($divisi as $d) {
            // Hitung total data
            $totalData = Riskregister::where('id_divisi', $d->id)->count();
            // Hitung jumlah data yang sudah berstatus 'close' atau 'Done'
            $doneCount = Riskregister::where('id_divisi', $d->id)
                ->whereHas('resikos', function ($query) {
                    $query->where('status', 'close'); // Ganti 'close' dengan 'Done' jika sesuai
                })
                ->count();

            // Simpan hasil hitungan ke dalam properti divisi
            $d->jumlah_data = $totalData - $doneCount; // Kurangi data 'Done' dari total
            $d->done_count = $doneCount; // Data yang berstatus 'Done'
        }

        return view('riskregister.index', compact('divisi'));
    }

    public function create($id)
    {
        $enchan = $id;
        $divisi = Divisi::all();
        return view('riskregister.create', compact('enchan', 'divisi', 'id'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'id_divisi' => 'required|exists:divisi,id',
                'issue' => 'required|string',
                'inex' => 'nullable|in:I,E',
                'nama_resiko' => 'nullable|required_without:peluang|string',
                'peluang' => 'nullable|required_without:nama_resiko|string',
                'kriteria' => 'nullable|in:Unsur keuangan / Kerugian,Safety & Health,Enviromental (lingkungan),Reputasi,Financial,Operational,Kinerja',
                'probability' => 'required|integer|min:1|max:5',
                'severity' => 'required|integer|min:1|max:5',
                'nama_tindakan' => 'required|array',
                'nama_tindakan.*' => 'required|string',
                'pihak' => 'nullable|string',
                // 'pihak.*' => 'required|string',
                'targetpic' => 'required|array',
                'targetpic.*' => 'required|string',
                'tgl_penyelesaian' => 'required|array',
                'tgl_penyelesaian.*' => 'required|date',
                'target_penyelesaian' => 'required|date',
                'status' => 'nullable|in:OPEN,ON PROGRES,CLOSE',
                'before' => 'nullable|string',
            ]);

            // Cek apakah kedua field 'risiko' dan 'peluang' diisi, dan jika iya, kembalikan error
            if ($request->filled('nama_resiko') && $request->filled('peluang')) {
                return back()->withErrors(['error' => 'Anda hanya bisa mengisi salah satu dari Risiko atau Peluang, tidak keduanya.']);
            }

            // Hitung tingkatan berdasarkan probability dan severity
            $tingkatan = $this->calculateTingkatan($validated['probability'], $validated['severity']);

            // Simpan data ke tabel riskregister
            $riskregister = Riskregister::create([
                'id_divisi' => $validated['id_divisi'],
                'issue' => $validated['issue'],
                'inex' => $validated['inex'],
                'pihak' => $validated['pihak'],
                'target_penyelesaian' => $validated['target_penyelesaian'],
                'peluang' => $validated['peluang'] ?? null, // Simpan peluang, jika ada
            ]);

            // Simpan data ke tabel resiko, jika ada risiko yang diisi
            $status = $validated['status'] ?? 'OPEN'; // Set status ke 'OPEN' jika tidak diberikan
            Resiko::create([
                'id_riskregister' => $riskregister->id,
                'nama_resiko' => $validated['nama_resiko'] ?? null, // Simpan risiko, jika ada
                'kriteria' => $validated['kriteria'],
                'probability' => $validated['probability'],
                'severity' => $validated['severity'],
                'tingkatan' => $tingkatan, // Simpan tingkatan dinamis
                'status' => $status,
                'before' => $validated['before'] ?? null
            ]);

            // Simpan data ke tabel tindakan dan realisasi
            foreach ($validated['nama_tindakan'] as $key => $nama_tindakan) {
                $tindakan = Tindakan::create([
                    'id_riskregister' => $riskregister->id,
                    'nama_tindakan' => $nama_tindakan,
                    'targetpic' => $validated['targetpic'][$key],
                    'tgl_penyelesaian' => $validated['tgl_penyelesaian'][$key],
                ]);

                 // Simpan data ke tabel realisasi dengan status otomatis ON PROGRES
                 Realisasi::create([
                    'id_riskregister' => $riskregister->id,
                    'id_tindakan' => $tindakan->id,
                    'nama_realisasi' => null, // Realisasi baru, jadi nama_realisasi belum diisi
                    'presentase' => 0, // Realisasi baru dimulai dari 0
                    'status' => 'ON PROGRES', // Status default ON PROGRES
                ]);
            }

            return redirect()->route('riskregister.tablerisk', ['id' => $riskregister->id_divisi])
                ->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Data gagal disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        // Ambil data riskregister berdasarkan ID
        $riskregister = Riskregister::findOrFail($id);
        // Ambil semua divisi untuk dropdown atau pilihan
        $divisi = Divisi::all();
        // Ambil semua tindakan yang terkait dengan riskregister
        $tindakanList = Tindakan::where('id_riskregister', $id)->get();

        // Return view untuk form edit
        return view('riskregister.edit', compact('riskregister', 'divisi', 'tindakanList'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        // Validasi input
        $validated = $request->validate([
            'id_divisi' => 'required|exists:divisi,id',
            'issue' => 'required|string',
            'inex' => 'nullable|in:I,E',
            'peluang' => 'nullable|string',
            'tindakan' => 'nullable|array',
            'tindakan.*' => 'nullable|string',
            'pihak' => 'nullable|string',
            'targetpic' => 'nullable|array',
            'targetpic.*' => 'nullable|string',
            'tgl_penyelesaian' => 'nullable|array',
            'tgl_penyelesaian.*' => 'nullable|date_format:Y-m-d',
            'target_penyelesaian' => 'required|date',
            'before' => 'nullable'
        ]);

        // Ambil riskregister berdasarkan ID
        $riskregister = Riskregister::findOrFail($id);

        // Update data riskregister
        $riskregister->update([
            'id_divisi' => $validated['id_divisi'],
            'issue' => $validated['issue'],
            'pihak' => $validated['pihak'],
            'inex' => $validated['inex'],
            'peluang' => $validated['peluang'],
            'target_penyelesaian' => $validated['target_penyelesaian'],
        ]);

        // Ambil tindakan yang ada
        $existingTindakan = Tindakan::where('id_riskregister', $riskregister->id)->get()->keyBy('id');

        // Flag untuk cek apakah semua realisasi selesai
        $isAllRealisasiComplete = true;

        // Update atau buat data tindakan baru
        foreach ($validated['tindakan'] as $key => $tindakan) {
            $tglPenyelesaian = isset($validated['tgl_penyelesaian'][$key]) ? $validated['tgl_penyelesaian'][$key] : null;

            if (!empty($tindakan) && !empty($validated['targetpic'][$key])) {
                if (isset($existingTindakan[$key])) {
                    // Update tindakan yang ada
                    $existingTindakan[$key]->update([
                        'nama_tindakan' => $tindakan,
                        'targetpic' => $validated['targetpic'][$key],
                        'tgl_penyelesaian' => $tglPenyelesaian
                    ]);
                } else {
                    // Buat tindakan baru jika tidak ada
                    $newTindakan = Tindakan::create([
                        'id_riskregister' => $riskregister->id,
                        'nama_tindakan' => $tindakan,
                        'targetpic' => $validated['targetpic'][$key],
                        'tgl_penyelesaian' => $tglPenyelesaian
                    ]);

                    // Simpan data ke tabel realisasi untuk tindakan baru
                    Realisasi::create([
                        'id_riskregister' => $riskregister->id,
                        'id_tindakan' => $newTindakan->id,
                        'nama_realisasi' => null, // Realisasi baru, jadi nama_realisasi belum diisi
                        'presentase' => 0, // Realisasi baru dimulai dari 0
                        'status' => 'ON PROGRES', // Status default ON PROGRES
                    ]);
                }
            }
        }

        // Cek semua tindakan untuk menentukan status realisasi
        $realisasiRecords = Realisasi::where('id_riskregister', $riskregister->id)->get();
        foreach ($realisasiRecords as $realisasi) {
            if ($realisasi->status === 'CLOSE') {
                $isAllRealisasiComplete = true; // Tetap CLOSE jika ada yang sudah CLOSE
            } else {
                $isAllRealisasiComplete = false; // Ada yang ON PROGRES atau tidak selesai
            }
        }

        // Ambil resiko terkait riskregister
        $resiko = Resiko::where('id_riskregister', $riskregister->id)->first();

        // Update status resiko berdasarkan realisasi
        if ($isAllRealisasiComplete) {
            // Jika semua realisasi selesai, set status menjadi CLOSE
            $resiko->status = 'CLOSE';
        } else {
            // Jika ada realisasi yang belum selesai, set status menjadi ON PROGRES
            $resiko->status = 'ON PROGRES';
        }
        $resiko->save();

        return redirect()->route('riskregister.tablerisk', ['id' => $validated['id_divisi']])
            ->with('success', 'Data berhasil diperbarui!');
    }


    private function calculateTingkatan($probability, $severity)
    {
        $scorerisk = $probability * $severity;

        if ($scorerisk >= 1 && $scorerisk <= 2) {
            return 'LOW'; // HIJAU
        } elseif ($scorerisk >= 3 && $scorerisk <= 4) {
            return 'MEDIUM'; // BIRU
        } elseif ($scorerisk >= 5 && $scorerisk <= 25) {
            return 'HIGH'; // KUNING
        }

        return 'UNKNOWN'; // Untuk menangani kasus tidak terduga
    }

    public function tablerisk(Request $request, $id)
{
    $search = $request->input('search'); // Get the search input from the request

    // Ambil semua riskregister terkait dengan divisi, dengan filtering jika ada query search
    $query = Riskregister::where('id_divisi', $id);

    if ($search) {
        $query->where('issue', 'like', '%' . $search . '%'); // Filter by issue
    }

    // Get filtered riskregister records
    $forms = $query->get(); // Use the query to get the results
    $data = [];

    foreach ($forms as $form) {
        // Ambil semua tindakan terkait dengan setiap riskregister
        $tindakanList = Tindakan::where('id_riskregister', $form->id)->get();

        // Perhitungan nilai_actual untuk setiap form
        $totalNilaiAkhir = Realisasi::where('id_riskregister', $form->id)->sum('nilai_akhir');
        $jumlahEntry = Realisasi::where('id_riskregister', $form->id)->count();
        $form->nilai_actual = $jumlahEntry > 0 ? round($totalNilaiAkhir / $jumlahEntry, 2) : 0;

        // Memeriksa apakah ada tindakan yang sudah berstatus CLOSE
        $tindakanList = $tindakanList->map(function ($tindakan) {
            $tindakan->isClosed = Realisasi::where('id_tindakan', $tindakan->id)
                                ->where('status', 'CLOSE')
                                ->exists();

            // Jika ada tanggal yang ingin diformat
            if ($tindakan->tgl_penyelesaian) {
                $tindakan->tgl_penyelesaian = Carbon::parse($tindakan->tgl_penyelesaian)->format('d-m-Y');
            } else {
                $tindakan->tgl_penyelesaian = '-';
            }

            return $tindakan;
        });

        // Format target_penyelesaian
        if ($form->target_penyelesaian) {
            $form->target_penyelesaian = Carbon::parse($form->target_penyelesaian)->format('d-m-Y');
        } else {
            $form->target_penyelesaian = '-';
        }

        $data[$form->id] = $tindakanList;
    }

    // Tampilkan view dengan data riskregister, tindakan terkait, serta status
    return view('riskregister.tablerisk', compact('forms', 'data', 'id'));
}

public function biglist(Request $request)
{
    // Ambil parameter filter dari request
    $tingkatanFilter = $request->input('tingkatan');
    $statusFilter = $request->input('status');
    $divisiFilter = $request->input('nama_divisi');
    $yearFilter = $request->input('year');
    $keywordFilter = $request->input('keyword');
    $kategoriFilter = $request->input('kriteria');
    $top10Filter = $request->input('top10'); // Tambahkan filter top 10

    $user = Auth::user();
    $allowedDivisi = json_decode($user->type, true);

    $query = Riskregister::with(['tindakan.realisasi', 'resikos', 'divisi']);

    if ($tingkatanFilter) {
        $query->whereHas('resikos', function ($q) use ($tingkatanFilter) {
            $q->where('tingkatan', $tingkatanFilter);
        });
    }

    if ($statusFilter == 'open_on_progres') {
        $query->whereHas('resikos', function ($q) {
            $q->whereIn('status', ['OPEN', 'ON PROGRES']);
        });
    } elseif ($statusFilter) {
        $query->whereHas('resikos', function ($q) use ($statusFilter) {
            $q->where('status', $statusFilter);
        });
    }

    if ($user->role == 'user' && !empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

    if ($divisiFilter) {
        $query->whereHas('divisi', function ($q) use ($divisiFilter) {
            $q->where('nama_divisi', $divisiFilter);
        });
    }

    if ($yearFilter) {
        $query->whereHas('tindakan.realisasi', function ($q) use ($yearFilter) {
            $q->whereYear('tgl_penyelesaian', $yearFilter);
        });
    }

    // Mengubah filter keyword untuk mencakup nama_tindakan, resiko, dan peluang
    if ($keywordFilter) {
        $query->where(function ($q) use ($keywordFilter) {
            $q->where('issue', 'like', '%' . $keywordFilter . '%')
              ->orWhereHas('tindakan', function ($q) use ($keywordFilter) {
                  $q->where('nama_tindakan', 'like', '%' . $keywordFilter . '%');
              })
              ->orWhereHas('resikos', function ($q) use ($keywordFilter) {
                  $q->where('nama_resiko', 'like', '%' . $keywordFilter . '%');
              })
              ->orWhere('peluang', 'like', '%' . $keywordFilter . '%');
        });
    }
    if ($kategoriFilter) {
        $query->whereHas('resikos', function ($q) use ($kategoriFilter) {
            $q->where('kriteria', $kategoriFilter);
        });
    }

    // Ambil data yang sudah difilter
    $data = $query->get();

    $formattedData = [];

    foreach ($data as $riskregister) {

        // Hitung nilai_actual
        $totalNilaiAkhir = Realisasi::where('id_riskregister', $riskregister->id)->sum('nilai_akhir');
        $jumlahEntry = Realisasi::where('id_riskregister', $riskregister->id)->count();
        $nilai_actual = $jumlahEntry > 0 ? round($totalNilaiAkhir / $jumlahEntry, 2) : 0;

        $resikoData = $riskregister->resikos->map(function ($resiko) {
            return [
                'nama_resiko' => $resiko->nama_resiko,
                'probability' => $resiko->probability,
                'severity' => $resiko->severity,
                'score' => $resiko->probability * $resiko->severity
            ];
        });

        $highestScore = $resikoData->pluck('score')->max();

        $formattedData[] = [
            'id' => $riskregister->id,
            'issue' => $riskregister->issue,
            'pihak' => $riskregister->tindakan->pluck('divisi.nama_divisi'),
            'tindak_lanjut' => $riskregister->tindakan->pluck('nama_tindakan'),
            'risiko' => $resikoData->pluck('nama_resiko'),
            'peluang' => $riskregister->peluang,
            'tingkatan' => $riskregister->resikos->pluck('tingkatan'),
            'status' => $riskregister->resikos->pluck('status'),
            'scores' => $resikoData->pluck('score'),
            'highest_score' => $highestScore,
            'nilai_actual' => $nilai_actual,
            'persentase_nilai_actual' => $jumlahEntry > 0 ? round(($nilai_actual / 100) * 100, 2) : 0 // Ganti 100 dengan nilai maksimum yang relevan
        ];
    }

    // Urutkan data berdasarkan skor tertinggi
    $formattedData = collect($formattedData)->sortByDesc('highest_score')->values();

    // Filter untuk 10 skor tertinggi jika checkbox diaktifkan
    if ($top10Filter) {
        $formattedData = $formattedData->take(10);
    }

    // Ambil daftar divisi untuk filter di tampilan
    $divisiList = Divisi::all();

    // Tampilkan ke view dengan data yang sudah difilter
    return view('riskregister.biglist', compact('formattedData', 'divisiList'));
}

    public function exportFilteredExcel(Request $request, $id)
{
    // Ambil parameter filter dari request
    $tingkatanFilter = $request->input('tingkatan');
    $statusFilter = $request->input('status');
    $divisiFilter = $request->input('nama_divisi');
    $yearFilter = $request->input('year');

    // Ambil user yang sedang login dan allowed divisi
    $user = Auth::user();
    $allowedDivisi = json_decode($user->type, true);

    // Ambil data riskregister yang sudah difilter
    $query = Riskregister::with(['tindakan.realisasi', 'resikos']);

    // Terapkan filter tingkatan
    if ($tingkatanFilter) {
        $query->whereHas('resikos', function ($q) use ($tingkatanFilter) {
            $q->where('tingkatan', $tingkatanFilter);
        });
    }

    // Filter status: Tangani filter untuk 'OPEN & ON PROGRES'
    if ($statusFilter == 'open_on_progres') {
        $query->whereHas('resikos', function ($q) {
            $q->whereIn('status', ['OPEN', 'ON PROGRES']);
        });
    } elseif ($statusFilter) {
        $query->whereHas('resikos', function ($q) use ($statusFilter) {
            $q->where('status', $statusFilter);
        });
    }

    // Filter berdasarkan divisi sesuai dengan hak akses user
    if ($user->role == 'user' && !empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

    // Filter tambahan berdasarkan divisi jika ada
    if ($divisiFilter) {
        $query->whereHas('divisi', function ($q) use ($divisiFilter) {
            $q->where('nama_divisi', $divisiFilter);
        });
    }

    // Filter tahun penyelesaian langsung dari Riskregister
    if ($yearFilter) {
        $query->whereYear('target_penyelesaian', $yearFilter);
    }

    // Ambil data yang sudah difilter
    $riskregisters = $query->get();

    // Siapkan data untuk ekspor
    $formattedData = [];
    foreach ($riskregisters as $riskregister) {
        foreach ($riskregister->resikos as $resiko) {
            $tindakanData = [];
            foreach ($riskregister->tindakan as $tindakan) {
                // Dapatkan tgl_realisasi terakhir dari relasi realisasi berdasarkan id_tindakan
                $tglRealisasiTerakhir = $tindakan->realisasi()->orderBy('tgl_realisasi', 'desc')->value('tgl_realisasi');

                $tindakanData[] = [
                    'nama_tindakan' => $tindakan->nama_tindakan,
                    'targetpic' => $tindakan->targetpic,
                    'tgl_realisasi' => $tglRealisasiTerakhir,
                ];
            }

            // Urutkan tindakan berdasarkan nama pihak (string input)
            usort($tindakanData, function ($a, $b) {
                return strcmp($a['nama_tindakan'], $b['nama_tindakan']);
            });

            $formattedData[] = [
                'issue' => $riskregister->issue,
                'inex' => $riskregister->inex, // Corrected to store 'inex' instead of duplicate 'issue'
                'pihak' => $riskregister->pihak, // Pihak now sourced from Riskregister
                'risiko' => $resiko->nama_resiko,
                'peluang' => $riskregister->peluang,
                'tingkatan' => $resiko->tingkatan,
                'tindak_lanjut' => array_column($tindakanData, 'nama_tindakan'),
                'targetpic' => array_column($tindakanData, 'targetpic'),
                'tgl_realisasi' => array_column($tindakanData, 'tgl_realisasi'),
                'status' => $resiko->status,
                'scores' => $resiko->risk,
                'before' => $resiko->before,
                'after' => $resiko->after,
            ];
        }
    }

    // Ekspor ke Excel
    if ($request->has('export') && $request->input('export') == 'excel') {
        return Excel::download(new RiskOpportunityExport($formattedData), 'risk_opportunity_export.xlsx');
    }
}


    public function destroy($id)
    {
        // Temukan RiskRegister berdasarkan ID
        $riskregister = Riskregister::findOrFail($id);

        // Hapus data terkait dari tabel resiko, cek apakah ada resiko yang terkait
        if ($riskregister->resikos()->exists()) {
            foreach ($riskregister->resikos as $resiko) {
                $resiko->delete();
            }
        }

        // Hapus data terkait dari tabel tindakan dan realisasi, cek apakah ada tindakan yang terkait
        if ($riskregister->tindakans()->exists()) {
            foreach ($riskregister->tindakans as $tindakan) {
                // Hapus realisasi terkait tindakan ini
                if ($tindakan->realisasis()->exists()) {
                    foreach ($tindakan->realisasis as $realisasi) {
                        $realisasi->delete();
                    }
                }

                // Hapus tindakan
                $tindakan->delete();
            }
        }

        // Hapus RiskRegister
        $riskregister->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('riskregister.biglist')->with('success', 'Data berhasil dihapus!. ✅');
    }

    public function exportFilteredPDF(Request $request, $id)
{
    // Ambil parameter filter dari request
    $tingkatanFilter = $request->query('tingkatan'); // Tangkap dari query string
    $statusFilter = $request->query('status');
    $divisiFilter = $request->query('nama_divisi');
    $yearFilter = $request->query('year');
    $kategoriFilter = $request->input('kriteria'); // Assuming this is how you want to filter by kriteria

    // Ambil user yang sedang login dan allowed divisi
    $user = Auth::user();
    $allowedDivisi = json_decode($user->type, true);

    // Ambil data riskregister yang sudah difilter
    $query = Riskregister::with(['tindakan.realisasi', 'resikos']);

    // Terapkan filter tingkatan
    if ($tingkatanFilter) {
        $query->whereHas('resikos', function ($q) use ($tingkatanFilter) {
            $q->where('tingkatan', $tingkatanFilter);
        });
    }

    // Filter status: Tangani filter untuk 'OPEN & ON PROGRES'
    if ($statusFilter == 'open_on_progres') {
        $query->whereHas('resikos', function ($q) {
            $q->whereIn('status', ['OPEN', 'ON PROGRES']);
        });
    } elseif ($statusFilter) {
        $query->whereHas('resikos', function ($q) use ($statusFilter) {
            $q->where('status', $statusFilter);
        });
    }

    // Filter berdasarkan divisi sesuai dengan hak akses user
    if ($user->role == 'user' && !empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

    // Filter tambahan berdasarkan divisi jika ada
    if ($divisiFilter) {
        $query->whereHas('divisi', function ($q) use ($divisiFilter) {
            $q->where('nama_divisi', $divisiFilter);
        });
    }

    // Filter tahun penyelesaian langsung dari Riskregister
    if ($yearFilter) {
        $query->whereYear('target_penyelesaian', $yearFilter);
    }

    // Filter berdasarkan kategori (kriteria)
    if ($kategoriFilter) {
        $query->whereHas('resikos', function ($q) use ($kategoriFilter) {
            $q->where('kategori', $kategoriFilter); // Ensure the column name matches your database schema
        });
    }

        // Ambil data yang sudah difilter
        $riskregisters = $query->get();

        // Siapkan data untuk ditampilkan pada PDF
        $formattedData = [];
        foreach ($riskregisters as $riskregister) {
            foreach ($riskregister->resikos as $resiko) {
                $tindakanData = [];
                foreach ($riskregister->tindakan as $tindakan) {
                    // Dapatkan tgl_realisasi terakhir dari relasi realisasi berdasarkan id_tindakan
                    $tglRealisasiTerakhir = $tindakan->realisasi()->orderBy('tgl_realisasi', 'desc')->value('tgl_realisasi');

                    $tindakanData[] = [
                        'pihak' => $tindakan->pihak,
                        'nama_tindakan' => $tindakan->nama_tindakan,
                        'targetpic' => $tindakan->targetpic,
                        'tgl_realisasi' => $tglRealisasiTerakhir,
                    ];
                }

                // Urutkan tindakan berdasarkan nama pihak
                usort($tindakanData, function ($a, $b) {
                    return strcmp($a['pihak'], $b['pihak']);
                });

                $formattedData[] = [
                    'issue' => $riskregister->issue,
                    'inex' => $riskregister->inex,
                    'pihak' => $riskregister->pihak,
                    'risiko' => $resiko->nama_resiko,
                    'peluang' => $riskregister->peluang,
                    'tingkatan' => $resiko->tingkatan,
                    'tindak_lanjut' => array_column($tindakanData, 'nama_tindakan'),
                    'targetpic' => array_column($tindakanData, 'targetpic'),
                    'tgl_realisasi' => array_column($tindakanData, 'tgl_realisasi'),
                    'status' => $resiko->status,
                    'risk' => $resiko->risk,
                    'before' => $resiko->before,
                    'after' => $resiko->after,
                ];
            }
        }

        // Render data ke view
        $pdf = PDF::loadView('pdf.risk_opportunity_export', compact('formattedData'));

        // Set orientasi PDF menjadi landscape
        $pdf->setPaper('A4', 'landscape');

        // Download file PDF
        return $pdf->download('risk_opportunity_export.pdf');
    }
}



