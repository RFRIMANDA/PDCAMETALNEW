@extends('layouts.main')

@section('content')

<h5 class="card-title">Create Risk & Opportunity Register </h5>

<!-- Tambahkan alert untuk menampilkan pesan error -->
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('riskregister.store') }}" method="POST">
    @csrf
    <input type="hidden" name="id_divisi" value="{{ $enchan }}" required>

    <!-- Bagian untuk mengisi Issue -->
    <div class="row mb-3">
        <label for="inputIssue" class="col-sm-2 col-form-label" ><strong>Issue</strong></label>
        <div class="col-sm-7">
            <textarea name="issue" class="form-control" rows="3" placeholder="Masukkan Issue" required></textarea>
        </div>
    </div>
    <br>

    <div class="row mb-3">
        <label for="inex" class="col-sm-2 col-form-label"><strong>I/E</strong></label>
        <div class="col-sm-4">
            <select name="inex" class="form-control" required>
                <option value="">--Silahkan Pilih--</option>
                <option value="I">INTERNAL</option>
                <option value="E">EXTERNAL</option>
            </select>
        </div>
    </div>
    <br>

    <!-- Default Accordion -->
    <div class="accordion" id="accordionExample">
        <!-- Bagian Risiko -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingResiko">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseResiko" aria-expanded="true" aria-controls="collapseResiko">
                    <strong>Risiko</strong>
                </button>
            </h2>
            <div id="collapseResiko" class="accordion-collapse collapse show" aria-labelledby="headingResiko" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <div class="row mb-3">
                        <label for="inputRisiko" class="col-sm-2 col-form-label"><strong>Risiko</strong></label>
                        <div class="col-sm-7">
                            <textarea id="inputRisiko" name="nama_resiko" class="form-control" placeholder="Masukkan Risiko" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Peluang -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingPeluang">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePeluang" aria-expanded="false" aria-controls="collapsePeluang">
                    <strong>Peluang</strong>
                </button>
            </h2>
            <div id="collapsePeluang" class="accordion-collapse collapse" aria-labelledby="headingPeluang" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <div class="row mb-3">
                        <label for="inputPeluang" class="col-sm-2 col-form-label"><strong>Peluang</strong></label>
                        <div class="col-sm-7">
                            <textarea id="inputPeluang" name="peluang" class="form-control" placeholder="Masukkan Peluang" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        const inputRisiko = document.getElementById('inputRisiko');
        const inputPeluang = document.getElementById('inputPeluang');

        // Event listener untuk risiko
        inputRisiko.addEventListener('input', function() {
            if (inputRisiko.value.trim() !== '') {
                inputPeluang.disabled = true; // Nonaktifkan input peluang
            } else {
                inputPeluang.disabled = false; // Aktifkan kembali jika risiko kosong
            }
        });

        // Event listener untuk peluang
        inputPeluang.addEventListener('input', function() {
            if (inputPeluang.value.trim() !== '') {
                inputRisiko.disabled = true; // Nonaktifkan input risiko
            } else {
                inputRisiko.disabled = false; // Aktifkan kembali jika peluang kosong
            }
        });
    </script>

  <br>
  <div class="row mb-3">
    <label class="col-sm-2 col-form-label"><strong>Pihak Berkepentingan:</strong></label>
    <div class="col-sm-10">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownDivisiAkses" data-bs-toggle="dropdown" aria-expanded="false">
                Pilih Pihak Berkepentingan
            </button>
            <ul class="dropdown-menu checkbox-group" aria-labelledby="dropdownDivisiAkses">
                <li>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                        <label class="form-check-label" for="select-all">Pilih Semua</label>
                    </div>
                </li>
                @foreach ($divisi as $d)
                    <li>
                        <div class="form-check">
                            <!-- Ubah value menjadi nama_divisi -->
                            <input class="form-check-input" type="checkbox" name="pihak[]" value="{{ $d->nama_divisi }}" id="divisi{{ $d->id }}"
                                @if(is_array(old('pihak', $selectedDivisi ?? [])) && in_array($d->nama_divisi, old('pihak', $selectedDivisi ?? []))) checked @endif>
                            <label class="form-check-label" for="divisi{{ $d->id }}">
                                {{ $d->nama_divisi }}
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>


<!-- JavaScript to handle the 'select all' functionality -->
<script>
    // Handle "Select All" checkbox functionality
    document.getElementById('select-all').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.checkbox-group input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked; // Set all checkboxes to match 'Select All' status
        });
    });
</script>

    <br>
    <div class="row mb-3">
        <label for="kriteria" class="col-sm-2 col-form-label"><strong>Kriteria</strong></label>
        <div class="col-sm-4">
            <select name="kriteria" class="form-control" id="kriteriaSelect" required>
                <option value="">--Pilih Kriteria--</option>
                @foreach($kriteria as $k)
                    <option value="{{ $k->nama_kriteria }}">{{ $k->nama_kriteria }}</option>
                @endforeach
            </select>
        </div>
    </div>


