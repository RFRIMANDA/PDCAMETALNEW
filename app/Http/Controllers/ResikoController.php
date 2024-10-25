<?php

namespace App\Http\Controllers;

use App\Models\Resiko;
use App\Models\Realisasi;
use App\Models\Divisi;
use App\Models\Riskregister; // Pastikan untuk mengimpor model Riskregister
use App\Models\Tindakan;
use Illuminate\Http\Request;

class ResikoController extends Controller
{
    public function index($id)
    {
        // Ambil semua resiko yang memiliki id_riskregister yang sesuai
        $resikos = Resiko::where('id_riskregister', $id)->paginate(10); // Ganti 10 dengan jumlah item per halaman yang kamu inginkan

        return view('resiko.index', compact('resikos'));
    }


    public function create($id, $resikoId = null)
    {
        $enchan = $id;
        $divisi = Divisi::all();
        $riskregister = Riskregister::find($id);

        if (!$riskregister) {
            return redirect()->route('riskregister.index')->with('error', 'Risk Register tidak ditemukan.');
        }

        $resiko = null;
        if ($resikoId) {
            $resiko = Resiko::find($resikoId); // Mengambil data resiko jika sedang dalam mode edit
        }

        return view('resiko.create', compact('enchan', 'divisi', 'id', 'riskregister', 'resiko',));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_riskregister' => 'required|exists:riskregister,id',
            'nama_resiko' => 'nullable|string|max:255',
            'kriteria' => 'nullable|in:Unsur keuangan / Kerugian,Safety & Health,Enviromental (lingkungan),Reputasi,Financial,Operational,Kinerja',
            'probability' => 'nullable|integer|min:1|max:5',
            'severity' => 'nullable|integer|min:1|max:5',
            'probabilityrisk' => 'nullable|integer|min:1|max:5',
            'severityrisk' => 'nullable|integer|min:1|max:5',
            'before' => 'nullable|string|max:255',
            'after' => 'nullable|string|max:255',
        ]);

        // Buat instance model Resiko
        $resiko = new Resiko();
        $resiko->id_riskregister = $request->input('id_riskregister');
        $resiko->nama_resiko = $request->input('nama_resiko');
        $resiko->kriteria = $request->input('kriteria');
        $resiko->probability = $request->input('probability');
        $resiko->severity = $request->input('severity');

        // Hitung tingkatan berdasarkan probability dan severity
        $resiko->calculateTingkatan();

        // Set nilai before dari input pengguna
        $resiko->before = $request->input('before');

        // Hitung risk berdasarkan probabilityrisk dan severityrisk
        $resiko->probabilityrisk = $request->input('probabilityrisk');
        $resiko->severityrisk = $request->input('severityrisk');
        $resiko->calculateRisk(); // Menggunakan kategori lama
        $resiko->calculateRiskNew(); // Menghitung kategori baru

        // Set nilai after dari input pengguna
        $resiko->after = $request->input('after');

        // Simpan data ke database
        $resiko->save();

        // Update status resiko berdasarkan realisasi
        $this->updateStatusResiko($resiko);

        return redirect()->route('resiko.index', ['id' => $resiko->id_riskregister])->with('success', 'Data resiko berhasil disimpan.✅');
    }

    public function edit($id)
    {
        $resiko = Resiko::findOrFail($id); // Mengambil data resiko berdasarkan id
        return view('resiko.edit', compact('resiko'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama_resiko' => 'nullable|string|max:255',
            'kriteria' => 'nullable|in:Unsur keuangan / Kerugian,Safety & Health,Enviromental (lingkungan),Reputasi,Financial,Operational,Kinerja',
            'probability' => 'nullable|integer|min:1|max:5',
            'severity' => 'nullable|integer|min:1|max:5',
            'status' => 'nullable|in:OPEN,ON PROGRES,CLOSE', // Pastikan status valid
            'probabilityrisk' => 'nullable|integer|min:1|max:5',
            'severityrisk' => 'nullable|integer|min:1|max:5',
            'before' => 'nullable|string|max:255',
            'after' => 'nullable|string|max:255',
        ]);

        // Ambil data resiko berdasarkan ID
        $resiko = Resiko::findOrFail($id);

        // Update data resiko
        $resiko->nama_resiko = $request->input('nama_resiko');
        $resiko->kriteria = $request->input('kriteria');
        $resiko->probability = $request->input('probability');
        $resiko->severity = $request->input('severity');
        $resiko->calculateTingkatan(); // Hitung tingkatan berdasarkan probability dan severity
        $resiko->before = $request->input('before');

        // Ambil status dari tabel realisasi
        $nilaiAkhir = Realisasi::where('id_tindakan', $resiko->id_tindakan)->value('nilai_akhir');
        $statusRealisasi = Realisasi::where('id_riskregister', $resiko->id_riskregister)->value('status');

        // Set status dari nilai yang diambil
        if ($statusRealisasi) {
            $resiko->status = $statusRealisasi; // Pastikan status diisi
        } else {
            $resiko->status = $request->input('status'); // Jika tidak ada status di tabel realisasi, ambil dari input
        }

        // Update nilai risk
        $resiko->probabilityrisk = $request->input('probabilityrisk');
        $resiko->severityrisk = $request->input('severityrisk');
        $resiko->calculateRisk(); // Hitung risk berdasarkan probabilityrisk dan severityrisk
        $resiko->calculateRiskNew(); // Menghitung kategori baru
        $resiko->after = $request->input('after');

        // Simpan perubahan ke database
        $resiko->save();

        // Ambil nilai actual dari tabel realisasi berdasarkan id_riskregister
        $nilaiActual = Realisasi::where('id_riskregister', $resiko->id_riskregister)->sum('nilai_actual');

        // Redirect ke halaman matriks dengan nilai akhir dan nilai actual yang dikirim ke view
        return redirect()->route('resiko.matriks', ['id' => $resiko->id_riskregister])
            ->with('success', 'Data berhasil diupdate. ✅')
            ->with('nilai_akhir', $nilaiAkhir) // Mengirim nilai akhir ke view
            ->with('nilai_actual', $nilaiActual); // Mengirim nilai actual ke view
    }

    // private function updateStatusResiko($resiko)
    // {
    //     // Ambil semua realisasi yang terkait dengan id_tindakan
    //     $id_tindakan = $resiko->id_tindakan; // Anda perlu memastikan ini ada di model Resiko
    //     $realisasiStatuses = Realisasi::where('id_tindakan', $id_tindakan)->pluck('status');

    //     // Cek apakah ada yang ON PROGRES
    //     if ($realisasiStatuses->contains('ON PROGRES')) {
    //         $resiko->status = 'ON PROGRES';
    //     } elseif ($realisasiStatuses->contains('CLOSE')) {
    //         $resiko->status = 'CLOSE';
    //     } else {
    //         $resiko->status = 'OPEN'; // Atau status default lainnya
    //     }

    //     // Simpan status resiko
    //     $resiko->save();
    // }

    public function matriks($id)
    {
        // Ambil data yang sama untuk matriks pertama
        $kategori = null;
        $resiko_nama = Resiko::where('id', $id)->value('nama_resiko');

        // Matriks lama dan baru
        $matriks = [
            [1, 2, 3, 4, 5],
            [2, 4, 6, 8, 10],
            [3, 6, 9, 12, 15],
            [4, 8, 12, 16, 20],
            [5, 10, 15, 20, 25],
        ];

        $matriksnew = [
            [1, 2, 3, 4, 5],
            [2, 4, 6, 8, 10],
            [3, 6, 9, 12, 15],
            [4, 8, 12, 16, 20],
        ];

        // Warna matriks lama dan baru
        $colors = [
            ['green', 'green', 'yellow', 'yellow', 'red'],
            ['green', 'yellow', 'red', 'red', 'red'],
            ['yellow', 'red', 'red', 'red', 'red'],
            ['yellow', 'red', 'red', 'red', 'red'],
            ['red', 'red', 'red', 'red', 'red'],
        ];

        $colorsnew = [
            ['green', 'green', 'yellow', 'yellow', 'red'],
            ['green', 'yellow', 'red', 'red', 'red'],
            ['yellow', 'red', 'red', 'red', 'red'],
            ['yellow', 'red', 'red', 'red', 'red'],
        ];

        $same = Tindakan::where('id_riskregister', $id)->value('id_riskregister');
        $form = Tindakan::findOrFail($id);
        $riskregister = Riskregister::where('id', $form->id_riskregister)->first();
        $samee = $riskregister->id_divisi;


        $divisi = Divisi::where('id', $id)->value('nama_divisi');
        $resiko = Resiko::where('id_riskregister', $id)->first();

        $status = Resiko::where('id', $id)->value('status'); // Pastikan kolom 'status' ada di tabel Tindakan

        // Hitung nilai_actual dari Realisasi
        $totalNilaiAkhir = Realisasi::where('id_riskregister', $id)->sum('nilai_akhir');
        $jumlahEntry = Realisasi::where('id_riskregister', $id)->count();
        $actual = $jumlahEntry > 0 ? round($totalNilaiAkhir / $jumlahEntry, 2) : 0; // Hitung nilai_actual

        if ($resiko) {
            // Matriks pertama
            $probability = $resiko->probability;
            $severity = $resiko->severity;
            $riskscore = $probability * $severity;
            $tingkatan = $resiko->tingkatan;
            $kategori = $resiko->kriteria;

            // Pilih matriks dan warna berdasarkan kategori
            if (in_array($kategori, ['Reputasi', 'Financial', 'Kinerja', 'Operational'])) {
                // Matriks baru
                $matriks_used = $matriksnew;
                $colors_used = $colorsnew;
            } else {
                // Matriks lama
                $matriks_used = $matriks;
                $colors_used = $colors;
            }

            // Matriks kedua
            $probabilityrisk = $resiko->probabilityrisk;
            $severityrisk = $resiko->severityrisk;
            $riskscorerisk = $probabilityrisk * $severityrisk;

            // Deskripsi severity berdasarkan kategori
            $deskripsiSeverity = $this->getDeskripsiSeverity($kategori);
        } else {
            $probability = $severity = $riskscore = $tingkatan = 'N/A';
            $probabilityrisk = $severityrisk = $riskscorerisk = 'N/A';
            $deskripsiSeverity = [];
        }

        return view('resiko.matriks', compact('matriks_used', 'colors_used', 'divisi', 'probability', 'severity', 'riskscore', 'tingkatan', 'same', 'resiko_nama', 'deskripsiSeverity', 'kategori', 'probabilityrisk', 'severityrisk', 'riskscorerisk', 'status', 'samee', 'actual','matriks','colors'));
    }

    private function getDeskripsiSeverity($kategori)
    {
        if ($kategori == 'Unsur keuangan / Kerugian') {
            return [
                "Gangguan kedalam kecil. Tidak terlalu berpengaruh terhadap reputasi perusahaan.",
                "Gangguan kedalam sedang dan mendapatkan perhatian dari management / corporate / regional.",
                "Gangguan kedalam serius, mendapatkan perhatian dari masyarakat / LSM / media lokal, dapat merugikan bisnis, kemungkinan dapat mengakibatkan tuntutan hukum.",
                "Gangguan sangat serius, berdampak kepada operasional perusahaan dan penjualan. Menarik perhatian media Nasional. Proses hukum hampir pasti.",
                "Bencana. Terhentinya operasional perusahaan, mengakibatkan jatuhnya harga saham. Menarik perhatian media nasional & internasional. Proses hukum yang pasti, tuntutan hukum terhadap Direktur."
            ];
        } elseif ($kategori == 'Safety & Health') {
            return [
                "Hampir tidak ada risiko cedera, berdampak kecil pada K3, memerlukan P3K tetapi pekerja dapat bekerja. No lost time injury.",
                "Cidera/sakit sedang, perlu perawatan medis. Pekerja dapat bekerja kembali tetapi terjadi penurunan performa.",
                "Cidera/sakit yang memerlukan perawatan khusus sehingga mengakibatkan kehilangan waktu kerja.",
                "Meninggal atau cacat fisik permanen karena pekerjaan.",
                "Meninggal lebih dari satu orang atau cedera cacat permanen lebih satu orang akibat dari pekerjaan."
            ];
        } elseif ($kategori == 'Enviromental (lingkungan)') {
            return [
                "Dampak polusi tertahan disekitar atau polusi kecil atau dampak tidak berarti, memerlukan perbaikan/pekerjaan perbaikan kecil dan dapat dipulihkan dengan cepat (< 1 Minggu).",
                "Polusi dengan dampak pada tempat kerja tetapi tidak ada komplain dari pihak luar, memerlukan pekerjaan perbaikan sedang dan dapat dipulihkan dalam waktu 7 hari - 3 bulan.",
                "Polusi berarti atau berpengaruh keluar atau mengakibatkan komplain, memerlukan pekerjaan perbaikan sedang dan dapat dipulihkan dalam waktu 3 - 6 bulan.",
                "Polusi berarti, berpengaruh keluar dan mengakibatkan komplain, memerlukan pekerjaan perbaikan besar dan dapat dipulihkan dalam waktu 6 bulan - 1 tahun.",
                "Polusi besar-besaran baik kedalam maupun keluar, ada tuntutan dari pihak luar serta membutuhkan pekerjaan perbaikan besar dan dapat dipulihkan lebih dari 1 tahun."
            ];
        } elseif ($kategori == 'Reputasi') {
            return [
                "Kejadian / Incident negatif, hanya diketahui internal organisasi tidak ada dampak kepada stakehoder.",
                "Kejadian / Incident negatif, mulai diketahui / berdampak kepada`stakeholders.",
                "Pemberitaan negatif, yang menurukan kepercayaan Stakeholders.",
                "Kemunduran/hilang kepercayaan Stakeholders.",
            ];
        } elseif ($kategori == 'Financial') {
            return [
                "Kerugian / biaya yang harus dikeluarkan ≤ Rp. 1.000.000,-.",
                "Kerugian / biaya yang harus dikeluarkan Rp.1.000.000 >x≥ Rp. 19.000.000,-.",
                "Kerugian / biaya yang harus dikeluarkan Rp.19.000.000 >x≥ Rp. 70.000.000,-.",
                "Kerugian / biaya yang harus dikeluarkan x>Rp. 70.000.000,-.",
            ];

        } elseif ($kategori == 'Operational') {
            return [
                "Menimbulkan gangguan kecil pada fungsi sistem terhadap proses bisnis namun tidak signifikan.",
                "Menimbulkan gangguan 25 - 50 % fungsi operasional atau hanya berdampak pada 1 unit bisnis.",
                "Menimbulkan gangguan 50 - 75 % fungsi operasional atau berdampak pada 2 unit bisnis terkait.",
                "Menimbulkan kegagalan > 75 % proses operasional atau berdampak pada sebagian besar unit bisnis .",
            ];
        } elseif ($kategori == 'Kinerja') {
            return [
                "Menimbulkan penundaan aktivitas (proses tidak dapat dijalankan) ≤ 1 jam.",
                "Menimbulkan penundaan aktivitas (proses tidak dapat dijalankan) 1< x≤ 3 jam.",
                "Menimbulkan penundaan aktivitas (proses tidak dapat dijalankan) 3< x≤ 5 jam.",
                "Menimbulkan penundaan aktivitas (proses tidak dapat dijalankan) >5 Jam (Uraian kerja tidak efektif dan efisien)"
            ];
        } else {
            return [];
        }
    }


}

