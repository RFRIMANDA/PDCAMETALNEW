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
                'nama_resiko' => 'nullable|required_without:peluang|string',
                'peluang' => 'nullable|required_without:nama_resiko|string',
                'kriteria' => 'nullable|in:Unsur keuangan / Kerugian,Safety & Health,Enviromental (lingkungan),Reputasi,Financial,Operational,Kinerja',
                'probability' => 'required|integer|min:1|max:5',
                'severity' => 'required|integer|min:1|max:5',
                'nama_tindakan' => 'required|array',
                'nama_tindakan.*' => 'required|string',
                'pihak' => 'required|array',
                'pihak.*' => 'required|string',
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
                    'pihak' => $validated['pihak'][$key],
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
        // Validasi input
        $validated = $request->validate([
            'id_divisi' => 'required|exists:divisi,id',
            'issue' => 'required|string',
            'peluang' => 'nullable|string',
            'tindakan' => 'nullable|array',
            'tindakan.*' => 'nullable|string',
            'pihak' => 'nullable|array',
            'pihak.*' => 'nullable|string',
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

            if (!empty($tindakan) && !empty($validated['pihak'][$key]) && !empty($validated['targetpic'][$key])) {
                if (isset($existingTindakan[$key])) {
                    // Update tindakan yang ada
                    $existingTindakan[$key]->update([
                        'nama_tindakan' => $tindakan,
                        'targetpic' => $validated['targetpic'][$key],
                        'pihak' => $validated['pihak'][$key],
                        'tgl_penyelesaian' => $tglPenyelesaian
                    ]);
                } else {
                    // Buat tindakan baru jika tidak ada
                    $newTindakan = Tindakan::create([
                        'id_riskregister' => $riskregister->id,
                        'nama_tindakan' => $tindakan,
                        'targetpic' => $validated['targetpic'][$key],
                        'pihak' => $validated['pihak'][$key],
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

    public function tablerisk($id)
    {
        // Ambil semua riskregister terkait dengan divisi
        $forms = Riskregister::where('id_divisi', $id)->get();
        $data = [];
        $statusIndicators = []; // Array untuk menyimpan status indikator

        foreach ($forms as $form) {
            // Ambil semua tindakan terkait dengan setiap riskregister
            $tindakanList = Tindakan::where('id_riskregister', $form->id)->get();
            $data[$form->id] = $tindakanList;

            // Memeriksa apakah ada tindakan yang sudah berstatus CLOSE
            $data[$form->id] = $tindakanList->map(function ($tindakan) {
                $tindakan->isClosed = Realisasi::where('id_tindakan', $tindakan->id)
                                    ->where('status', 'CLOSE')
                                    ->exists();
                return $tindakan;
            });
        }

        // Tampilkan view dengan data riskregister, tindakan terkait, serta status
        return view('riskregister.tablerisk', compact('forms', 'data'));
    }


    public function biglist(Request $request)
    {
        // Ambil parameter filter dari request
        $tingkatanFilter = $request->input('tingkatan');
        $statusFilter = $request->input('status'); // Status filter sebagai string (bukan array)
        $divisiFilter = $request->input('nama_divisi');
        $yearFilter = $request->input('year'); // Tambahkan parameter filter tahun

        // Ambil user yang sedang login dan divisi yang diizinkan
        $user = Auth::user();
        $allowedDivisi = json_decode($user->type, true); // Decode type user ke array

        // Ambil semua riskregister beserta relasi tindakan, realisasi, risiko, dan divisi
        $query = Riskregister::with(['tindakan.realisasi', 'resikos', 'divisi']);

        // Filter berdasarkan tingkatan jika ada
        if ($tingkatanFilter) {
            $query->whereHas('resikos', function ($q) use ($tingkatanFilter) {
                $q->where('tingkatan', $tingkatanFilter);
            });
        }

        // Filter status: Tangani filter untuk 'OPEN' dan 'ON PROGRES'
        if ($statusFilter == 'open_on_progres') {
            $query->whereHas('resikos', function ($q) {
                $q->whereIn('status', ['OPEN', 'ON PROGRES']);
            });
        } elseif ($statusFilter) {
            // Filter untuk status tunggal
            $query->whereHas('resikos', function ($q) use ($statusFilter) {
                $q->where('status', $statusFilter);
            });
        }

        // Filter berdasarkan hak akses divisi user yang sedang login
        if ($user->role == 'user' && !empty($allowedDivisi)) {
            $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
                $q->whereIn('id', $allowedDivisi); // Filter hanya berdasarkan divisi yang diizinkan
            });
        }

        // Filter tambahan berdasarkan nama divisi jika ada
        if ($divisiFilter) {
            $query->whereHas('divisi', function ($q) use ($divisiFilter) {
                $q->where('nama_divisi', $divisiFilter);
            });
        }

        // Filter berdasarkan tahun penyelesaian pada tabel Realisasi
        if ($yearFilter) {
            $query->whereHas('tindakan.realisasi', function ($q) use ($yearFilter) {
                $q->whereYear('tgl_penyelesaian', $yearFilter);
            });
        }

        // Ambil data yang sudah difilter
        $data = $query->get();

        // Format data yang akan dikembalikan ke view
        $formattedData = [];

        foreach ($data as $riskregister) {
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
            ];
        }

        // Urutkan data berdasarkan skor tertinggi
        $formattedData = collect($formattedData)->sortByDesc('highest_score')->values()->all();

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
                        'pihak' => $tindakan->pihak, // Pihak sekarang adalah inputan string biasa
                        'nama_tindakan' => $tindakan->nama_tindakan,
                        'targetpic' => $tindakan->targetpic,
                        'tgl_realisasi' => $tglRealisasiTerakhir,
                    ];
                }

                // Urutkan tindakan berdasarkan nama pihak (string input)
                usort($tindakanData, function ($a, $b) {
                    return strcmp($a['pihak'], $b['pihak']);
                });

                $formattedData[] = [
                    'issue' => $riskregister->issue,
                    'pihak' => array_column($tindakanData, 'pihak'),
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
                    'pihak' => array_column($tindakanData, 'pihak'),
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



