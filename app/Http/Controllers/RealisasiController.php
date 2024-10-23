<?php

namespace App\Http\Controllers;

use App\Models\Resiko;
use App\Models\Realisasi;
use App\Models\Riskregister;
use App\Models\Tindakan;
use Illuminate\Http\Request;

class RealisasiController extends Controller
{
    public function index($id)
    {
        // Ambil data tindakan berdasarkan ID
        $form = Tindakan::findOrFail($id);

        // Ambil data realisasi yang terkait dengan id_tindakan
        $realisasiList = Realisasi::where('id_tindakan', $id)->get();
        $tindak = Tindakan::where('id', $id)->value('nama_tindakan');
        $pic = Tindakan::where('id',$id)->value('targetpic');
        $deadline = Tindakan::where('id',$id)->value('tgl_penyelesaian');

        // Ambil id_divisi dari tabel Riskregister berdasarkan id_tindakan
        $riskregister = Riskregister::where('id', $form->id_riskregister)->first();
        $divisi = $riskregister->id_divisi;

        // Ambil data tambahan yang mungkin diperlukan
        $data = Riskregister::where('id', $riskregister->id)->get();

        // Return view dengan data yang relevan
        return view('realisasi.index', compact('form', 'realisasiList', 'data', 'divisi', 'id','tindak','pic','deadline'));
    }

    public function edit($id)
    {
        // Ambil data realisasi yang ingin diedit
        $realisasi = Realisasi::findOrFail($id);

        // Ambil tindakan yang terkait sebagai informasi tambahan
        $tindakan = Tindakan::findOrFail($realisasi->id_tindakan);

        // Ambil nama tindakan dan PIC dari tindakan
        $tindak = $tindakan->nama_tindakan;
        $pic = $tindakan->targetpic;

        // Ambil semua realisasi yang terkait dengan id_tindakan
        $realisasiList = Realisasi::where('id_tindakan', $realisasi->id_tindakan)->get();

        return view('realisasi.edit', compact('realisasi', 'tindakan', 'tindak', 'realisasiList'));
    }


