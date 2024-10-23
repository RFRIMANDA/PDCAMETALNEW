@extends('layouts.main')

@section('content')
<div class="container">
    <h5 class="card-title">Detail Resiko Issue  {{ $riskregister->issue }}</h5>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('resiko.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_riskregister" value="{{ $id }}">

        <div class="row mb-3">
            <label for="nama_resiko" class="col-sm-2 col-form-label"><strong>Nama Resiko</strong></label>
            <div class="col-sm-10">
                <textarea name="nama_resiko" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label for="kriteria" class="col-sm-2 col-form-label"><strong>Kriteria</strong></label>
            <select class="col-sm-4" name="kriteria" id="kriteria">
                <option value="">--Pilih Kriteria--</option>
                <option value="Unsur keuangan / Kerugian">Unsur keuangan / Kerugian</option>
                <option value="Safety & Health">Safety & Health</option>
                <option value="Enviromental (lingkungan)">Enviromental (lingkungan)</option>
            </select>
        </div>
        <hr>

        <div class="row mb-3">
            <label for="severity" class="col-sm-2 col-form-label"><strong>Probability / Kemungkinan (1-5)</strong></label>
            <div class="col-sm-4">
                <input type="number" class="form-control" name="severity" id="severity" min="1" max="5" oninput="calculateTingkatan()">
            </div>
            <div class="col-sm-5">
                <p class="form-text"><strong>1. Sangat jarang terjadi | 2. Jarang terjadi | 3. Dapat Terjadi | 4. Sering terjadi | 5. Selalu terjadi</strong></p>
            </div>
        </div>
        <hr>

        <div class="row mb-3">
            <label for="probability" class="col-sm-2 col-form-label"><strong>Severity / Dampak (1-5)</strong></label>
            <div class="col-sm-4">
                <input type="number" class="form-control" name="probability" id="probability" min="1" max="5" oninput="calculateTingkatan()">
            </div>
            <div class="col-sm-5">
                <p class="form-text"><strong>1. Sangat jarang terjadi | 2. Jarang terjadi | 3. Dapat Terjadi | 4. Sering terjadi | 5. Selalu terjadi</strong></p>
            </div>
        </div>
        <hr>

        <div class="row mb-3">
            <label for="tingkatan" class="col-sm-2 col-form-label"><strong>Tingkatan</strong></label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="tingkatan" id="tingkatan" readonly>
            </div>
        </div>

        <div class="row mb-3">
            <label for="status" class="col-sm-2 col-form-label"><strong>Status</strong></label>
            <select class="col-sm-4" name="status" id="status">
                <option value="">--Pilih Status--</option>
                <option value="OPEN">OPEN</option>
                <option value="ON PROGRES">ON PROGRES</option>
                <option value="CLOSE">CLOSE</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save
            <i class="ri-save-3-fill"></i>
        </button>
    </form>
</div>

<script>
    const severityDescriptions = {
        "Unsur keuangan / Kerugian": [
            "Gangguan kecil. Tidak terlalu berpengaruh terhadap reputasi perusahaan.",
            "Gangguan sedang dan mendapatkan perhatian dari manajemen / corporate / regional.",
            "Gangguan serius, mendapatkan perhatian dari masyarakat / LSM / media lokal.",
            "Gangguan sangat serius, berdampak kepada operasional perusahaan dan pemerintah.",
            "Bencana. Terhentinya operasional perusahaan, mengakibatkan hilangnya kepercayaan."
        ],
        "Safety & Health": [
            "Hampir tidak ada risiko cedera, berdampak kecil.",
            "Cedera/sakit sedang, perlu perawatan medis.",
            "Cedera/sakit yang memerlukan perawatan khusus sehingga mengakibatkan kehilangan waktu kerja.",
            "Meninggal atau cacat fisik permanen karena pekerjaan.",
            "Meninggal lebih dari satu orang atau cedera/cacat parah."
        ],
        "Enviromental (lingkungan)": [
            "Dampak polusi kecil dan dapat diperbaiki dalam waktu kurang dari 1 minggu.",
            "Polusi berdampak pada tempat kerja tetapi tidak ada keluhan serius.",
            "Polusi berdampak keluar dan memerlukan tindakan perbaikan sedang dalam 3-6 bulan.",
            "Polusi berat, memerlukan perbaikan dalam waktu 6-12 bulan.",
            "Polusi besar-besaran yang memerlukan perbaikan lebih dari 1 tahun."
        ],
        "Reputasi": [
            "Kejadian / Incident negatif, hanya diketahui internal organisasi tidak ada dampak kepada stakehoder.",
            "Kejadian / Incident negatif, mulai diketahui / berdampak kepada`stakeholders.",
            "Pemberitaan negatif, yang menurukan kepercayaan Stakeholders.",
            "Kemunduran/hilang kepercayaan Stakeholders.",
        ],
        "Financial": [
            "Kerugian / biaya yang harus dikeluarkan ≤ Rp. 1.000.000,-.",
            "Kerugian / biaya yang harus dikeluarkan Rp.1.000.000 >x≥ Rp. 19.000.000,-.",
            "Kerugian / biaya yang harus dikeluarkan Rp.19.000.000 >x≥ Rp. 70.000.000,-.",
            "Kerugian / biaya yang harus dikeluarkan x>Rp. 70.000.000,-.",
        ],

    };

    function updateSeverityDescription() {
        const selectedKriteria = document.getElementById('kriteria').value;
        const severity = document.getElementById('severity').value;
        const descriptionElement = document.getElementById('severity-description');

        if (selectedKriteria && severity) {
            descriptionElement.textContent = severityDescriptions[selectedKriteria][severity - 1];
        } else {
            descriptionElement.textContent = '';
        }
    }

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
            // } else if (score >= 15 && score <= 25) {
            //     tingkatan = 'EXTREME';
            }
        }

        document.getElementById('tingkatan').value = tingkatan;
        updateSeverityDescription();
    }

    document.getElementById('kriteria').addEventListener('change', updateSeverityDescription);
    document.getElementById('severity').addEventListener('input', updateSeverityDescription);
</script>

@endsection
