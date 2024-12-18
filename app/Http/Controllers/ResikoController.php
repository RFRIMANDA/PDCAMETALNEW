<?php

namespace App\Http\Controllers;

use App\Models\Resiko;
use App\Models\Realisasi;
use App\Models\Divisi;
use App\Models\Kriteria;
use App\Models\Riskregister; // Pastikan untuk mengimpor model Riskregister
use App\Models\Tindakan;
use Carbon\Carbon;
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
        'id_riskregister' => 'required|exists:riskregister,id', // Validasi keberadaan riskregister
        'nama_resiko' => 'nullable|string|max:255',
        'kriteria' => 'required|array',        // Pastikan kriteria berupa array
        'kriteria.*' => 'required|string', // Validasi setiap item kriteria
        'probability' => 'nullable|integer|min:1|max:5',  // Probability 1 - 5
        'severity' => 'nullable|integer|min:1|max:5', // Severity 1 - 5
        'probabilityrisk' => 'nullable|integer|min:1|max:5', // Probability untuk risk
        'severityrisk' => 'nullable|integer|min:1|max:5', // Severity untuk risk
        'before' => 'nullable|string|max:255',
        'after' => 'nullable|string|max:255',
    ]);

    // Membuat instance baru untuk Resiko
    $resiko = new Resiko();
    $resiko->id_riskregister = $request->input('id_riskregister');
    $resiko->nama_resiko = $request->input('nama_resiko');

    // Menyimpan kriteria sebagai array (jika ada)
    $resiko->kriteria = $request->input('kriteria') ? implode(', ', $request->input('kriteria')) : null; // Gabungkan kriteria menjadi string

    // Menyimpan probability dan severity
    $resiko->probability = $request->input('probability');
    $resiko->severity = $request->input('severity');

    // Menghitung tingkatan berdasarkan probability dan severity
    $tingkatan = $this->calculateTingkatan($resiko->probability, $resiko->severity);
    $resiko->tingkatan = $tingkatan;  // Simpan tingkatan

    // Menyimpan nilai before
    $resiko->before = $request->input('before');

    // Menghitung risk berdasarkan probabilityrisk dan severityrisk
    $resiko->probabilityrisk = $request->input('probabilityrisk');
    $resiko->severityrisk = $request->input('severityrisk');
    $risk = $this->calculateRisk($resiko->probabilityrisk, $resiko->severityrisk);
    $resiko->risk = $risk; // Simpan nilai risk

    // Menghitung risk kategori baru jika diperlukan
    $this->calculateRiskNew($resiko);

    // Menyimpan nilai after
    $resiko->after = $request->input('after');

    // Simpan data ke database
    $resiko->save();

    // Update status resiko berdasarkan realisasi
    $this->updateStatusResiko($resiko);

    // Redirect dengan pesan sukses
    return redirect()->route('resiko.index', ['id' => $resiko->id_riskregister])->with('success', 'Data resiko berhasil disimpan.✅');
}




    public function edit($id)
{
    // Fetch the resiko data based on the provided ID
    $resiko = Resiko::findOrFail($id);  // Fetch risk data
    $kriteria = Kriteria::all(); // Get all kriteria data
    // dd($kriteria);

    // Fetch the associated Riskregister to get the division ID
    $riskregister = Riskregister::findOrFail($resiko->id_riskregister); // Get Riskregister based on resiko relation
    $divisionId = $riskregister->id_divisi; // Division ID for the current Riskregister

    $one = Resiko::findOrFail($id);
    $two = Riskregister::where('id', $one->id_riskregister)->first();
    $three = $two->id_divisi; // Division ID for the current Riskregister

    $severityOptions = [];
        foreach ($kriteria as $k) {
            // Pecah nilai dan deskripsi berdasarkan koma
            $nilaiArray = explode(',', $k->nilai_kriteria); // Tidak perlu hapus simbol lagi
            $descArray = explode(',', $k->desc_kriteria); // Tidak perlu hapus simbol lagi

            // Menyusun array dengan masing-masing pasangan nilai dan deskripsi
            $severityOptions[] = [
                'nama_kriteria' => $k->nama_kriteria,
                'options' => array_map(function ($nilai, $desc) {
                    return ['value' => trim($nilai), 'desc' => trim($desc)];
                }, $nilaiArray, $descArray),
            ];
        }

    // dd($severityOptions);


    // Pass the data to the view
    return view('resiko.edit', compact('resiko', 'kriteria', 'divisionId','three','severityOptions'));
}