    public function store(Request $request)
    {
        // Validasi input form
        $validated = $request->validate([
            'id_tindakan' => 'required|exists:tindakan,id',
            'nama_realisasi.*' => 'nullable|string|max:255',
            'tgl_realisasi.*' => 'nullable|date',
            'target.*' => 'nullable|string|max:255',
            'desc.*' => 'nullable|string',
            'presentase.*' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:ON PROGRES,CLOSE',
        ]);

        // Ambil id_riskregister dari tindakan
        $id_riskregister = Tindakan::where('id', $validated['id_tindakan'])->value('id_riskregister');

        if (!empty($validated['nama_realisasi'])) {
            foreach ($validated['tgl_realisasi'] as $key => $tgl_realisasi) {
                // Simpan realisasi
                $realisasi = Realisasi::create([
                    'id_tindakan' => $validated['id_tindakan'],
                    'id_riskregister' => $id_riskregister,
                    'status' => $validated['status'] ?? null,
                    'nama_realisasi' => $validated['nama_realisasi'][$key],
                    'tgl_realisasi' => $tgl_realisasi,
                    'target' => $validated['target'][$key] ?? null,
                    'desc' => $validated['desc'][$key] ?? null,
                    'presentase' => $validated['presentase'][$key] ?? null,
                    'nilai_akhir' => $validated['presentase'][$key] ?? null, // Simpan nilai akhir di realisasi
                ]);
            }
        }

        // Hitung total persentase dan jumlah aktivitas
        $realisasiList = Realisasi::where('id_tindakan', $validated['id_tindakan'])->get();
        $totalPresentase = $realisasiList->sum('presentase');
        $jumlahActivity = $realisasiList->count();

        // Menghitung nilai rata-rata persentase
        $nilaiAkhir = $jumlahActivity > 0 ? round($totalPresentase / $jumlahActivity, 2) : 0;

        // Simpan nilai akhir ke tabel realisasi terbaru
        Realisasi::where('id_tindakan', $validated['id_tindakan'])
            ->update(['nilai_akhir' => $nilaiAkhir]);  // Update nilai_akhir di setiap realisasi

        // Hitung nilai_actual keseluruhan untuk id_riskregister yang sama
        $nilaiActual = Realisasi::where('id_riskregister', $id_riskregister)->sum('nilai_akhir');
        $jumlahTindakan = Realisasi::where('id_riskregister', $id_riskregister)->count('id_tindakan');

        // Menghitung rata-rata nilai_actual
        $rataNilaiActual = $jumlahTindakan > 0 ? round($nilaiActual / $jumlahTindakan, 2) : 0;

        // Update nilai_actual di realisasi terbaru
        Realisasi::where('id_tindakan', $validated['id_tindakan'])
            ->update(['nilai_actual' => $rataNilaiActual]);

        // Update status di tabel resiko jika ada
        if (isset($validated['status'])) {
            $resiko = Resiko::where('id', $id_riskregister)->first();
            if ($resiko) {
                $resiko->status = $validated['status']; // Ambil status dari input form
                $resiko->save();
            }

            // Cek apakah ada status yang bukan 'CLOSE' untuk id_riskregister yang sama
            $hasOpenStatus = Realisasi::where('id_riskregister', $id_riskregister)
                ->where('status', '!=', 'CLOSE')
                ->exists();

            // Jika tidak ada status selain CLOSE, update status di tabel resiko menjadi 'CLOSE'
            if (!$hasOpenStatus) {
                $resiko->status = 'CLOSE';
            } else {
                $resiko->status = 'ON PROGRES';
            }
            $resiko->save();
        }

        return redirect()->route('realisasi.index', ['id' => $validated['id_tindakan']])
            ->with('success', 'Activity berhasil ditambahkan!.✅');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'nama_realisasi' => 'nullable|string|max:255',
            'tgl_realisasi' => 'nullable|date',
            'target' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'presentase' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:ON PROGRES,CLOSE',
        ]);

        // Temukan realisasi berdasarkan ID
        $realisasi = Realisasi::findOrFail($id);

        // Update field pada realisasi yang ditemukan
        $realisasi->nama_realisasi = $validated['nama_realisasi'] ?? $realisasi->nama_realisasi;
        $realisasi->target = $validated['target'] ?? $realisasi->target;
        $realisasi->desc = $validated['desc'] ?? $realisasi->desc;
        $realisasi->tgl_realisasi = $validated['tgl_realisasi'] ?? $realisasi->tgl_realisasi;
        $realisasi->presentase = $validated['presentase'] ?? $realisasi->presentase;

        // Update status jika ada
        if (isset($validated['status'])) {
            $realisasi->status = $validated['status'];
        }

        // Simpan perubahan untuk realisasi yang spesifik ini
        $realisasi->save();

        // Update status di tabel resiko
        if (isset($validated['status'])) {
            $id_riskregister = $realisasi->id_riskregister;
            $resiko = Resiko::where('id', $id_riskregister)->first();

            // Cek semua status di tabel realisasi untuk id_riskregister yang sama
            $hasOpenStatus = Realisasi::where('id_riskregister', $id_riskregister)
                ->where('status', '!=', 'CLOSE')
                ->exists();

            // Jika tidak ada status selain CLOSE, update status di tabel resiko menjadi 'CLOSE'
            $resiko->status = $hasOpenStatus ? 'ON PROGRES' : 'CLOSE';
            $resiko->save();
        }

        // Menghitung total persentase dan jumlah aktivitas untuk id_tindakan
        $id_tindakan = $realisasi->id_tindakan;
        $realisasiList = Realisasi::where('id_tindakan', $id_tindakan)->get();
        $totalPresentase = $realisasiList->sum('presentase');
        $jumlahActivity = $realisasiList->count();

        $nilaiAkhir = $jumlahActivity > 0 ? round($totalPresentase / $jumlahActivity, 2) : 0;

        // Simpan nilai akhir di tabel realisasi untuk id_tindakan
        Realisasi::where('id_tindakan', $id_tindakan)
            ->update(['nilai_akhir' => $nilaiAkhir]); // Update semua nilai akhir untuk id_tindakan yang sama

        // Hitung nilai_actual keseluruhan untuk id_riskregister yang sama
        $nilaiActual = Realisasi::where('id_riskregister', $realisasi->id_riskregister)->sum('nilai_akhir');
        $jumlahTindakan = Realisasi::where('id_riskregister', $realisasi->id_riskregister)->count('id_tindakan');

        // Menghitung rata-rata nilai_actual
        $rataNilaiActual = $jumlahTindakan > 0 ? round($nilaiActual / $jumlahTindakan, 2) : 0;

        // Update nilai_actual di tabel realisasi untuk id_riskregister yang sama
        Realisasi::where('id_riskregister', $realisasi->id_riskregister)
            ->update(['nilai_actual' => $rataNilaiActual]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('realisasi.index', ['id' => $id_tindakan])
            ->with('success', 'Activity berhasil diperbarui!.✅');
    }



    public function getDetail($id)
    {
        // Mengambil data track record berdasarkan ID
        $details = Realisasi::where('id_tindakan', $id)->get(['nama_realisasi', 'tgl_penyelesaian']);
        return response()->json($details);
    }
}
