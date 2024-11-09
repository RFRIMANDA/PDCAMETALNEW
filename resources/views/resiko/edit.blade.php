@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Data Peta Resiko & Actual Risk</h1>
    <hr>

   <!-- Menampilkan Nilai Actual jika tersedia -->
   @if(session('nilai_actual'))
   <div class="alert alert-success">
       <strong>Nilai Actual dari Realisasi:</strong> {{ session('nilai_actual') }}%
   </div>
   @endif

    <!-- Form untuk edit -->
    <form action="{{ route('resiko.update', $resiko->id) }}" method="POST">
        @csrf

        <!-- Nama Resiko -->
        <div class="row mb-3">
            <label for="nama_resiko" class="col-sm-2 col-form-label"><strong>Nama Resiko</strong></label>
            <div class="col-sm-10">
                <textarea name="nama_resiko" class="form-control" rows="3">{{ old('nama_resiko', $resiko->nama_resiko) }}</textarea>
            </div>
        </div>

        <!-- Kriteria Dropdown -->
        <div class="row mb-3">
            <label for="kriteria" class="col-sm-2 col-form-label"><strong>Kriteria</strong></label>
            <div class="col-sm-4">
                <select name="kriteria" class="form-control" id="kriteriaSelect" required onchange="updateSeverityDropdown()">
                    <option value="">--Pilih Kriteria--</option>
                    @foreach($kriteria as $k)
                        <option value="{{ $k->nama_kriteria }}" {{ old('kriteria', $resiko->kriteria) == $k->nama_kriteria ? 'selected' : '' }}>
                            {{ $k->nama_kriteria }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Severity Dropdown -->
        <div class="row mb-3">
            <label for="severity" class="col-sm-2 col-form-label"><strong>Severity</strong></label>
            <div class="col-sm-4">
                <select class="form-select" name="severity" id="severity" required>
                    <option value="">--Pilih Severity--</option>
                    <!-- Isi opsi severity sesuai kriteria yang terpilih -->
                    @if(old('kriteria', $resiko->kriteria))
                        @foreach($kriteria as $k)
                            @if($k->nama_kriteria == old('kriteria', $resiko->kriteria))
                                @php
                                    // Mendapatkan nilai dan deskripsi untuk opsi severity
                                    $nilaiKriteriaArray = explode(',', str_replace(['[', ']', '"'], '', $k->nilai_kriteria));
                                    $descKriteriaArray = explode(',', str_replace(['[', ']', '"'], '', $k->desc_kriteria));
                                @endphp
                                @foreach($nilaiKriteriaArray as $index => $nilai)
                                    <option value="{{ $nilai }}" {{ old('severity', $resiko->severity) == $nilai ? 'selected' : '' }}>
                                        {{ $nilai }} - {{ $descKriteriaArray[$index] ?? '' }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>


        <style>
            /* CSS untuk memaksimalkan ruang dan menampilkan deskripsi di bawah nilai */
        #severity option {
            white-space: normal;  /* Memungkinkan teks untuk membungkus ke baris baru */
            word-wrap: break-word; /* Membungkus kata jika panjangnya melebihi lebar dropdown */
        }

        </style>
        <script>
            // Fungsi untuk memperbarui dropdown severity berdasarkan kriteria yang dipilih
            const kriteriaData = @json($kriteria);

            function updateSeverityDropdown(targetDropdownId) {
                const selectedKriteria = document.getElementById('kriteriaSelect').value;
                const severitySelect = document.getElementById(targetDropdownId);
                severitySelect.innerHTML = '<option value="">--Pilih Severity--</option>';  // Hapus opsi sebelumnya

                if (selectedKriteria) {
                    const filteredKriteria = kriteriaData.filter(k => k.nama_kriteria === selectedKriteria);

                    filteredKriteria.forEach(kriteria => {
                        const nilaiKriteriaArray = kriteria.nilai_kriteria.replace(/[\[\]"]+/g, '').split(',');
                        const descKriteriaArray = kriteria.desc_kriteria.replace(/[\[\]"]+/g, '').split(',');

                        for (let i = 0; i < nilaiKriteriaArray.length; i++) {
                            const option = document.createElement('option');
                            option.value = nilaiKriteriaArray[i];
                            option.textContent = `${nilaiKriteriaArray[i]} - ${descKriteriaArray[i]}`;
                            severitySelect.appendChild(option);
                        }
                    });
                }
            }

            document.getElementById('kriteriaSelect').addEventListener('change', function () {
                updateSeverityDropdown('severity');
                updateSeverityDropdown('severityrisk');
            });
        </script>

        <!-- Probability -->
        <div class="row mb-3">
            <label for="probability" class="col-sm-2 col-form-label"><strong>Probability / Dampak</strong></label>
            <div class="col-sm-4">
                <select class="form-select" name="probability" id="probability" required onchange="calculateTingkatan()">
                    <option value="">--Silahkan Pilih Probability--</option>
                    <option value="1" {{ old('probability', $resiko->probability) == 1 ? 'selected' : '' }}>1. Sangat jarang terjadi</option>
                    <option value="2" {{ old('probability', $resiko->probability) == 2 ? 'selected' : '' }}>2. Jarang terjadi</option>
                    <option value="3" {{ old('probability', $resiko->probability) == 3 ? 'selected' : '' }}>3. Dapat Terjadi</option>
                    <option value="4" {{ old('probability', $resiko->probability) == 4 ? 'selected' : '' }}>4. Sering terjadi</option>
                    <option value="5" {{ old('probability', $resiko->probability) == 5 ? 'selected' : '' }}>5. Selalu terjadi</option>
                </select>
            </div>
        </div>

        <!-- Tingkatan -->
        <div class="row mb-3">
            <label for="tingkatan" class="col-sm-2 col-form-label"><strong>Tingkatan</strong></label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="tingkatan" id="tingkatan"  value="{{ old('tingkatan', $resiko->tingkatan) }}" readonly>
            </div>
        </div>

        <hr>
        <hr>

        <h1 class="card-title">ACTUAL RISK</h1>

        <!-- Severity Dropdown for Actual Risk -->
        <div class="row mb-3">
            <label for="severityrisk" class="col-sm-2 col-form-label"><strong>Severity</strong></label>
            <div class="col-sm-4">
                <select class="form-select" name="severityrisk" id="severityrisk" required>
                    <option value="">--Pilih Severity--</option>
                    <!-- Isi opsi severity sesuai kriteria yang terpilih -->
                    @if(old('kriteria', $resiko->kriteria))
                        @foreach($kriteria as $k)
                            @if($k->nama_kriteria == old('kriteria', $resiko->kriteria))
                                @php
                                    // Mendapatkan nilai dan deskripsi untuk opsi severity
                                    $nilaiKriteriaArray = explode(',', str_replace(['[', ']', '"'], '', $k->nilai_kriteria));
                                    $descKriteriaArray = explode(',', str_replace(['[', ']', '"'], '', $k->desc_kriteria));
                                @endphp
                                @foreach($nilaiKriteriaArray as $index => $nilai)
                                    <option value="{{ $nilai }}" {{ old('severityrisk', $resiko->severityrisk) == $nilai ? 'selected' : '' }}>
                                        {{ $nilai }} - {{ $descKriteriaArray[$index] ?? '' }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>


        <!-- Probability Risk -->
        <div class="row mb-3">
            <label for="probabilityrisk" class="col-sm-2 col-form-label"><strong>Probability / Dampak</strong></label>
            <div class="col-sm-4">
                <select name="probabilityrisk" class="form-control" id="probabilityrisk" onchange="calculateRisk()">
                    <option value="" >--Silahkan pilih Severity--</option>
                    <option value="1" {{ old('probabilityrisk', $resiko->probabilityrisk) == 1 ? 'selected' : '' }}>1. Sangat jarang terjadi</option>
                    <option value="2" {{ old('probabilityrisk', $resiko->probabilityrisk) == 2 ? 'selected' : '' }}>2. Jarang terjadi</option>
                    <option value="3" {{ old('probabilityrisk', $resiko->probabilityrisk) == 3 ? 'selected' : '' }}>3. Dapat Terjadi</option>
                    <option value="4" {{ old('probabilityrisk', $resiko->probabilityrisk) == 4 ? 'selected' : '' }}>4. Sering terjadi</option>
                    <option value="5" {{ old('probabilityrisk', $resiko->probabilityrisk) == 5 ? 'selected' : '' }}>5. Selalu terjadi</option>
                </select>
            </div>
        </div>

        <!-- Risk -->
        <div class="row mb-3">
            <label for="risk" class="col-sm-2 col-form-label"><strong>Tingkatan</strong></label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="risk" id="risk"  value="{{ old('risk', $resiko->risk) }}" readonly>
            </div>
        </div>

        {{-- Before --}}
        <div class="row mb-3">
            <label for="after" class="col-sm-2 col-form-label"><strong>Before</strong></label>
            <div class="col-sm-10">
                <textarea name="before" class="form-control" rows="3">{{ old('before', $resiko->before) }}</textarea>
            </div>
        </div>

        <!-- After -->
        <div class="row mb-3">
            <label for="after" class="col-sm-2 col-form-label"><strong>After</strong></label>
            <div class="col-sm-10">
                <textarea name="after" placeholder="Deskripsikan Kondisi Setelah Ditindak Lanjuti" class="form-control" rows="3">{{ old('after', $resiko->after) }}</textarea>
            </div>
        </div>

        <!-- Status hanya muncul jika user memiliki role 'admin' -->
        @if(auth()->user()->role == 'admin')

            <!-- Status -->
            <div class="row mb-3">
                <label for="status" class="col-sm-2 col-form-label"><strong>Status</strong></label>
                <div class="col-sm-4">
                    <select name="status" class="form-control">
                        <option value="">--Pilih Status--</option>
                        <option value="OPEN" {{ $resiko->status == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                        <option value="ON PROGRES" {{ $resiko->status == 'ON PROGRES' ? 'selected' : '' }}>ON PROGRES</option>
                        <option value="CLOSE" {{ $resiko->status == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                    </select>
                </div>
            </div>
        @endif

        <!-- Submit Button -->
        <a href="javascript:history.back()" class="btn btn-danger " title="Kembali">
            <i class="ri-arrow-go-back-line"></i>
        </a>

        <button type="submit" class="btn btn-primary" title="Update">Save
            <i class="ri-save-3-fill"></i>
        </button>
    </form>

</div>

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

function calculateRisk() {
    var probabilityrisk = document.getElementById('probabilityrisk').value;
    var severityrisk = document.getElementById('severityrisk').value;
    var risk = '';

    if (probabilityrisk && severityrisk) {
        var score = probabilityrisk * severityrisk;

        if (score >= 1 && score <= 2) {
            risk = 'LOW';
        } else if (score >= 3 && score <= 4) {
            risk = 'MEDIUM';
        } else if (score >= 5 && score <= 25) {
            risk = 'HIGH';
        }
    }

    document.getElementById('risk').value = risk;
}

</script>
@endsection