public function update(Request $request, $id)
{
    // Validasi input
    // dd($request->all());
    $request->validate([
        'nama_resiko' => 'nullable|string|max:255',
        'kriteria' => 'nullable|in:Unsur keuangan / Kerugian,Safety & Health,Enviromental (lingkungan),Reputasi,Financial,Operational,Kinerja',
        'probability' => 'nullable|integer|min:1|max:5',
        'severity' => 'nullable|integer|min:1|max:5',
        'status' => 'nullable|in:OPEN,ON PROGRES,CLOSE',
        'probabilityrisk' => 'nullable|integer|min:1|max:5',
        'severityrisk' => 'nullable|integer|min:1|max:5',
        'risk' => 'nullable|string|max:255',
        'before' => 'nullable|string|max:255',
        'after' => 'nullable|string|max:255',
    ]);

    // Temukan data resiko berdasarkan ID
    $resiko = Resiko::findOrFail($id);

    // Update data dari request
    $resiko->nama_resiko = $request->input('nama_resiko');
    $resiko->kriteria = $request->input('kriteria');
    $resiko->probability = $request->input('probability');
    $resiko->severity = $request->input('severity');
    $resiko->before = $request->input('before');
    $resiko->risk = $request->input('risk');

    // Hitung ulang tingkatan
    $resiko->tingkatan = $this->calculateTingkatan($request->input('probability'), $request->input('severity'));

    // Validasi jika tingkatan masih null
    if (is_null($resiko->tingkatan)) {
        return redirect()->back()->withErrors(['tingkatan' => 'Tingkatan tidak dapat kosong. Periksa input probability dan severity.']);
    }

    // Validasi panjang kolom tingkatan
    if (strlen($resiko->tingkatan) > 255) {
        $resiko->tingkatan = substr($resiko->tingkatan, 0, 255);
    }

    // Periksa status Realisasi
    $statusRealisasi = Realisasi::where('id_riskregister', $resiko->id_riskregister)->value('status');
    $resiko->status = $statusRealisasi ?? $request->input('status');

    // Update nilai risiko baru
    $resiko->probabilityrisk = $request->input('probabilityrisk');
    $resiko->severityrisk = $request->input('severityrisk');
    $resiko->after = $request->input('after');

    // Hitung ulang nilai risiko
    $resiko->calculateRisk();
    $resiko->calculateRiskNew();

    // Simpan data
    $resiko->save();

    // Hitung nilai akhir dan nilai aktual
    $nilaiAkhir = Realisasi::where('id_tindakan', $resiko->id_tindakan)->value('nilai_akhir');
    $nilaiActual = Realisasi::where('id_riskregister', $resiko->id_riskregister)->sum('nilai_actual');

    // Redirect dengan pesan sukses
    return redirect()->route('resiko.matriks2', ['id' => $resiko->id_riskregister])
        ->with('success', 'Data berhasil diupdate. ✅')
        ->with('nilai_akhir', $nilaiAkhir)
        ->with('nilai_actual', $nilaiActual);
}

/**
 * Hitung Tingkatan berdasarkan Probability dan Severity
 */
