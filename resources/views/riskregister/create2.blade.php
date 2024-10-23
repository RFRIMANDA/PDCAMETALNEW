{{-- UPDATE --}}
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
        'pihak.*' => 'nullable|exists:divisi,id',
        'targetpic' => 'nullable|array',
        'targetpic.*' => 'nullable|string',
        'tgl_penyelesaian' => 'nullable|array',
        'tgl_penyelesaian.*' => 'nullable|date' // Ini memperbaiki agar menerima array tanggal
    ]);

    // Cari riskregister berdasarkan ID
    $riskregister = Riskregister::findOrFail($id);

    // Update data riskregister
    $riskregister->update([
        'id_divisi' => $request->input('id_divisi'),
        'issue' => $request->input('issue'),
        'peluang' => $request->input('peluang'),
    ]);

    // Ambil tindakan yang ada
    $existingTindakan = Tindakan::where('id_riskregister', $riskregister->id)->get()->keyBy('id');

    // Update atau buat data tindakan baru
    foreach ($validated['tindakan'] as $key => $tindakan) {
        if (!empty($tindakan) && !empty($validated['pihak'][$key]) && !empty($validated['targetpic'][$key])) {
            // Jika tindakan sudah ada, update
            if (isset($existingTindakan[$key])) {
                $existingTindakan[$key]->update([
                    'nama_tindakan' => $tindakan,
                    'targetpic' => $validated['targetpic'][$key],
                    'pihak' => $validated['pihak'][$key],
                    'tgl_penyelesaian' => $validated['tgl_penyelesaian'][$key], // Memperbarui tgl_penyelesaian
                ]);
            } else {
                // Jika tidak ada, buat tindakan baru
                $newTindakan = Tindakan::create([
                    'id_riskregister' => $riskregister->id,
                    'nama_tindakan' => $tindakan,
                    'targetpic' => $validated['targetpic'][$key],
                    'pihak' => $validated['pihak'][$key],
                    'tgl_penyelesaian' => $validated['tgl_penyelesaian'][$key] // Tambahkan tgl_penyelesaian
                ]);

                // Buat entri realisasi untuk tindakan baru
                // Realisasi::create([
                //     'id_tindakan' => $newTindakan->id,
                //     'id_riskregister' => $riskregister->id,
                // ]);
            }
        }
    }

    return redirect()->route('riskregister.tablerisk', ['id' => $request->input('id_divisi')])
        ->with('success', 'Data berhasil diperbarui!');
}

{{-- UPDATE LOOPING --}}
public function update(Request $request, $id)
{
    // Validasi input
    $validated = $request->validate([
        'id_divisi' => 'required|exists:divisi,id',
        'issue' => 'required|string',
        'peluang' => 'nullable|string',
        'tindakan' => 'required|array', // Require the tindakan array
        'tindakan.*' => 'required|string', // Ensure each tindakan is a string
        'pihak' => 'required|array',
        'pihak.*' => 'required|exists:divisi,id',
        'targetpic' => 'required|array',
        'targetpic.*' => 'required|string',
        'tgl_penyelesaian' => 'required|array',
        'tgl_penyelesaian.*' => 'required|date',
        'probability' => 'required|integer|min:1|max:5',
        'severity' => 'required|integer|min:1|max:5',
    ]);

    // Cari riskregister berdasarkan ID
    $riskregister = Riskregister::findOrFail($id);

    // Hitung ulang tingkatan
    $tingkatan = $this->calculateTingkatan($validated['probability'], $validated['severity']);

    // Update data riskregister
    $riskregister->update([
        'id_divisi' => $validated['id_divisi'],
        'issue' => $validated['issue'],
        'peluang' => $validated['peluang'],
        'target_penyelesaian' => $validated['target_penyelesaian'], // Assumes this field is in the request
    ]);

    // Update data resiko
    $riskregister->resiko()->update([
        'probability' => $validated['probability'],
        'severity' => $validated['severity'],
        'tingkatan' => $tingkatan,
    ]);

    // Hapus tindakan yang ada sebelum menambah yang baru
    Tindakan::where('id_riskregister', $riskregister->id)->delete();

    // Simpan data tindakan baru
    foreach ($validated['tindakan'] as $key => $nama_tindakan) {
        Tindakan::create([
            'id_riskregister' => $riskregister->id,
            'nama_tindakan' => $nama_tindakan,
            'pihak' => $validated['pihak'][$key],
            'targetpic' => $validated['targetpic'][$key],
            'tgl_penyelesaian' => $validated['tgl_penyelesaian'][$key],
        ]);
    }

    return redirect()->route('riskregister.tablerisk', ['id' => $validated['id_divisi']])
        ->with('success', 'Data berhasil diperbarui!');
}

