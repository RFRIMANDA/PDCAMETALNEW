@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Edit Matriks Resiko & Actual Risk</h1>
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
            <label for="nama_resiko" class="col-sm-2 col-form-label"><strong>Resiko</strong></label>
            <div class="col-sm-10">
                <textarea name="nama_resiko" class="form-control" rows="3">{{ old('nama_resiko', $resiko->nama_resiko) }}</textarea>
            </div>
        </div>
        <div class="row mb-3">
            <label for="severity" class="col-sm-2 col-form-label"><strong>Severity</strong></label>
            <div class="col-sm-10">
                <select class="form-select" name="severity" id="severity">
                    <option style="font-size: 15px;" value="">--Pilih Severity--</option>
                    @if(old('kriteria', $resiko->kriteria))
                        @foreach($kriteria as $k)
                            @if($k->nama_kriteria == old('kriteria', $resiko->kriteria))
                                @php
                                    // Get the severity values and descriptions for the selected kriteria
                                    $nilaiKriteriaArray = explode(',', str_replace(['[', ']', '"'], '', $k->nilai_kriteria));
                                    $descKriteriaArray = explode(',', str_replace(['[', ']', '"'], '', $k->desc_kriteria));
                                @endphp
                                @foreach($nilaiKriteriaArray as $index => $nilai)
                                    <option style="font-size: 15px;" value="{{ $nilai }}" {{ old('severity', $resiko->severity) == $nilai ? 'selected' : '' }}>
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

    <!-- Probability -->
    <div class="row mb-3">
        <label for="probability" class="col-sm-2 col-form-label"><strong>Probability / Dampak</strong></label>
        <div class="col-sm-4">
            <select class="form-select" name="probability" id="probability" onchange="calculateTingkatan()">
                <option value="">--Silahkan Pilih Probability--</option>
                <option value="1" {{ old('probability', $resiko->probability) == 1 ? 'selected' : '' }}>1. Sangat jarang terjadi</option>
                <option value="2" {{ old('probability', $resiko->probability) == 2 ? 'selected' : '' }}>2. Jarang terjadi</option>
                <option value="3" {{ old('probability', $resiko->probability) == 3 ? 'selected' : '' }}>3. Dapat Terjadi</option>
                <option value="4" {{ old('probability', $resiko->probability) == 4 ? 'selected' : '' }}>4. Sering terjadi</option>
                <option value="5" {{ old('probability', $resiko->probability) == 5 ? 'selected' : '' }}>5. Selalu terjadi</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="kriteria" class="col-sm-2 col-form-label"><strong>Kriteria</strong></label>
        <div class="col-sm-4">
            <select class="form-select" name="kriteria" id="kriteriaInput" onchange="updateSeverityDropdown()">
                <option value="">--Pilih Kriteria--</option>
                @foreach($kriteria as $k)
                    <option value="{{ $k->nama_kriteria }}" {{ old('kriteria', $resiko->kriteria) == $k->nama_kriteria ? 'selected' : '' }}>
                        {{ $k->nama_kriteria }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


<!-- Tingkatan -->
<div class="row mb-3">
    <label for="tingkatan" class="col-sm-2 col-form-label"><strong>Tingkatan</strong></label>
    <div class="col-sm-4">
        <input type="text" class="form-control" name="tingkatan" id="tingkatan" value="{{ old('tingkatan', $resiko->tingkatan) }}" readonly>
    </div>
</div>

<hr>
<hr>

<h1 class="card-title">ACTUAL RISK</h1>

<!-- Severity Dropdown for Actual Risk -->
<div class="row mb-3">
    <label for="severityrisk" class="col-sm-2 col-form-label"><strong>Severity</strong></label>
    <div class="col-sm-4">
        <select class="form-select" name="severityrisk" id="severityrisk">
            <option value="">--Pilih Severity--</option>
            @if(old('kriteria', $resiko->kriteria))
                @foreach($kriteria as $k)
                    @if($k->nama_kriteria == old('kriteria', $resiko->kriteria))
                        @php
                            // Get the severity values and descriptions for the selected kriteria
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

<script>
    // Function to update Severity Dropdown for Actual Risk dynamically based on selected kriteria
    function updateSeverityDropdown() {
    const selectedKriteria = document.getElementById('kriteriaInput').value;
    const severitySelect = document.getElementById('severityrisk');

    // Clear existing options
    severitySelect.innerHTML = '<option value="">--Pilih Severity--</option>';

    // Find the corresponding kriteria data based on selected kriteria
    const kriteriaData = @json($kriteria);
    const selectedKriteriaData = kriteriaData.find(k => k.nama_kriteria === selectedKriteria);

    if (selectedKriteriaData) {
        const nilaiKriteriaArray = selectedKriteriaData.nilai_kriteria.replace(/[\[\]"]+/g, '').split(',');
        const descKriteriaArray = selectedKriteriaData.desc_kriteria.replace(/[\[\]"]+/g, '').split(',');

        // Add severity options dynamically
        nilaiKriteriaArray.forEach((nilai, index) => {
            const option = document.createElement('option');
            option.value = nilai;
            option.textContent = `${nilai} - ${descKriteriaArray[index] || ''}`;
            option.selected = nilai === '{{ old('severityrisk', $resiko->severityrisk) }}'; // Maintain old selected value
            severitySelect.appendChild(option);
        });
    }
}

// Event listener to update severityrisk dropdown when kriteria is selected
document.getElementById('kriteriaInput').addEventListener('change', updateSeverityDropdown);

// Initialize severity dropdown when page loads
document.addEventListener('DOMContentLoaded', updateSeverityDropdown);

</script>

<!-- Probability Risk -->
<div class="row mb-3">
    <label for="probabilityrisk" class="col-sm-2 col-form-label"><strong>Probability / Dampak</strong></label>
    <div class="col-sm-4">
        <select name="probabilityrisk" class="form-control" id="probabilityrisk" onchange="calculateRisk()">
            <option value="">--Silahkan pilih Probability--</option>
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
    <label for="risk" class="col-sm-2 col-form-label"><strong>Actual Risk</strong></label>
    <div class="col-sm-4">
        <input type="text" class="form-control" name="risk" id="risk"  value="{{ old('risk', $resiko->risk) }}" readonly>
    </div>
</div>


<!-- Status hanya muncul jika user memiliki role 'admin' -->
@if(auth()->user()->role == 'admin')
    <!-- Status -->
    <div class="row mb-3" style="display: none">
        <label for="status" class="col-sm-2 col-form-label"><strong>Status</strong></label>
        <div class="col-sm-4">
            <select name="status" class="form-control">
                <option value="">--Pilih Status--</option>
                <option value="OPEN" {{ old('status', $resiko->status) == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                <option value="ON PROGRES" {{ old('status', $resiko->status) == 'ON PROGRES' ? 'selected' : '' }}>ON PROGRES</option>
                <option value="CLOSE" {{ old('status', $resiko->status) == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
            </select>
        </div>
    </div>
@endif

<a class="btn btn-danger" href="{{ route('riskregister.tablerisk', ['id' => $three]) }}" title="Back">
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