private function calculateTingkatan($probability, $severity)
{
    if ($probability && $severity) {
        $score = $probability * $severity;

        if ($score >= 1 && $score <= 2) {
            return 'LOW';
        } elseif ($score >= 3 && $score <= 4) {
            return 'MEDIUM';
        } elseif ($score >= 5 && $score <= 25) {
            return 'HIGH';
        }
    }
    return null; // Jika tidak ada probability atau severity
}

    public function matriks($id)
{
    try {
        // Fetch the resiko name from the Resiko table
        $resiko_nama = Resiko::where('id', $id)->value('nama_resiko');

        // Define matriks and colors for risk categories
        $matriks = [
            [1, 2, 3, 4, 5],
            [2, 4, 6, 8, 10],
            [3, 6, 9, 12, 15],
            [4, 8, 12, 16, 20],
            [5, 10, 15, 20, 25],
        ];

        $colors = [
            ['green', 'green', 'yellow', 'yellow', 'red'],
            ['green', 'yellow', 'red', 'red', 'red'],
            ['yellow', 'red', 'red', 'red', 'red'],
            ['yellow', 'red', 'red', 'red', 'red'],
            ['red', 'red', 'red', 'red', 'red'],
        ];

        $lol = Resiko::where('id_riskregister', $id)->value('id');
        if (!$lol) {
            return back()->withErrors(['error' => 'Data resiko tidak ditemukan untuk ID riskregister: ' . $id]);
        }


        // Fetch the riskregister, resiko, and divisi based on the id
        $riskregister = Riskregister::findOrFail($id);
        $resiko = Resiko::where('id_riskregister', $riskregister->id)->first();

        // Get divisi name
        $divisi = Divisi::where('id', $riskregister->id_divisi)->value('nama_divisi');

        // Get resiko details
        $status = $riskregister->status;
        $kategori = $resiko ? $resiko->kriteria : null;

        // Initialize kriteriaData based on the 'kategori'
        $kriteriaData = [];
            if ($kategori) {
                $kriteria = Kriteria::where('nama_kriteria', $kategori)->first();
                if ($kriteria) {
                    // Mengonversi string 'desc_kriteria' yang sudah di-implode menjadi array
                    $descArray = explode(',', $kriteria->desc_kriteria);

                    // Menghilangkan elemen kosong dari array
                    $filteredDesc = array_filter($descArray);

                    // Pastikan ada data sebelum dimasukkan ke dalam $kriteriaData
                    if (!empty($filteredDesc)) {
                        $kriteriaData[] = [
                            'nama_kriteria' => $kriteria->nama_kriteria,
                            'desc_kriteria' => array_values($filteredDesc), // Reset indeks array
                            'nilai_kriteria' => $kriteria->nilai_kriteria,
                        ];
                    }
                }
            }

        // Calculate actual progress based on 'realisasi' data
        $totalNilaiAkhir = Realisasi::where('id_riskregister', $id)->sum('nilai_akhir');
        $jumlahEntry = Realisasi::where('id_riskregister', $id)->count();
        $actual = $jumlahEntry > 0 ? round($totalNilaiAkhir / $jumlahEntry, 2) : 0;

        // Default values in case there are no risk data
        $probability = $severity = $riskscore = $tingkatan = 'N/A';
        $probabilityrisk = $severityrisk = $riskscorerisk = 'N/A';
        $deskripsiSeverity = [];

        // If resiko exists, calculate values
        if ($resiko) {
            $probability = $resiko->probability;
            $severity = $resiko->severity;
            $riskscore = $probability * $severity;
            $tingkatan = $resiko->tingkatan;

            // Adjust matrix and colors based on kategori
            if (in_array($kategori, ['Reputasi', 'Financial', 'Kinerja', 'Operational', 'Unsur Keuangan / Kerugian', 'Safety & Health', 'Enviromental (lingkungan)'])) {
                $matriks_used = $matriks;
                $colors_used = $colors;
            } else {
                $matriks_used = $matriks;
                $colors_used = $colors;
            }

            // Additional risk data for specific categories
            $probabilityrisk = $resiko->probabilityrisk;
            $severityrisk = $resiko->severityrisk;
            $riskscorerisk = $probabilityrisk * $severityrisk;
            $deskripsiSeverity = $this->getDeskripsiSeverity($kategori);
        }

        // Fetch kriteria data based on the selected kategori
        $kriteriaData = Kriteria::where('nama_kriteria', $kategori)->get();
        if (!$kategori) {
            $kriteriaData = Kriteria::all(); // If no kategori is selected, fetch all kriteria
        }

        // Get division ID
        $three = $riskregister->id_divisi;

        // Return view with all the necessary data
        return view('resiko.matriks', compact(
            'matriks_used', 'colors_used', 'divisi', 'probability', 'severity', 'riskscore',
            'tingkatan', 'resiko_nama', 'deskripsiSeverity', 'kategori', 'probabilityrisk',
            'severityrisk', 'riskscorerisk', 'status', 'kriteriaData', 'three','lol'
        ));
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
}


public function matriks2($id)
{
    // Fetch Riskregister data and related information
    $resiko_nama = Resiko::where('id', $id)->value('nama_resiko');

    // Define the matriks data
    $matriks = [
        [1, 2, 3, 4, 5],
        [2, 4, 6, 8, 10],
        [3, 6, 9, 12, 15],
        [4, 8, 12, 16, 20],
        [5, 10, 15, 20, 25],
    ];

    $colors = [
        ['green', 'green', 'yellow', 'yellow', 'red'],
        ['green', 'yellow', 'red', 'red', 'red'],
        ['yellow', 'red', 'red', 'red', 'red'],
        ['yellow', 'red', 'red', 'red', 'red'],
        ['red', 'red', 'red', 'red', 'red'],
    ];

    // Fetch the relevant data
    $same = Tindakan::where('id_riskregister', $id)->value('id_riskregister');
    $form = Resiko::findOrFail($id);
    $riskregister = Riskregister::where('id', $form->id_riskregister)->first();
    $samee = $riskregister->id_divisi;

    $divisi = Divisi::where('id', $id)->value('nama_divisi');
    $resiko = Resiko::where('id_riskregister', $id)->first();

    $status = $riskregister->status;
    $kategori = $resiko ? $resiko->kriteria : null;

    // Fetch kriteria data based on the selected 'kategori'
    $kriteriaData = [];
    if ($kategori) {
        $kriteria = Kriteria::where('nama_kriteria', $kategori)->get();

        foreach ($kriteria as $k) {
            // Decode or parse desc_kriteria as array
            $descArray = is_string($k->desc_kriteria) ? json_decode($k->desc_kriteria, true) : $k->desc_kriteria;
            $descArray = is_array($descArray) ? $descArray : explode(',', $k->desc_kriteria);

            if (!empty($descArray)) {
                $kriteriaData[] = [
                    'nama_kriteria' => $k->nama_kriteria,
                    'desc_kriteria' => array_values($descArray),
                    'nilai_kriteria' => $k->nilai_kriteria,
                ];
            }
        }
    }

    // Calculate risk data
    $totalNilaiAkhir = Realisasi::where('id_riskregister', $id)->sum('nilai_akhir');
    $jumlahEntry = Realisasi::where('id_riskregister', $id)->count();
    $actual = $jumlahEntry > 0 ? round($totalNilaiAkhir / $jumlahEntry, 2) : 0;

    // Default values in case there are no risk data
    $probability = $severity = $riskscore = $tingkatan = 'N/A';
    $probabilityrisk = $severityrisk = $riskscorerisk = 'N/A';
    $deskripsiSeverity = [];

    if ($resiko) {
        $probability = $resiko->probability;
        $severity = $resiko->severity;
        $riskscore = $probability * $severity;
        $tingkatan = $resiko->tingkatan;

        if (in_array($kategori, ['Reputasi', 'Financial', 'Kinerja', 'Operational', 'Unsur Keuangan / Kerugian', 'Safety & Health', 'Enviromental (lingkungan)'])) {
            $matriks_used = $matriks;
            $colors_used = $colors;
        } else {
            $matriks_used = $matriks;
            $colors_used = $colors;
        }

        $probabilityrisk = $resiko->probabilityrisk;
        $severityrisk = $resiko->severityrisk;
        $riskscorerisk = $probabilityrisk * $severityrisk;
        $deskripsiSeverity = $this->getDeskripsiSeverity($kategori);
    }

    // Fetch kriteria data based on the selected 'kategori' in your model
    if ($kategori) {
        $kriteriaData = Kriteria::where('nama_kriteria', $kategori)->get();
    } else {
        $kriteriaData = Kriteria::all(); // If no kategori is selected, fetch all kriteria
    }

    // Retrieve division id for the 'three' variable
    $one = Resiko::findOrFail($id);
    $two = Riskregister::where('id', $one->id_riskregister)->first();
    $three = $two->id_divisi;

    // Pass all variables to the view
    return view('resiko.matriks2', compact(
        'matriks_used', 'colors_used', 'divisi', 'probability', 'severity', 'riskscore',
        'tingkatan', 'same', 'resiko_nama', 'deskripsiSeverity', 'kategori', 'probabilityrisk',
        'severityrisk', 'riskscorerisk', 'status', 'samee', 'actual', 'matriks', 'colors',
        'kriteriaData', 'three'
    ));
}


    private function getDeskripsiSeverity($kategori)
    {
        // Fetch the kriteria data based on 'nama_kriteria' matching the category (kategori)
        $deskripsiSeverity = Kriteria::where('nama_kriteria', $kategori)
                                      ->orderBy('nilai_kriteria')
                                      ->pluck('desc_kriteria', 'nilai_kriteria'); // Getting 'desc_kriteria' and 'nilai_kriteria' as key-value pairs

        // Map descriptions based on the nilai_kriteria
        $mappedDeskripsi = [];
        foreach ($deskripsiSeverity as $nilai => $desc) {
            // Assuming desc_kriteria is a comma-separated string, we split it into an array
            $mappedDeskripsi[$nilai] = explode(',', $desc);
        }

        return $mappedDeskripsi;
    }
}