{{-- matriks --}}
@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Matriks Risiko: <br>
    {{$resiko_nama}}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><strong>Tingkatan = {{ $tingkatan }}</strong></h5>
            <p class="card-text"><strong>Probability = {{ $severity }}</strong></p>
            <p class="card-text"><strong>Severity = {{ $probability }}</strong></p>
            <p class="card-text"><strong>Probability x Severity = {{ $riskscore }}</strong></p>
        </div>
    </div>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Severity / Keparahan
                    <p>{{$kategori}}
                </th>
                <th colspan="5">Probability / Dampak (Likelihood)</th>
            </tr>
            <tr>
                <th>1 (Sangat Jarang Terjadi)</th>
                <th>2 (Jarang Terjadi)</th>
                <th>3 (Dapat Terjadi)</th>
                <th>4 (Sering Terjadi)</th>
                <th>5 (Selalu Terjadi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matriks as $i => $row)
            <tr>
                <!-- Menampilkan deskripsi severity berdasarkan kategori yang dipilih -->
                <td>{{ $i + 1 }}</td> <!-- Menampilkan indeks di sini -->
                <td>
                    @if($kategori == 'Environmental (Lingkungan)')
                        @if(isset($deskripsiSeverity[$i]))
                            {{ $deskripsiSeverity[$i] }}
                        @else
                            None
                        @endif
                    @elseif($kategori == 'Safety & Health')
                        @if(isset($deskripsiSeverity[$i]))
                            {{ $deskripsiSeverity[$i] }}
                        @else
                            None
                        @endif
                    @else
                        @if(isset($deskripsiSeverity[$i]))
                            {{ $deskripsiSeverity[$i] }}
                        @else
                            None
                        @endif
                    @endif
                </td>

                @foreach($row as $j => $value)
                <td style="background-color: {{ $colors[$i][$j] }}; color: black;">
                    @if(($i + 1) == $severity && ($j + 1) == $probability)
                        <div class="d-flex align-items-center">
                            <div class="spinner-grow text-info" role="status" style="width: 1.5rem; height: 1.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">{{ $value }}</span>
                        </div>
                    @else
                        {{ $value }}
                    @endif
                </td>
            @endforeach
            </tr>
            @endforeach
        </tbody>

    </table>
    <a class="btn btn-danger" href="{{ route('riskregister.tablerisk', $same) }}" title="Back">
        <i class="bx bx-arrow-back"></i>
    </a>
    <a class="btn btn-warning" href="{{ route('resiko.edit', ['id' => $same]) }}" title="Back">
        <i class="bx bx-edit"></i>
    </a>


</div>
@endsection

