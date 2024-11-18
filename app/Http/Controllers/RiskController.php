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
use App\Models\Kriteria;
use App\Models\User;
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
    $kriteria = Kriteria::all();

    // Ambil nama divisi berdasarkan id yang diberikan
    $divisiData = Divisi::findOrFail($id);
    $nama_divisi = $divisiData->nama_divisi;

    // Filter users berdasarkan nama divisi yang sesuai
    $users = User::where('divisi', $nama_divisi)->get();

    return view('riskregister.create', compact('enchan', 'divisi', 'id', 'kriteria', 'users'));
}

    public function store(Request $request)
{
    try {
        // Validasi input
        // dd($request->all());
        $validated = $request->validate([
            'id_divisi' => 'required|exists:divisi,id',
            'issue' => 'required|string',
            'inex' => 'nullable|in:I,E',
            'nama_resiko' => 'nullable|required_without:peluang|string',
            'peluang' => 'nullable|required_without:nama_resiko|string',
            'kriteria' => 'nullable|in:Unsur Keuangan / Kerugian,Safety & Health,Enviromental (lingkungan),Reputasi,Financial,Operational,Kinerja',
            'probability' => 'required|integer|min:1|max:5',
            'severity' => 'required|integer|min:1|max:5',
            'nama_tindakan' => 'required|array',
            'nama_tindakan.*' => 'required|string',
            'pihak' => 'nullable|array', // Pastikan pihak berupa array jika memilih beberapa divisi
            'pihak.*' => 'string', // Validasi pihak sebagai string (nama divisi)
            'targetpic' => 'required|array',
            'targetpic.*' => 'required|string',
            'tgl_penyelesaian' => 'required|array',
            'tgl_penyelesaian.*' => 'required|date',
            'target_penyelesaian' => 'required|date',
            'status' => 'nullable|in:OPEN,ON PROGRES,CLOSE',
            'before' => 'nullable|string',
            'pihak_other' => 'nullable|string' // Validasi untuk pihak lainnya jika ada input
        ]);

        // Cek apakah kedua field 'risiko' dan 'peluang' diisi, dan jika iya, kembalikan error
        if ($request->filled('nama_resiko') && $request->filled('peluang')) {
            return back()->withErrors(['error' => 'Anda hanya bisa mengisi salah satu dari Risiko atau Peluang, tidak keduanya.']);
        }

        // Hitung tingkatan berdasarkan probability dan severity
        $tingkatan = $this->calculateTingkatan($validated['probability'], $validated['severity']);

        // Tambahkan pihak_other ke array pihak jika ada
        if ($request->has('pihak_other') && $request->filled('pihak_other')) {
            $validated['pihak'][] = $validated['pihak_other']; // Tambahkan ke array pihak
        }

        // Simpan data ke tabel riskregister
        $riskregister = Riskregister::create([
            'id_divisi' => $validated['id_divisi'],
            'issue' => $validated['issue'],
            'inex' => $validated['inex'],
            'pihak' => $validated['pihak'] ? implode(',', $validated['pihak']) : null, // Simpan nama divisi
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
            ->with('success', 'Data berhasil disimpan!.✅');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Data gagal disimpan: ' . $e->getMessage()]);
    }
}

public function edit($id)
{
    // Ambil data Riskregister berdasarkan ID
    $riskregister = Riskregister::findOrFail($id);

    // Ambil semua data divisi
    $divisi = Divisi::all();

    // Ambil tindakan yang terkait dengan Riskregister
    $tindakanList = Tindakan::where('id_riskregister', $id)->get();
    $resikoList = Resiko::where('id_riskregister',$id)->get();

    // Mendapatkan divisi yang dipilih untuk kolom pihak (dipecah dengan koma)
    $selectedDivisi = $riskregister->pihak ? explode(',', $riskregister->pihak) : [];

    // Ambil target PIC berdasarkan targetpicId
    $targetpicId = $riskregister->targetpic;
    $users = User::all(); // Ambil semua pengguna untuk dropdown select

    // Kembalikan tampilan edit dengan data yang diperlukan
    return view('riskregister.edit', compact('riskregister', 'divisi', 'tindakanList','resikoList', 'selectedDivisi', 'users'));
}

public function update(Request $request, $id)
{
    // Update validation to accept 'nama_resiko', 'before', and 'after' as arrays
    // dd($request->all());
    $validated = $request->validate([
        'id_divisi' => 'required|exists:divisi,id',
        'issue' => 'required|string',
        'inex' => 'nullable|in:I,E',
        'peluang' => 'nullable|string',
        'tindakan' => 'nullable|array',
        'tindakan.*' => 'nullable|string',
        'pihak' => 'nullable|array',
        'pihak.*' => 'exists:divisi,id',
        'targetpic' => 'nullable|array',
        'targetpic.*' => 'nullable|string',
        'tgl_penyelesaian' => 'nullable|array',
        'tgl_penyelesaian.*' => 'nullable|date_format:Y-m-d',
        'nama_resiko' => 'nullable|array',
        'before' => 'nullable|array',
        'after' => 'nullable|array',
        'target_penyelesaian' => 'required|date',
        'pihak_other' => 'nullable|string',
    ]);


    // Process pihak and pihak_other
    $pihak = $validated['pihak'] ?? [];
    if (!empty($validated['pihak_other'])) {
        $pihak[] = $validated['pihak_other'];
    }

    // Find the Riskregister by ID
    $riskregister = Riskregister::findOrFail($id);

    // Update riskregister
    $riskregister->update([
        'id_divisi' => $validated['id_divisi'],
        'issue' => $validated['issue'],
        'inex' => $validated['inex'],
        'peluang' => $validated['peluang'],
        'target_penyelesaian' => $validated['target_penyelesaian'],
        'pihak' => $pihak ? implode(',', Divisi::whereIn('id', $pihak)->pluck('nama_divisi')->toArray()) : null,
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

    // Get the first value from 'nama_resiko', 'before', and 'after' arrays
    $nama_resiko = !empty($validated['nama_resiko']) ? array_shift($validated['nama_resiko']) : null;
    $before = !empty($validated['before']) ? array_shift($validated['before']) : null;
    $after = !empty($validated['after']) ? array_shift($validated['after']) : null;

    // Update or create Resiko record
    $resiko = Resiko::firstOrNew(['id_riskregister' => $riskregister->id]);
    $resiko->fill([
        'nama_resiko' => $nama_resiko,
        'before' => $before,
        'after' => $after,
    ])->save();

    return redirect()->route('riskregister.tablerisk', ['id' => $validated['id_divisi']])
        ->with('success', 'Data berhasil diperbarui!.✅');
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
    $targetPicSearch = $request->input('targetpic');
    $tingkatanFilter = $request->input('tingkatan');
    $statusFilter = $request->input('status');
    $yearFilter = $request->input('year');
    $kategoriFilter = $request->input('kriteria');
    $top10Filter = $request->input('top10');
    $keywordFilter = $request->input('keyword');

    $user = Auth::user();
    $allowedDivisi = json_decode($user->type, true);

    // Start the query
    $query = Riskregister::where('id_divisi', $id);

    // Filter by allowed divisi
    $query->when($allowedDivisi, function ($query) use ($allowedDivisi) {
        return $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    });

    // Filter by tingkatan
    $query->when($tingkatanFilter, function ($query) use ($tingkatanFilter) {
        return $query->whereHas('resikos', function ($q) use ($tingkatanFilter) {
            $q->where('tingkatan', $tingkatanFilter);
        });
    });

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

    // Filter by status
    $query->when($statusFilter, function ($query) use ($statusFilter) {
        if ($statusFilter === 'open_on_progres') {
            return $query->whereHas('resikos', function ($q) {
                $q->whereIn('status', ['OPEN', 'ON PROGRES']);
            });
        }
        return $query->whereHas('resikos', function ($q) use ($statusFilter) {
            $q->where('status', $statusFilter);
        });
    });

    // Filter by kategori (kriteria)
    if ($kategoriFilter) {
        $query->whereHas('resikos', function ($q) use ($kategoriFilter) {
            $q->where('kriteria', $kategoriFilter);
        });
    }

    // Filter by year
    $query->when($yearFilter, function ($query) use ($yearFilter) {
        return $query->whereHas('tindakan.realisasi', function ($q) use ($yearFilter) {
            $q->whereYear('tgl_penyelesaian', $yearFilter);
        });
    });

    // Get filtered riskregister records
    $forms = $query->get();
    $data = [];

    // Get tindakan list filtered by targetpic search
    $tindakanList = Tindakan::whereIn('id_riskregister', $forms->pluck('id'));

    // Apply filter for targetpic if provided
    if ($targetPicSearch) {
        $tindakanList->whereHas('user', function ($query) use ($targetPicSearch) {
            $query->where('nama_user', 'like', '%' . $targetPicSearch . '%');
        });
    }

    // Get and format the tindakan data
    $tindakanList = $tindakanList->get()->groupBy('id_riskregister');

    // Process forms and add filtered tindakan
    foreach ($forms as $form) {
        $tindakanFiltered = $tindakanList->get($form->id, collect())->map(function ($tindakan) {
            $tindakan->isClosed = Realisasi::where('id_tindakan', $tindakan->id)
                                            ->where('status', 'CLOSE')
                                            ->exists();

            $tindakan->tgl_penyelesaian = $tindakan->tgl_penyelesaian
                ? Carbon::parse($tindakan->tgl_penyelesaian)->format('d-m-Y')
                : '-';

            $tindakan->targetpic = $tindakan->targetpic ?? '-';

            return $tindakan;
        });

        $data[$form->id] = $tindakanFiltered;

        // Calculate actual value
        $totalNilaiAkhir = Realisasi::where('id_riskregister', $form->id)->sum('nilai_akhir');
        $jumlahEntry = Realisasi::where('id_riskregister', $form->id)->count();
        $form->nilai_actual = $jumlahEntry > 0 ? round($totalNilaiAkhir / $jumlahEntry, 2) : 0;

        // Format target_penyelesaian
        $form->target_penyelesaian = $form->target_penyelesaian
            ? Carbon::parse($form->target_penyelesaian)->format('d-m-Y')
            : '-';
    }

    // Sort by highest_score if required
    if ($top10Filter) {
        $forms = $forms->sortByDesc('highest_score')->take(10);
    } else {
        $forms = $forms->sortByDesc('highest_score');
    }

    // Get divisi and users for dropdown
    $divisiData = Divisi::findOrFail($id);
    $nama_divisi = $divisiData->nama_divisi;
    $users = User::where('divisi', $nama_divisi)->get();

    $divisiList = $nama_divisi;

    return view('riskregister.tablerisk', compact('forms', 'data', 'id', 'users', 'divisiList'));
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
    $allowedDivisi = json_decode($user->type, true); // Ambil tipe dari user

    $query = Riskregister::with(['tindakan.realisasi', 'resikos', 'divisi']);

    // Filter hanya data yang terkait dengan tipe user
    if (!empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

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

    // Calculate nilai_actual
    $totalNilaiAkhir = Realisasi::where('id_riskregister', $riskregister->id)->sum('nilai_akhir');
    $jumlahEntry = Realisasi::where('id_riskregister', $riskregister->id)->count();
    $nilai_actual = $jumlahEntry > 0 ? round($totalNilaiAkhir / $jumlahEntry, 2) : 0;

    $resikoData = $riskregister->resikos->map(function ($resiko) {
        return [
            'nama_resiko' => $resiko->nama_resiko,
            'probability' => $resiko->probability,
            'severity' => $resiko->severity,
            'risk' => $resiko->risk,
            'before' => $resiko->before,
            'after' => $resiko->after,
            'score' => $resiko->probability * $resiko->severity
        ];
    });

    $highestScore = $resikoData->pluck('score')->max();

    $formattedData[] = [
        'id' => $riskregister->id,
        'issue' => $riskregister->issue,
        'inex' => $riskregister->inex,
        'pihak' => $riskregister->pihak,
        'tindak' => $riskregister->tindakan->pluck('divisi.nama_divisi'),
        'tindak_lanjut' => $riskregister->tindakan->pluck('nama_tindakan'),
        'risiko' => $resikoData->pluck('nama_resiko'),
        'peluang' => $riskregister->peluang,
        'tingkatan' => $riskregister->resikos->pluck('tingkatan'),
        'status' => $riskregister->resikos->pluck('status'),
        'scores' => $resikoData->pluck('score'),
        'risk' => $resikoData->pluck('risk'),
        'before' => $resikoData->pluck('before'),
        'after' => $resikoData->pluck('after'),
        'probabilities' => $resikoData->pluck('probability'),  // Added probabilities
        'severities' => $resikoData->pluck('severity'),        // Added severities
        'highest_score' => $highestScore,
        'nilai_actual' => $nilai_actual,
        'persentase_nilai_actual' => $jumlahEntry > 0 ? round(($nilai_actual / 100) * 100, 2) : 0 // Adjust 100 if needed
    ];
}

// Sort the data by highest score
$formattedData = collect($formattedData)->sortByDesc('highest_score')->values();

// Filter to top 10 if the checkbox is enabled
if ($top10Filter) {
    $formattedData = $formattedData->take(10);
}

// Get list of divisions for filtering in the view
$divisiList = Divisi::all();
$defaultDivisiId = $divisiList->first()->id ?? null;

$divisi = Riskregister::all();




// Pass data to the view
return view('riskregister.biglist', compact('formattedData', 'divisiList','defaultDivisiId','divisi'));}

public function exportFilteredPDF(Request $request)
{
    // Retrieve filter parameters from the request
    $tingkatanFilter = $request->query('tingkatan');
    $statusFilter = $request->query('status');
    $divisiFilter = $request->query('nama_divisi');
    $yearFilter = $request->query('year');
    $kategoriFilter = $request->query('kriteria');
    $keywordFilter = $request->input('keyword');
    $top10Filter = $request->input('top10');

    // Get the logged-in user and their allowed divisions
    $user = Auth::user();
    $allowedDivisi = json_decode($user->type, true);

    // Initialize the query
    $query = Riskregister::with(['tindakan.realisasi', 'resikos']);

    // Apply division filter based on user type
    if (!empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

    // Apply additional filters
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

    if ($divisiFilter) {
        $query->whereHas('divisi', function ($q) use ($divisiFilter) {
            $q->where('nama_divisi', $divisiFilter);
        });
    }

    if ($yearFilter) {
        $query->whereYear('target_penyelesaian', $yearFilter);
    }

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

    if ($top10Filter) {
        $query->take(10);
    }

    // Retrieve the filtered data
    $riskregisters = $query->get();

    // Format the data for PDF export
    $formattedData = [];
    foreach ($riskregisters as $riskregister) {
        foreach ($riskregister->resikos as $resiko) {
            $tindakanData = [];
            foreach ($riskregister->tindakan as $tindakan) {
                $tglRealisasiTerakhir = $tindakan->tgl_penyelesaian;
                $targetpicName = $tindakan->user ? $tindakan->user->nama_user : 'Tidak ada targetpic';

                $tindakanData[] = [
                    'pihak' => $tindakan->pihak,
                    'nama_tindakan' => $tindakan->nama_tindakan,
                    'targetpic' => $targetpicName,
                    'tgl_penyelesaian' => $tglRealisasiTerakhir,
                ];
            }

            // Sort tindakan data by pihak
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
                'tgl_penyelesaian' => array_column($tindakanData, 'tgl_penyelesaian'),
                'status' => $resiko->status,
                'risk' => $resiko->risk,
                'before' => $resiko->before,
                'after' => $resiko->after,
            ];
        }
    }

    // Generate PDF
    $pdf = PDF::loadView('pdf.risk_opportunity_export', compact('formattedData'))
              ->setPaper('A4', 'landscape');

    return $pdf->download('risk_opportunity_export.pdf');
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
                $tglRealisasiTerakhir = $tindakan->tgl_penyelesaian;

                $tindakanData[] = [
                    'nama_tindakan' => $tindakan->nama_tindakan,
                    'targetpic' => $tindakan->targetpic,
                    'tgl_penyelesaian' => $tglRealisasiTerakhir,
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
                'tgl_penyelesaian' => array_column($tindakanData, 'tgl_penyelesaian'),
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
}