<!-- Severity -->
<div class="row mb-3">
    <label for="severity" class="col-sm-2 col-form-label"><strong>Severity / Keparahan (Check Tool Box)</strong></label>
    <div class="col-sm-4 d-flex align-items-center">
        <input type="number" class="form-control" placeholder="Masukkan Nilai Severity" name="severity" id="severity" min="1" max="5" oninput="calculateTingkatan()" required>
        <button type="button" class="btn btn-info btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#severityModal">
            <i class="bx bxs-bar-chart-square"></i>
        </button>
    </div>
</div>

<!-- Modal untuk Severity -->
<div class="modal fade" id="severityModal" tabindex="-1" aria-labelledby="severityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="severityModalLabel">Pilih Nilai Kriteria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Nilai Kriteria</th>
                            <th scope="col">Deskripsi Kriteria</th>
                        </tr>
                    </thead>
                    <tbody id="criteriaTableBody">
                        <!-- Data akan diisi menggunakan JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Simulasi data kriteria (ambil ini dari database menggunakan Laravel)
    const kriteriaData = @json($kriteria);

// Function untuk menampilkan kriteria berdasarkan nama kriteria yang dipilih
function showKriteriaOptions() {
    const selectedKriteria = document.getElementById('kriteriaSelect').value;
    const tableBody = document.getElementById('criteriaTableBody');
    tableBody.innerHTML = '';  // Clear previous data

    if (selectedKriteria) {
        // Filter kriteria berdasarkan nama yang dipilih
        const filteredKriteria = kriteriaData.filter(k => k.nama_kriteria === selectedKriteria);

        filteredKriteria.forEach(kriteria => {
            // Membersihkan simbol [] dan "" pada nilai_kriteria dan desc_kriteria
            const nilaiKriteriaString = kriteria.nilai_kriteria.replace(/[\[\]"]+/g, ''); // Menghapus [ ] dan tanda kutip ganda
            const descKriteriaString = kriteria.desc_kriteria.replace(/[\[\]"]+/g, ''); // Menghapus [ ] dan tanda kutip ganda

            // Memisahkan nilai_kriteria dan desc_kriteria menjadi array
            const nilaiKriteriaArray = nilaiKriteriaString.split(','); // Memisahkan dengan koma
            const descKriteriaArray = descKriteriaString.split(','); // Memisahkan dengan koma

            // Debug: Cek apakah array sudah terpisah dengan benar
            console.log('Nilai Kriteria Array:', nilaiKriteriaArray);
            console.log('Deskripsi Kriteria Array:', descKriteriaArray);

            // Loop melalui setiap pasangan nilai dan deskripsi kriteria
            for (let i = 0; i < nilaiKriteriaArray.length; i++) {
                // Menambahkan baris ke tabel modal
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><button class="btn btn-link" onclick="selectKriteria('${nilaiKriteriaArray[i]}', event)">${nilaiKriteriaArray[i]}</button></td>
                    <td>${descKriteriaArray[i]}</td>
                `;
                tableBody.appendChild(row);
            }
        });
    }
}

// Function untuk memilih nilai kriteria dan mengisi input severity
function selectKriteria(nilai, event) {
    event.preventDefault();  // Mencegah aksi default browser

    // Isi nilai ke input severity
    document.getElementById('severity').value = nilai;

    // Menutup modal setelah memilih nilai
    // $('#severityModal').modal('hide');
}

// Event listener untuk memanggil showKriteriaOptions() saat kriteria berubah
document.getElementById('kriteriaSelect').addEventListener('change', showKriteriaOptions);

// Pastikan data kriteria tampil ketika modal dibuka
$('#severityModal').on('show.bs.modal', function () {
    showKriteriaOptions();  // Menampilkan opsi kriteria saat modal muncul
});


</script>

 <!-- Probability -->
 <div class="row mb-3">
    <label for="probability" class="col-sm-2 col-form-label"><strong>Probability / Dampak</strong></label>
    <div class="col-sm-4">
        <select class="form-select" name="probability" id="probability" required onchange="calculateTingkatan()">
            <option value="">--Silahkan Pilih Probability--</option>
            <option value="1" {{ old('probability') == 1 ? 'selected' : '' }}>1. Sangat jarang terjadi</option>
            <option value="2" {{ old('probability') == 2 ? 'selected' : '' }}>2. Jarang terjadi</option>
            <option value="3" {{ old('probability') == 3 ? 'selected' : '' }}>3. Dapat Terjadi</option>
            <option value="4" {{ old('probability') == 4 ? 'selected' : '' }}>4. Sering terjadi</option>
            <option value="5" {{ old('probability') == 5 ? 'selected' : '' }}>5. Selalu terjadi</option>
        </select>
    </div>
</div>

    <div class="row mb-3">
        <label for="tingkatan" class="col-sm-2 col-form-label"><strong>Tingkatan</strong></label>
        <div class="col-sm-4">
            <input type="text" placeholder="Nilai Otomatis"class="form-control" readonly name="tingkatan" id="tingkatan" required>
        </div>
    </div>

    <hr>
    <h5 class="card-title">Tindakan Lanjut </h5>

    <!-- Bagian untuk mengisi Tindakan, Pihak, Target, dan PIC -->
    <div id="inputContainer">

        <div class="row mb-3">
            <label for="inputTindakan" class="col-sm-2 col-form-label"><strong>Tindakan Lanjut</strong></label>
            <div class="col-sm-7">
                <textarea name="nama_tindakan[]" placeholder="Masukkan Tindakan Lanjut" class="form-control" rows="3" required></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label for="inputTarget" class="col-sm-2 col-form-label"><strong>Target Tanggal Tindakan Lanjut</strong></label>
            <div class="col-sm-7">
                <input type="date" name="tgl_penyelesaian[]" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="inputPIC" class="col-sm-2 col-form-label"><strong>PIC</strong></label>
            <div class="col-sm-7">
                <textarea name="targetpic[]" placeholder="Masukkan Nama PIC" class="form-control" rows="3" required></textarea>
            </div>
        </div>
    </div>


    <!-- Tombol Add More -->
    <div>
        <button type="button" class="btn btn-secondary" id="addMore">Add More</button>
    </div>

    <hr>

    <div class="row mb-3">
        <label for="inputIssue" class="col-sm-2 col-form-label"  "><strong>Before</strong></label>
        <div class="col-sm-7">
            <textarea name="before" placeholder="Masukkan Deskripsi Saat Ini" class="form-control" rows="3" ></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <label for="inputTarget" class="col-sm-2 col-form-label"><strong>Target Tanggal Besar Penyelesaian Issue</strong></label>
        <div class="col-sm-7">
            <input type="date" name="target_penyelesaian" class="form-control" required>
        </div>
    </div>

    {{-- status --}}
    <div class="row mb-3" style="display: none;">
        <label for="inputStatus" class="col-sm-2 col-form-label"><strong>Status</strong></label>
        <div class="col-sm-7">
            <select type="hidden" name="status" class="form-control" required>
                {{-- <option value="">-- Pilih Status --</option> --}}
                <option value="OPEN">OPEN</option>
                {{-- <option value="ON PROGRESS">ON PROGRESS</option>
                <option value="CLOSE">CLOSE</option> --}}
            </select>
        </div>
    </div>

    <!-- Tombol Submit -->
    <div class="text-center mt-3">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

<!-- Full Screen Modal -->
<div class="modal fade" id="fullscreenModal" tabindex="-1" aria-labelledby="fullscreenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="fullscreenModalLabel">Severity / Keparahan</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Dynamic content will be injected here based on selected kriteria -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Script untuk menambah input "Add More" -->
<script>
function calculateTingkatan() {
    var probability = document.getElementById('probability').value;
    var severity = document.getElementById('severity').value;
    var tingkatan = '';

    if (probability && severity) {
        var score = probability * severity;

        if (score >= 1 && score <= 2) {
            tingkatan = 'LOW';
        } else if (score >= 3 && score <= 4) {
            tingkatan = 'MEDIUM';
        } else if (score >= 5 && score <= 25) {
            tingkatan = 'HIGH';
        }
    }

    document.getElementById('tingkatan').value = tingkatan;
}

document.getElementById('addMore').addEventListener('click', function() {
    var newInputSection = `
    <hr>

    <div class="row mb-3">
        <label for="inputTindakan" class="col-sm-2 col-form-label"><strong>Tindakan Lanjut</strong></label>
        <div class="col-sm-7">
            <textarea placeholder="Masukkan Tindakan" name="nama_tindakan[]" class="form-control" placeholder="Masukkan Tindakan Lanjut" rows="3" required></textarea>
        </div>
    </div>
    <div class="row mb-3">
        <label for="inputTarget" class="col-sm-2 col-form-label"><strong>Target Tanggal Tindakan Lanjut</strong></label>
        <div class="col-sm-7">
            <input type="date" name="tgl_penyelesaian[]" class="form-control" required>
        </div>
    </div>
    <div class="row mb-3">
        <label for="inputPIC" class="col-sm-2 col-form-label"><strong>PIC</strong></label>
        <div class="col-sm-7">
            <textarea name="targetpic[]" class="form-control" placeholder="Masukkan Target PIC" rows="3" required></textarea>
        </div>
    </div>`;
    document.getElementById('inputContainer').insertAdjacentHTML('beforeend', newInputSection);
});
</script>

@endsection


public function edit($id)
{
    // Fetch the Riskregister entry by ID
    $riskregister = Riskregister::findOrFail($id);

    // Fetch all divisi and kriteria
    $divisi = Divisi::all();

    // Fetch the actions related to the Riskregister
    $tindakanList = Tindakan::where('id_riskregister', $id)->get();

    // Get the selected divisi for the pihak field (split by comma)
    $selectedDivisi = $riskregister->pihak ? explode(',', $riskregister->pihak) : [];
    $users = User::where('divisi', $id)->get();

    // Return the edit view with all necessary data
    return view('riskregister.edit', compact('riskregister', 'divisi', 'tindakanList', 'selectedDivisi','users'));
}

public function update(Request $request, $id)
{
    try {
        // Validate the input data
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
            'pihak' => 'nullable|array',
            'pihak.*' => 'exists:divisi,id',
            'targetpic' => 'required|array',
            'targetpic.*' => 'required|string',
            'tgl_penyelesaian' => 'required|array',
            'tgl_penyelesaian.*' => 'required|date',
            'target_penyelesaian' => 'required|date',
            'status' => 'nullable|in:OPEN,ON PROGRES,CLOSE',
            'before' => 'nullable|string',
            'pihak_other' => 'nullable|string'
        ]);

        // Check if both 'nama_resiko' and 'peluang' are filled
        if ($request->filled('nama_resiko') && $request->filled('peluang')) {
            return back()->withErrors(['error' => 'Anda hanya bisa mengisi salah satu dari Risiko atau Peluang, tidak keduanya.']);
        }

        // Calculate the 'tingkatan' based on 'probability' and 'severity'
        $tingkatan = $this->calculateTingkatan($validated['probability'], $validated['severity']);

        // Add 'pihak_other' to the array if provided
        if ($request->has('pihak_other') && $request->filled('pihak_other')) {
            $validated['pihak'][] = $validated['pihak_other'];
        }

        // Find the Riskregister by ID
        $riskregister = Riskregister::findOrFail($id);

        // Update the Riskregister entry
        $riskregister->update([
            'id_divisi' => $validated['id_divisi'],
            'issue' => $validated['issue'],
            'inex' => $validated['inex'],
            'pihak' => $validated['pihak'] ? implode(',', $validated['pihak']) : null,
            'target_penyelesaian' => $validated['target_penyelesaian'],
            'peluang' => $validated['peluang'] ?? null
        ]);

        // Update or create the related 'Resiko' entry
        $status = $validated['status'] ?? 'OPEN';
        $resiko = Resiko::where('id_riskregister', $riskregister->id)->first();
        if ($resiko) {
            $resiko->update([
                'nama_resiko' => $validated['nama_resiko'] ?? null,
                'kriteria' => $validated['kriteria'],
                'probability' => $validated['probability'],
                'severity' => $validated['severity'],
                'tingkatan' => $tingkatan,
                'status' => $status,
                'before' => $validated['before'] ?? null
            ]);
        }

        // Update or create related 'Tindakan' entries
        foreach ($validated['nama_tindakan'] as $key => $nama_tindakan) {
            $tglPenyelesaian = $validated['tgl_penyelesaian'][$key] ?? null;

            if (!empty($nama_tindakan) && !empty($validated['targetpic'][$key])) {
                $tindakan = Tindakan::updateOrCreate(
                    ['id_riskregister' => $riskregister->id, 'id' => $key],
                    [
                        'nama_tindakan' => $nama_tindakan,
                        'targetpic' => $validated['targetpic'][$key],
                        'tgl_penyelesaian' => $tglPenyelesaian
                    ]
                );

                // Ensure there's a corresponding 'Realisasi' entry
                Realisasi::updateOrCreate(
                    ['id_riskregister' => $riskregister->id, 'id_tindakan' => $tindakan->id],
                    [
                        'nama_realisasi' => null,
                        'presentase' => 0,
                        'status' => 'ON PROGRES'
                    ]
                );
            }
        }

        // Update the status of 'Resiko' based on the 'Realisasi' status
        $isAllRealisasiComplete = Realisasi::where('id_riskregister', $riskregister->id)->where('status', 'CLOSE')->count() === Realisasi::where('id_riskregister', $riskregister->id)->count();

        if ($isAllRealisasiComplete) {
            $resiko->status = 'CLOSE';
        } else {
            $resiko->status = 'ON PROGRES';
        }
        $resiko->save();

        return redirect()->route('riskregister.tablerisk', ['id' => $validated['id_divisi']])
            ->with('success', 'Data berhasil diperbarui!');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Data gagal diperbarui: ' . $e->getMessage()]);
    }
}