{{-- function matriks --}}
public function matriks($id)
{
    // Ambil nama risiko berdasarkan ID
    $kategori = null; // Inisialisasi kategori
    $resiko_nama = Resiko::where('id', $id)->value('nama_resiko');

    // Data untuk tabel matriks risiko
    $matriks = [
        [1, 2, 3, 4, 5], // Row 1
        [2, 4, 6, 8, 10], // Row 2
        [3, 6, 9, 12, 15], // Row 3
        [4, 8, 12, 16, 20], // Row 4
        [5, 10, 15, 20, 25], // Row 5
    ];

    // Warna tiap tingkat risiko
    $colors = [
        ['green', 'green', 'yellow', 'yellow', 'red'],
        ['green', 'yellow', 'red', 'red', 'red'],
        ['yellow', 'red', 'red', 'red', 'red'],
        ['yellow', 'red', 'red', 'red', 'red'],
        ['red', 'red', 'red', 'red', 'red'],
    ];

    // Ambil tindakan terkait risiko
    $same = Tindakan::where('id_riskregister', $id)->value('id_riskregister');
    // Ambil divisi terkait risiko
    $divisi = Divisi::where('id', $id)->value('nama_divisi');

    // Ambil risiko berdasarkan ID risk register
    $resiko = Resiko::where('id_riskregister', $id)->first();

    // Cek apakah risiko ada
    if ($resiko) {
        $probability = $resiko->probability;
        $severity = $resiko->severity;
        $riskscore = $probability * $severity;
        $tingkatan = $resiko->tingkatan;
        $kategori = $resiko->kriteria; // Ambil kategori dari risiko

        // Definisikan deskripsi severity berdasarkan kategori
        if ($kategori == 'Unsur keuangan / Kerugian') {
            $deskripsiKeuangan = [
                "Gangguan kedalam kecil. Tidak terlalu berpengaruh terhadap reputasi perusahaan.",
                "Gangguan kedalam sedang dan mendapatkan perhatian dari management / corporate / regional.",
                "Gangguan kedalam serius, mendapatkan perhatian dari masyarakat / LSM / media lokal, dapat merugikan bisnis, kemungkinan dapat mengakibatkan tuntutan hukum.",
                "Gangguan sangat serius, berdampak kepada operasional perusahaan dan penjualan. Menarik perhatian media Nasional. Proses hukum hampir pasti.",
                "Bencana. Terhentinya operasional perusahaan, mengakibatkan jatuhnya harga saham. Menarik perhatian media nasional & internasional. Proses hukum yang pasti, tuntutan hukum terhadap Direktur."
            ];
            $deskripsiSeverity = $deskripsiKeuangan;
        } elseif ($kategori == 'Safety & Health') {
            $deskripsiSafety = [
                "Hampir tidak ada risiko cedera, berdampak kecil pada K3, memerlukan P3K tetapi pekerja dapat bekerja. No lost time injury.",
                "Cidera/sakit sedang, perlu perawatan medis. Pekerja dapat bekerja kembali tetapi terjadi penurunan performa.",
                "Cidera/sakit yang memerlukan perawatan khusus sehingga mengakibatkan kehilangan waktu kerja.",
                "Meninggal atau cacat fisik permanen karena pekerjaan.",
                "Meninggal lebih dari satu orang atau cedera cacat permanen lebih satu orang akibat dari pekerjaan."
            ];
            $deskripsiSeverity = $deskripsiSafety;
        } elseif ($kategori == 'Enviromental (lingkungan)') {
            $deskripsiEnvironmental = [
                "Dampak polusi tertahan disekitar atau polusi kecil atau dampak tidak berarti, memerlukan perbaikan/pekerjaan perbaikan kecil dan dapat dipulihkan dengan cepat (< 1 Minggu).",
                "Polusi dengan dampak pada tempat kerja tetapi tidak ada komplain dari pihak luar, memerlukan pekerjaan perbaikan sedang dan dapat dipulihkan dalam waktu 7 hari - 3 bulan.",
                "Polusi berarti atau berpengaruh keluar atau mengakibatkan komplain, memerlukan pekerjaan perbaikan sedang dan dapat dipulihkan dalam waktu 3 - 6 bulan.",
                "Polusi berarti, berpengaruh keluar dan mengakibatkan komplain, memerlukan pekerjaan perbaikan besar dan dapat dipulihkan dalam waktu 6 bulan - 1 tahun.",
                "Polusi besar-besaran baik kedalam maupun keluar, ada tuntutan dari pihak luar serta membutuhkan pekerjaan perbaikan besar dan dapat dipulihkan lebih dari 1 tahun."
            ];
            $deskripsiSeverity = $deskripsiEnvironmental;
        } else {
            $deskripsiSeverity = []; // Kategori tidak dikenali
        }
    } else {
        // Jika risiko tidak ditemukan
        $probability = 'N/A';
        $severity = 'N/A';
        $riskscore = 'N/A';
        $tingkatan = 'N/A';
        $deskripsiSeverity = []; // Default jika tidak ada risiko
    }

    // Kirim data ke view
    return view('resiko.matriks', compact('matriks', 'colors', 'divisi', 'probability', 'severity', 'riskscore', 'tingkatan', 'same', 'resiko_nama', 'deskripsiSeverity','kategori'));
}

