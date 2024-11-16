<?php

namespace App\Http\Controllers;

use App\Models\Resiko;
use App\Models\Realisasi;
use App\Models\Divisi;
use App\Models\Kriteria;
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
        $kriteria = Kriteria::all();

        $one = Resiko::findOrFail($id);
        $two = Riskregister::where('id', $one->id_riskregister)->first();
        $three = $two->id_divisi;
        return view('resiko.edit', compact('resiko','kriteria','three'));
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
        return redirect()->route('resiko.matriks2', ['id' => $resiko->id_riskregister])
            ->with('success', 'Data berhasil diupdate. ✅')
            ->with('nilai_akhir', $nilaiAkhir) // Mengirim nilai akhir ke view
            ->with('nilai_actual', $nilaiActual); // Mengirim nilai actual ke view
    }


    public function matriks($id)
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

    $same = Tindakan::where('id_riskregister', $id)->value('id_riskregister');
    $form = Resiko::findOrFail($id);
    $riskregister = Riskregister::where('id', $form->id_riskregister)->first();
    $samee = $riskregister->id_divisi;

    $divisi = Divisi::where('id', $id)->value('nama_divisi');
    $resiko = Resiko::where('id_riskregister', $id)->first();

    // Fetch the status from the Riskregister model
    $status = $riskregister->status;

    // Ensure that 'kategori' is set from 'resiko'
    $kategori = $resiko ? $resiko->kriteria : null;

    // Fetch kriteria data based on the filled 'kategori'
    $kriteriaData = [];
    if ($kategori) {
        $kriteria = Kriteria::where('nama_kriteria', $kategori)->get();

        foreach ($kriteria as $k) {
            // Decode or parse desc_kriteria as array
            $descArray = is_string($k->desc_kriteria) ? json_decode($k->desc_kriteria, true) : $k->desc_kriteria;
            $descArray = is_array($descArray) ? $descArray : explode(',', $k->desc_kriteria);

            if (!empty($filteredDesc)) {
                $kriteriaData[] = [
                    'nama_kriteria' => $k->nama_kriteria,
                    'desc_kriteria' => array_values($filteredDesc),
                    'nilai_kriteria' => $k->nilai_kriteria,
                ];
            }
        }
    }

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


    // dd($data);


    $one = Resiko::findOrFail($id);
    $two = Riskregister::where('id', $one->id_riskregister)->first();
    $three = $two->id_divisi;

    // Pass all variables to the view
    return view('resiko.matriks', compact(
        'matriks_used', 'colors_used', 'divisi', 'probability', 'severity', 'riskscore',
        'tingkatan', 'same', 'resiko_nama', 'deskripsiSeverity', 'kategori', 'probabilityrisk',
        'severityrisk', 'riskscorerisk', 'status', 'samee', 'actual', 'matriks', 'colors',
        'kriteriaData', 'kriteria','kriteria','three'
    ));

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

    $same = Tindakan::where('id_riskregister', $id)->value('id_riskregister');
    $form = Resiko::findOrFail($id);
    $riskregister = Riskregister::where('id', $form->id_riskregister)->first();
    $samee = $riskregister->id_divisi;

    $divisi = Divisi::where('id', $id)->value('nama_divisi');
    $resiko = Resiko::where('id_riskregister', $id)->first();

    // Fetch the status from the Riskregister model
    $status = $riskregister->status;

    // Ensure that 'kategori' is set from 'resiko'
    $kategori = $resiko ? $resiko->kriteria : null;

    // Fetch kriteria data based on the filled 'kategori'
    $kriteriaData = [];
    if ($kategori) {
        $kriteria = Kriteria::where('nama_kriteria', $kategori)->get();

        foreach ($kriteria as $k) {
            // Decode or parse desc_kriteria as array
            $descArray = is_string($k->desc_kriteria) ? json_decode($k->desc_kriteria, true) : $k->desc_kriteria;
            $descArray = is_array($descArray) ? $descArray : explode(',', $k->desc_kriteria);

            if (!empty($filteredDesc)) {
                $kriteriaData[] = [
                    'nama_kriteria' => $k->nama_kriteria,
                    'desc_kriteria' => array_values($filteredDesc),
                    'nilai_kriteria' => $k->nilai_kriteria,
                ];
            }
        }
    }

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

    // dd($data);

    $one = Resiko::findOrFail($id);
    $two = Riskregister::where('id', $one->id_riskregister)->first();
    $three = $two->id_divisi;

    // dd($three);

    // Pass all variables to the view
    return view('resiko.matriks2', compact(
        'matriks_used', 'colors_used', 'divisi', 'probability', 'severity', 'riskscore',
        'tingkatan', 'same', 'resiko_nama', 'deskripsiSeverity', 'kategori', 'probabilityrisk',
        'severityrisk', 'riskscorerisk', 'status', 'samee', 'actual', 'matriks', 'colors',
        'kriteriaData', 'kriteria','kriteria','three'
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

        // Return the mapped descriptions
        return $mappedDeskripsi;
    }
}
