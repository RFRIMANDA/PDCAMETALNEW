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
                <textarea name="nama_resiko" class="form-control" rows="3"  >{{ old('nama_resiko', $resiko->nama_resiko) }}</textarea>
            </div>
        </div>

        <!-- Kriteria -->
        <div class="row mb-3">
            <label for="kriteria" class="col-sm-2 col-form-label"><strong>Kriteria</strong></label>
            <div class="col-sm-4">
                <select name="kriteria" class="form-control" >
                    <option value="Unsur keuangan / Kerugian" {{ $resiko->kriteria == 'Unsur keuangan / Kerugian' ? 'selected' : '' }}>Unsur keuangan / Kerugian</option>
                    <option value="Safety & Health" {{ $resiko->kriteria == 'Safety & Health' ? 'selected' : '' }}>Safety & Health</option>
                    <option value="Enviromental (lingkungan)" {{ $resiko->kriteria == 'Enviromental (lingkungan)' ? 'selected' : '' }}>Enviromental (lingkungan)</option>
                    <option value="Reputasi" {{ $resiko->kriteria == 'Reputasi' ? 'selected' : '' }}>Reputasi</option>
                    <option value="Financial" {{ $resiko->kriteria == 'Financial' ? 'selected' : '' }}>Financial</option>
                    <option value="Operational" {{ $resiko->kriteria == 'Operational' ? 'selected' : '' }}>Operational</option>
                    <option value="Kinerja" {{ $resiko->kriteria == 'Kinerja' ? 'selected' : '' }}>Kinerja</option>
                </select>
            </div>
        </div>

        <!-- Probability -->
        <div class="row mb-3">
            <label for="probability" class="col-sm-2 col-form-label"><strong>Probability / Dampak (1-5)</strong></label>
            <div class="col-sm-4 d-flex align-items-center">
                <input type="number" class="form-control" name="probability" id="probability" min="1" max="5" oninput="calculateTingkatan()" value="{{ old('probability', $resiko->probability) }}" >
            </div>
            <div class="col-sm-5">
                <p class="form-text"><strong>1. Sangat jarang terjadi | 2. Jarang terjadi | 3. Dapat Terjadi | 4. Sering terjadi | 5. Selalu terjadi</strong></p>
            </div>
        </div>

        <!-- Severity -->
        <div class="row mb-3">
            <label for="severity" class="col-sm-2 col-form-label"><strong>Severity / Keparahan</strong></label>
            <div class="col-sm-4 d-flex align-items-center justify-content-between">
                <input type="number" class="form-control me-2" name="severity" id="severity" min="1" max="5" oninput="calculateTingkatan()" value="{{ old('severity', $resiko->severity) }}">
                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#fullscreenModal">
                    <i class="bx bxs-bar-chart-square"></i>
                </button>
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

        <!-- Probability Risk -->
        <div class="row mb-3">
            <label for="probabilityrisk" class="col-sm-2 col-form-label"><strong>Probability / Dampak (1-5)</strong></label>
            <div class="col-sm-4 d-flex align-items-center">
                <input type="number" placeholder="Masukan Nilai Probability" class="form-control" name="probabilityrisk" id="probabilityrisk" min="1" max="5" oninput="calculateRisk()" value="{{ old('probabilityrisk', $resiko->probabilityrisk) }}" >
            </div>
            <div class="col-sm-5">
                <p class="form-text"><strong>1. Sangat jarang terjadi | 2. Jarang terjadi | 3. Dapat Terjadi | 4. Sering terjadi | 5. Selalu terjadi</strong></p>
            </div>
        </div>

        <!-- Severity Risk -->
        <div class="row mb-3">
            <label for="severityrisk" class="col-sm-2 col-form-label"><strong>Severity / Keparahan (Check Tool Box)</strong></label>
            <div class="col-sm-4 d-flex align-items-center justify-content-between">
                <input type="number" placeholder="Masukan Nilai Severity" class="form-control me-2" name="severityrisk" id="severityrisk" min="1" max="5" oninput="calculateRisk()" value="{{ old('severityrisk', $resiko->severityrisk) }}">
                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#fullscreenModal">
                    <i class="bx bxs-bar-chart-square"></i>
                </button>
            </div>
        </div>

        <!-- Risk -->
        <div class="row mb-3">
            <label for="risk" class="col-sm-2 col-form-label"><strong>Tingkatan</strong></label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="risk" id="risk"  value="{{ old('risk', $resiko->risk) }}" readonly>
            </div>
        </div>

        <!-- Before -->
        {{-- <div class="row mb-3">
            <label for="before" class="col-sm-2 col-form-label"><strong>Before</strong></label>
            <div class="col-sm-10">
                <textarea name="before" class="form-control" rows="3">{{ old('before', $resiko->before) }}</textarea>
            </div>
        </div> --}}

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