public function tablerisk($id)
{
    // Ambil semua riskregister terkait dengan divisi
    $forms = Riskregister::where('id_divisi', $id)->get();
    // Ambil semua resiko terkait dengan divisi
    $resikos = Resiko::where('id_riskregister', $id)->get();

    $data = [];
    $divisi = [];
    $statusIndicators = []; // Array untuk menyimpan status indikator

    foreach ($forms as $form) {
        // Ambil semua tindakan terkait dengan setiap riskregister
        $tindakanList = Tindakan::where('id_riskregister', $form->id)->get();
        $data[$form->id] = $tindakanList;

        // Periksa apakah SEMUA tindakan berstatus CLOSE
        $allClosed = $tindakanList->every(function($tindakan) {
            return Realisasi::where('id_tindakan', $tindakan->id)->where('status', 'CLOSE')->exists();
        });

        // Jika semua tindakan berstatus CLOSE, set indikator CLOSE, jika tidak ON PROGRES
        $statusIndicators[$form->id] = $allClosed ? 'CLOSE' : 'ON PROGRES';

        // Ambil semua pihak yang terlibat dalam tindakan
        $pihak = Tindakan::where('id_riskregister', $form->id)->pluck('pihak');
        $divisi[$form->id] = Divisi::whereIn('id', $pihak)->get();
    }

    // Tampilkan view dengan data riskregister, tindakan terkait, resiko, serta status
    return view('riskregister.tablerisk', compact('forms', 'data', 'divisi', 'resikos', 'statusIndicators'));
}

{{-- yang ada di tablerisk --}}


                                                    {{-- @foreach($data[$form->id] as $tindakan)
                                                        @if($tindakan->pihak == $name->id) <!-- Sesuaikan ID divisi dan tindakan -->
                                                            <li>
                                                                <a href="{{ route('realisasi.index', $tindakan->id) }}">
                                                                    {{ $tindakan->nama_tindakan }}
                                                                </a>
                                                                <div>
                                                                    <span class="badge bg-success">{{ $tindakan->tgl_penyelesaian ?? '-' }}</span>
                                                                </div>

                                                                @php
                                                                    $hasCloseStatus = \App\Models\Resiko::where('id_tindakan', $tindakan->id)->where('status', 'CLOSE')->exists();
                                                                @endphp
                                                                @if($hasCloseStatus)
                                                                    <span class="badge bg-danger">CLOSE</span>
                                                                @endif
                                                                <hr>
                                                            </li>
                                                        @endif
                                                    @endforeach --}}



