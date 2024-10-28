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
        <label for="kriteria" class="col-sm-2 col-form-label"><strong>Kriteria</strong></label>
        <div class="col-sm-4">
            <select name="kriteria" class="form-control" required>
                <option value="">--Pilih Kriteria--</option>
                <option value="Unsur keuangan / Kerugian">Unsur Keuangan / Kerugian</option>
                <option value="Safety & Health">Safety & Health</option>
                <option value="Enviromental (lingkungan)">Enviromental (lingkungan)</option>
                <option value="Reputasi">Reputasi</option>
                <option value="Financial">Financial</option>
                <option value="Operational">Operational</option>
                <option value="Kinerja">Kinerja</option>
            </select>
        </div>
    </div>

    <!-- Probability -->
    <div class="row mb-3">
        <label for="probability" class="col-sm-2 col-form-label"><strong>Probability / Dampak (1-5)</strong></label>
        <div class="col-sm-4 d-flex align-items-center">
            <input type="number" class="form-control" placeholder="Masukkan Nilai Probability" name="probability" id="probability" min="1" max="5" oninput="calculateTingkatan()" required>
        </div>
        <div class="col-sm-5">
            <p class="form-text"><strong>1. Sangat jarang terjadi | 2. Jarang terjadi | 3. Dapat Terjadi | 4. Sering terjadi | 5. Selalu terjadi</strong></p>
        </div>
    </div>

    <!-- Severity -->
    <div class="row mb-3">
        <label for="severity" class="col-sm-2 col-form-label"><strong>Severity / Keparahan (Check Tool Box)</strong></label>
        <div class="col-sm-4 d-flex align-items-center">
            <input type="number" class="form-control" placeholder="Masukkan Nilai Severity" name="severity" id="severity" min="1" max="5" oninput="calculateTingkatan()" required>
            <button type="button" class="btn btn-info btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#fullscreenModal">
                <i class="bx bxs-bar-chart-square"></i>
            </button>
        </div>
    </div>


    <div class="row mb-3">
        <label for="tingkatan" class="col-sm-2 col-form-label"><strong>Tingkatan</strong></label>
        <div class="col-sm-4">
            <input type="text" placeholder="Nilai Otomatis"class="form-control" name="tingkatan" id="tingkatan" required>
        </div>
    </div>

    <hr>

    <!-- Bagian untuk mengisi Tindakan, Pihak, Target, dan PIC -->
    <div id="inputContainer">
        <div class="row mb-3">
            <label for="inputPihak" class="col-sm-2 col-form-label"><strong>Pihak yang Berkepentingan</strong></label>
            <div class="col-sm-7">
                <textarea placeholder="Masukkan Departemen" name="pihak[]" class="form-control" rows="3" required></textarea>
            </div>
        </div>

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
        <label for="inputTarget" class="col-sm-2 col-form-label"><strong>Target Penyelesaian</strong></label>
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
            <label for="inputPihak" class="col-sm-2 col-form-label"><strong>Pihak yang Berkepentingan</strong></label>
            <div class="col-sm-7">
                <textarea name="pihak[]" class="form-control" placeholder="Masukkan Pihak Berkepentingan" rows="3" required></textarea>
            </div>
        </div>
    <div class="row mb-3">
        <label for="inputTindakan" class="col-sm-2 col-form-label"><strong>Tindakan Lanjut</strong></label>
        <div class="col-sm-7">
            <textarea placeholder="Masukkan Departemen" name="nama_tindakan[]" class="form-control" placeholder="Masukkan Tindakan Lanjut" rows="3" required></textarea>
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