<!-<!-- Full Screen Modal -->
<div class="modal fade" id="fullscreenModal" tabindex="-1" aria-labelledby="fullscreenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="fullscreenModalLabel">Severity / Keparahan</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6><strong>Unsur Keuangan / Kerugian</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nilai.</th>
                            <th>Dampak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Gangguan kedalam kecil. Tidak terlalu berpengaruh terhadap reputasi perusahaan.</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Gangguan kedalam sedang dan mendapatkan perhatian dari manajemen / corporate / regional.</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Gangguan kedalam serius, mendapatkan perhatian dari masyarakat / LSM / media lokal, dapat merugikan bisnis, kemungkinan dapat mengakibatkan tuntutan hukum.</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Gangguan sangat serius, berdampak kepada operasional perusahaan dan penjualan. Menarik perhatian media nasional. Proses hukum hampir pasti.</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Bencana. Terhentinya operasional perusahaan, mengakibatkan jatuhnya harga saham. Menarik perhatian media nasional & internasional. Proses hukum yang pasti, tuntutan hukum terhadap Direktur.</td>
                        </tr>
                    </tbody>
                </table>
                <hr>

                <h6><strong>Safety & Health</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nilai</th>
                            <th>Dampak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Hampir tidak ada risiko cedera, berdampak kecil pada K3, memerlukan P3K tetapi pekerja dapat bekerja. No lost time injury.</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Cidera/sakit sedang, perlu perawatan medis. Pekerja dapat bekerja kembali tetapi terjadi penurunan performa.</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Cidera/sakit yang memerlukan perawatan khusus sehingga mengakibatkan kehilangan waktu kerja.</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Meninggal atau cacat fisik permanen karena pekerjaan.</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Meninggal lebih dari satu orang atau cedera cacat permanen lebih satu orang akibat dari pekerjaan.</td>
                        </tr>
                    </tbody>
                </table>
                <hr>

                <h6><strong>Enviromental (Lingkungan)</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nilai</th>
                            <th>Dampak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Dampak polusi tertahan disekitar atau polusi kecil atau dampak tidak berarti, memerlukan perbaikan/pekerjaan perbaikan kecil dan dapat dipulihkan dengan cepat (&lt; 1 Minggu).</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Polusi dengan dampak pada tempat kerja tetapi tidak ada komplain dari pihak luar, memerlukan pekerjaan perbaikan sedang dan dapat dipulihkan dalam waktu 7 hari - 3 bulan.</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Polusi berarti atau berpengaruh keluar atau mengakibatkan komplain, memerlukan pekerjaan perbaikan sedang dan dapat dipulihkan dalam waktu 3 - 6 bulan.</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Polusi berarti, berpengaruh keluar dan mengakibatkan komplain, memerlukan pekerjaan perbaikan besar dan dapat dipulihkan dalam waktu 6 bulan - 1 tahun.</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Polusi besar-besaran baik kedalam maupun keluar, ada tuntutan dari pihak luar serta membutuhkan pekerjaan perbaikan besar dan dapat dipulihkan lebih dari 1 tahun.</td>
                        </tr>
                        <hr>
                    </tbody>
                </table>
                <!-- Reputasi -->
                <h6><strong>Reputasi</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nilai</th>
                            <th>Dampak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Kejadian / Incident negatif, hanya diketahui internal organisasi tidak ada dampak kepada stakehoder.</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Kejadian / Incident negatif, mulai diketahui / berdampak kepada stakeholders.</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Pemberitaan negatif, yang menurunkan kepercayaan Stakeholders.</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Kemunduran/hilang kepercayaan Stakeholders.</td>
                        </tr>
                    </tbody>
                </table>
                <hr>

                <!-- Financial -->
                <h6><strong>Financial</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nilai</th>
                            <th>Dampak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Kerugian / biaya yang harus dikeluarkan ≤ Rp. 1.000.000,-.</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Kerugian / biaya yang harus dikeluarkan Rp.1.000.000 >x≥ Rp. 19.000.000,-.</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Kerugian / biaya yang harus dikeluarkan Rp.19.000.000 >x≥ Rp. 70.000.000,-.</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Kerugian / biaya yang harus dikeluarkan x>Rp. 70.000.000,-.</td>
                        </tr>
                    </tbody>
                </table>
                <hr>

                <!-- Operational -->
                <h6><strong>Operational</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nilai</th>
                            <th>Dampak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Menimbulkan gangguan kecil pada fungsi sistem terhadap proses bisnis namun tidak signifikan.</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Menimbulkan gangguan 25 - 50 % fungsi operasional atau hanya berdampak pada 1 unit bisnis.</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Menimbulkan gangguan 50 - 75 % fungsi operasional atau berdampak pada 2 unit bisnis terkait.</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Menimbulkan kegagalan > 75 % proses operasional atau berdampak pada sebagian besar unit bisnis.</td>
                        </tr>
                    </tbody>
                </table>
                <hr>

                <!-- Kinerja -->
                <h6><strong>Kinerja</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nilai</th>
                            <th>Dampak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Menimbulkan penundaan aktivitas (proses tidak dapat dijalankan) ≤ 1 jam.</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Menimbulkan penundaan aktivitas (proses tidak dapat dijalankan) 1< x≤ 3 jam.</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Menimbulkan penundaan aktivitas (proses tidak dapat dijalankan) 3< x≤ 5 jam.</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Menimbulkan penundaan aktivitas (proses tidak dapat dijalankan) >5 Jam (Uraian kerja tidak efektif dan efisien).</td>
                        </tr>
                    </tbody>
                </table>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Full Screen Modal -->

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