{{-- REALISASI UPDATE STORE TGL LOGIKA --}}
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
        $deadline = Tindakan::where('id', $validated['id_tindakan'])->value('tgl_penyelesaian');

        if (!empty($validated['nama_realisasi'])) {
            foreach ($validated['tgl_realisasi'] as $key => $tgl_realisasi) {
                if ($tgl_realisasi > $deadline) {
                    return redirect()->back()->withErrors(['tgl_realisasi' => 'Tanggal penyelesaian tidak boleh lebih dari tanggal target.']);
                }

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
        $resiko = Resiko::where('id', $id_riskregister)->first();
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
            'nama_realisasi.*' => 'nullable|string|max:255',
            'tgl_realisasi.*' => 'nullable|date',
            'target.*' => 'nullable|string|max:255',
            'desc.*' => 'nullable|string',
            'presentase.*' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:ON PROGRES,CLOSE',
        ]);

        // Temukan realisasi berdasarkan ID
        $realisasi = Realisasi::findOrFail($id);
        $deadline = Tindakan::where('id', $realisasi->id_tindakan)->value('tgl_penyelesaian');

        // Validasi tgl_realisasi tidak lebih dari deadline
        if (!empty($validated['tgl_realisasi'])) {
            foreach ($validated['tgl_realisasi'] as $tgl_realisasi) {
                if ($tgl_realisasi > $deadline) {
                    return redirect()->back()->withErrors(['tgl_realisasi' => 'Tanggal penyelesaian tidak boleh lebih dari tanggal target.']);
                }
            }
        }

        // Ambil semua realisasi terkait id_tindakan
        $realisasiList = Realisasi::where('id_tindakan', $realisasi->id_tindakan)->get();

        foreach ($realisasiList as $key => $realisasi) {
            // Update setiap field berdasarkan ID
            if (!empty($validated['nama_realisasi'])) {
                $realisasi->nama_realisasi = $validated['nama_realisasi'][$key] ?? $realisasi->nama_realisasi;
            }

            if (!empty($validated['target'])) {
                $realisasi->target = $validated['target'][$key] ?? $realisasi->target;
            }

            if (!empty($validated['desc'])) {
                $realisasi->desc = $validated['desc'][$key] ?? $realisasi->desc;
            }

            if (!empty($validated['tgl_realisasi'])) {
                $realisasi->tgl_realisasi = $validated['tgl_realisasi'][$key] ?? $realisasi->tgl_realisasi;
            }

            if (!empty($validated['presentase'])) {
                $realisasi->presentase = $validated['presentase'][$key] ?? $realisasi->presentase;
            }

            // Update status jika ada
            if (isset($validated['status'])) {
                $realisasi->status = $validated['status'];
            }

            // Simpan perubahan setiap realisasi
            $realisasi->save();
        }

        // Update status di tabel resiko
        if (isset($validated['status'])) {
            $id_riskregister = $realisasi->id_riskregister;
            $resiko = Resiko::where('id', $id_riskregister)->first();

            // Cek semua status di tabel realisasi untuk id_riskregister yang sama
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

        // Menghitung total persentase dan jumlah aktivitas untuk id_tindakan
        $realisasiList = Realisasi::where('id_tindakan', $realisasi->id_tindakan)->get();
        $totalPresentase = $realisasiList->sum('presentase');
        $jumlahActivity = $realisasiList->count();

        $nilaiAkhir = $jumlahActivity > 0 ? round($totalPresentase / $jumlahActivity, 2) : 0;

        // Simpan nilai akhir di tabel realisasi
        Realisasi::where('id_tindakan', $realisasi->id_tindakan)
            ->update(['nilai_akhir' => $nilaiAkhir]);

        // Hitung nilai_actual keseluruhan untuk id_riskregister yang sama
        $nilaiActual = Realisasi::where('id_riskregister', $realisasi->id_riskregister)->sum('nilai_akhir');
        $jumlahTindakan = Realisasi::where('id_riskregister', $realisasi->id_riskregister)->count('id_tindakan');

        // Menghitung rata-rata nilai_actual
        $rataNilaiActual = $jumlahTindakan > 0 ? round($nilaiActual / $jumlahTindakan, 2) : 0;

        // Update nilai_actual di tabel resiko
        Realisasi::where('id_tindakan', $realisasi->id_tindakan)
            ->update(['nilai_actual' => $rataNilaiActual]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('realisasi.index', ['id' => $realisasi->id_tindakan])
            ->with('success', 'Activity berhasil diperbarui!.✅');
    }


    {{-- UPDATE STORE RISK TGL --}}
    public function store(Request $request)
    {
        try {
            // Validasi input
            // dd($request->all());
            $validated = $request->validate([
                'id_divisi' => 'required|exists:divisi,id',
                'issue' => 'required|string',
                'nama_resiko' => 'nullable|string',
                'kriteria' => 'nullable|in:Unsur keuangan / Kerugian,Safety & Health,Enviromental (lingkungan)',
                'probability' => 'required|integer|min:1|max:5',
                'severity' => 'required|integer|min:1|max:5',
                'nama_tindakan' => 'required|array',
                'nama_tindakan.*' => 'required|string',
                'pihak' => 'required|array',
                'pihak.*' => 'required|exists:divisi,id',
                'targetpic' => 'required|array',
                'targetpic.*' => 'required|string',
                'tgl_penyelesaian' => 'required|array',
                'tgl_penyelesaian.*' => 'required|date',
                'target_penyelesaian' => 'required|date',
                'peluang' => 'nullable|string',
                'status' => 'nullable|in:OPEN,ON PROGRES,CLOSE',
                'before' => 'nullable|string',
            ]);

            // Hitung tingkatan berdasarkan probability dan severity
            $tingkatan = $this->calculateTingkatan($validated['probability'], $validated['severity']);

            // Simpan data ke tabel riskregister
            $riskregister = Riskregister::create([
                'id_divisi' => $validated['id_divisi'],
                'issue' => $validated['issue'],
                'target_penyelesaian' => $validated['target_penyelesaian'],
                'peluang' => $validated['peluang'],
            ]);

            // Simpan data ke tabel resiko
            $status = $validated['status'] ?? 'OPEN'; // Set status ke 'OPEN' jika tidak diberikan
            Resiko::create([
                'id_riskregister' => $riskregister->id,
                'nama_resiko' => $validated['nama_resiko'],
                'kriteria' => $validated['kriteria'],
                'probability' => $validated['probability'],
                'severity' => $validated['severity'],
                'tingkatan' => $tingkatan, // Simpan tingkatan dinamis
                'status' => $status,
                'before' => $validated['before']
            ]);

            // Cek apakah tanggal penyelesaian lebih besar dari target_penyelesaian
            foreach ($validated['tgl_penyelesaian'] as $key => $tgl_penyelesaian) {
                if ($tgl_penyelesaian > $validated['target_penyelesaian']) {
                    return back()->withErrors([
                        'tgl_penyelesaian' => "Tanggal penyelesaian untuk tindakan ke-" . ($key + 1) . " tidak boleh melebihi target penyelesaian ({$validated['target_penyelesaian']})."
                    ]);
                }
            }

            // Simpan data ke tabel tindakan dan realisasi
            foreach ($validated['nama_tindakan'] as $key => $nama_tindakan) {
                Tindakan::create([
                    'id_riskregister' => $riskregister->id,
                    'nama_tindakan' => $nama_tindakan,
                    'pihak' => $validated['pihak'][$key],
                    'targetpic' => $validated['targetpic'][$key],
                    'tgl_penyelesaian' => $validated['tgl_penyelesaian'][$key],
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
            'pihak.*' => 'nullable|exists:divisi,id',
            'targetpic' => 'nullable|array',
            'targetpic.*' => 'nullable|string',
            'tgl_penyelesaian' => 'nullable|array',
            'tgl_penyelesaian.*' => 'nullable|date_format:Y-m-d',
            'before' => 'nullable'
        ]);

        // Ambil riskregister berdasarkan ID
        $riskregister = Riskregister::findOrFail($id);

        // Ambil target_penyelesaian dari tabel Riskregister
        $targetPenyelesaian = $riskregister->target_penyelesaian;

        // Update data riskregister
        $riskregister->update([
            'id_divisi' => $request->input('id_divisi'),
            'issue' => $request->input('issue'),
            'peluang' => $request->input('peluang'),
        ]);

        // Ambil tindakan yang ada
        $existingTindakan = Tindakan::where('id_riskregister', $riskregister->id)->get()->keyBy('id');

        // Update atau buat data tindakan baru
        foreach ($validated['tindakan'] as $key => $tindakan) {
            // Periksa jika ada tanggal penyelesaian yang valid untuk tindakan ini
            $tglPenyelesaian = isset($validated['tgl_penyelesaian'][$key]) ? $validated['tgl_penyelesaian'][$key] : null;

            // Cek apakah tgl_penyelesaian melebihi target_penyelesaian
            if (!is_null($tglPenyelesaian) && $tglPenyelesaian > $targetPenyelesaian) {
                return redirect()->back()->withErrors([
                    'tgl_penyelesaian' => "Tanggal penyelesaian untuk tindakan ke-" . ($key + 1) . " tidak boleh melebihi target penyelesaian ($targetPenyelesaian)."
                ]);
            }

            if (!empty($tindakan) && !empty($validated['pihak'][$key]) && !empty($validated['targetpic'][$key])) {
                // Jika tindakan sudah ada, update
                if (isset($existingTindakan[$key])) {
                    $existingTindakan[$key]->update([
                        'nama_tindakan' => $tindakan,
                        'targetpic' => $validated['targetpic'][$key],
                        'pihak' => $validated['pihak'][$key],
                        'tgl_penyelesaian' => $tglPenyelesaian
                    ]);
                } else {
                    // Jika tidak ada, buat tindakan baru
                    Tindakan::create([
                        'id_riskregister' => $riskregister->id,
                        'nama_tindakan' => $tindakan,
                        'targetpic' => $validated['targetpic'][$key],
                        'pihak' => $validated['pihak'][$key],
                        'tgl_penyelesaian' => $tglPenyelesaian
                    ]);
                }
            }
        }

        return redirect()->route('riskregister.tablerisk', ['id' => $request->input('id_divisi')])
            ->with('success', 'Data berhasil diperbarui!');
    }


    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>TRANSFORMATION || PDCA MANAGEMENT SYSTEM</title>
    <meta content="" name="description">
    <meta content="" name="keywords">


  <!-- Favicons -->
  <link href="{{ asset('admin/img/TML Logo.jpg') }}" rel="icon">
  <link href="{{ asset('admin/img/TML Logo.jpg') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Vendor CSS Files -->
  <link href="{{ asset('admin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

{{-- HEADER --}}
  <header id="header" class="header fixed-top d-flex align-items-center" style="background: linear-gradient(90deg, #87ceeb, #98FB98);">
    <div class="d-flex align-items-center justify-content-between">
        <a href="/" class="logo d-flex align-items-center">
            <span class="d-none d-lg-block" style="color: white; font-size: 1.5rem; font-weight: 700; margin-left: 10px; text-transform: uppercase; letter-spacing: 1px; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">Tata Metal Lestari</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn text-light fs-3"></i>
    </div>

<nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="" data-bs-toggle="dropdown">
                <img src="{{ asset('admin/img/TML3LOGO.png') }}" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; border: 2px solid #fff;">
                <span class="d-none d-md-block dropdown-toggle ps-2 text-dark">{{ Auth::user()->nama_user }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                <li class="dropdown-header">
                    <h6>Email: {{ Auth::user()->email }}</h6>
                    <span>Role: {{ Auth::user()->role }}</span>
                </li>

                <li><hr class="dropdown-divider">

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="/password">
                          <i class="ri-lock-password-fill"></i>
                          <span>Change Password</span>
                        </a>
                    </li>
                </li>

                <li><hr class="dropdown-divider">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="/logout">
                          <i class="bi bi-box-arrow-right"></i>
                          <span>Sign Out</span>
                        </a>
                      </li>
                </li>

          <li>
                <!-- Tambahkan item lainnya di sini jika diperlukan -->
            </ul>
        </li>
    </ul>
</nav>

</header>


<div class="container">
    <h1 class="card-title">Matriks Risiko: <br>{{ $resiko_nama }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><strong>Tingkatan = {{ $tingkatan }}</strong></h5>
            <p class="card-text"><strong>Probability = {{ $severity }}</strong></p>
            <p class="card-text"><strong>Severity = {{ $probability }}</strong></p>
            <p class="card-text"><strong>Probability x Severity = {{ $riskscore }}</strong></p>
            {{-- <p class="card-text"><strong>Nilai Actual Risk: {{$actual}}%</strong></p> --}}
        </div>
    </div>

    <h4><strong>MATRIKS BEFORE</strong></h4>
    <table class="table table-bordered text-center">
        <!-- Tabel matriks pertama -->
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Severity / Keparahan
                    <p>{{$kategori}}
                </th>
                <th colspan="5">Probability / Dampak (Likelihood)</th>
            </tr>
            <tr>
                <th>1 (Sangat Jarang Terjadi)</th>
                <th>2 (Jarang Terjadi)</th>
                <th>3 (Dapat Terjadi)</th>
                <th>4 (Sering Terjadi)</th>
                <th>5 (Selalu Terjadi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matriks_used as $i => $row) <!-- Menggunakan matriks_used -->
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    @if(isset($deskripsiSeverity[$i]))
                        {{ $deskripsiSeverity[$i] }}
                    @else
                        None
                    @endif
                </td>
                @foreach($row as $j => $value)
                <td style="background-color: {{ $colors_used[$i][$j] }}; color: black;"> <!-- Menggunakan colors_used -->
                    @if(($i + 1) == $severity && ($j + 1) == $probability)
                        <div class="d-flex align-items-center">
                            <div class="spinner-grow text-info" role="status" style="width: 1.5rem; height: 1.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">{{ $value }}</span>
                        </div>
                    @else
                        {{ $value }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <br>
    <hr>
    <br>
    <br>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><strong>ACTUAL RISK</strong></h5>
            <p class="card-text"><strong>Probability = {{ $severityrisk }}</strong></p>
            <p class="card-text"><strong>Severity = {{ $probabilityrisk }}</strong></p>
            <p class="card-text"><strong>Probability x Severity = {{ $riskscorerisk }}</strong></p>
            <p class="card-text"><strong>Nilai Actual Risk: {{$actual}}%</strong></p>
        </div>
    </div>

    <h4><strong>MATRIKS AFTER</strong></h4>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Severity / Keparahan
                    <p>{{$kategori}}
                </th>
                <th colspan="5">Probability / Dampak (Likelihood)</th>
            </tr>
            <tr>
                <th>1 (Sangat Jarang Terjadi)</th>
                <th>2 (Jarang Terjadi)</th>
                <th>3 (Dapat Terjadi)</th>
                <th>4 (Sering Terjadi)</th>
                <th>5 (Selalu Terjadi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matriks_used as $i => $row) <!-- Menggunakan matriksnew untuk matriks kedua -->
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    @if(isset($deskripsiSeverity[$i]))
                        {{ $deskripsiSeverity[$i] }}
                    @else
                        None
                    @endif
                </td>
                @foreach($row as $j => $value)
                <td style="background-color: {{ $colors_used[$i][$j] }}; color: black;">
                    @if(($i + 1) == $severityrisk && ($j + 1) == $probabilityrisk)
                        <div class="d-flex align-items-center">
                            <div class="spinner-grow text-warning" role="status" style="width: 1.5rem; height: 1.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">{{ $value }}</span>
                        </div>
                    @else
                        {{ $value }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

<a class="btn btn-danger" href="{{ route('riskregister.tablerisk', $samee) }}" title="Back">
    <i class="ri-arrow-go-back-line"></i>
</a>

    <a class="btn btn-warning" href="{{ route('resiko.edit', ['id' => $same]) }}" title="Back">
        <i class="bx bx-edit"></i>
    </a>
</div>

<footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>PT. TATA METAL LESTARI</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="">TATA METAL LESTARI PRODUCTION</a>
    </div>
  </footer>
</html>

