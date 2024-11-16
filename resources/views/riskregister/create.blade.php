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
                            <input class="form-check-input" type="checkbox" name="pihak[]" value="{{ $d->nama_divisi }}" id="divisi{{ $d->id }}"
                                @if(is_array(old('pihak', $selectedDivisi ?? [])) && in_array($d->nama_divisi, old('pihak', $selectedDivisi ?? []))) checked @endif>
                            <label class="form-check-label" for="divisi{{ $d->id }}">
                                {{ $d->nama_divisi }}
                            </label>
                        </div>
                    </li>
                @endforeach
                <li>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="otherCheckbox">
                        <label class="form-check-label" for="otherCheckbox">Other</label>
                    </div>
                    <div class="mt-2" id="otherInputContainer" style="display: none;">
                        <input type="text" class="form-control" name="pihak_other" id="pihakOther" placeholder="Masukkan Pihak Berkepentingan Lainnya">
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Toggle the input field for 'Other' when checkbox is checked
    document.getElementById('otherCheckbox').addEventListener('change', function() {
        const otherInputContainer = document.getElementById('otherInputContainer');
        if (this.checked) {
            otherInputContainer.style.display = 'block'; // Show the input field
        } else {
            otherInputContainer.style.display = 'none'; // Hide the input field
        }
    });
</script>



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
<!-- Kriteria Dropdown -->
<div class="row mb-3">
    <label for="kriteria" class="col-sm-2 col-form-label"><strong>Kriteria</strong></label>
    <div class="col-sm-4">
        <select name="kriteria" class="form-control" id="kriteriaSelect" required onchange="updateSeverityDropdown()">
            <option value="">--Pilih Kriteria--</option>
            @foreach($kriteria as $k)
                <option value="{{ $k->nama_kriteria }}">{{ $k->nama_kriteria }}</option>
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
            <!-- Opsi severity akan diisi secara dinamis di sini -->
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
// Simulasi data kriteria (ambil ini dari database menggunakan Laravel)
const kriteriaData = @json($kriteria);

// Function untuk mengupdate dropdown severity berdasarkan kriteria yang dipilih
function updateSeverityDropdown() {
    const selectedKriteria = document.getElementById('kriteriaSelect').value;
    const severitySelect = document.getElementById('severity');
    severitySelect.innerHTML = '<option value="">--Pilih Severity--</option>';  // Clear previous options

    if (selectedKriteria) {
        // Filter kriteria berdasarkan nama yang dipilih
        const filteredKriteria = kriteriaData.filter(k => k.nama_kriteria === selectedKriteria);

        filteredKriteria.forEach(kriteria => {
            // Memisahkan nilai_kriteria dan desc_kriteria menjadi array
            const nilaiKriteriaString = kriteria.nilai_kriteria.replace(/[\[\]"]+/g, ''); // Menghapus [ ] dan tanda kutip ganda
            const descKriteriaString = kriteria.desc_kriteria.replace(/[\[\]"]+/g, ''); // Menghapus [ ] dan tanda kutip ganda

            const nilaiKriteriaArray = nilaiKriteriaString.split(','); // Memisahkan dengan koma
            const descKriteriaArray = descKriteriaString.split(','); // Memisahkan dengan koma

            // Loop melalui setiap pasangan nilai dan deskripsi kriteria
            for (let i = 0; i < nilaiKriteriaArray.length; i++) {
                const option = document.createElement('option');
                option.value = nilaiKriteriaArray[i];
                // Menambahkan deskripsi di bawah nilai
                option.textContent = `${nilaiKriteriaArray[i]}\n${descKriteriaArray[i]}`;
                severitySelect.appendChild(option);
            }
        });
    }
}

// Memastikan dropdown severity diupdate saat kriteria dipilih
document.getElementById('kriteriaSelect').addEventListener('change', updateSeverityDropdown);

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
        <!-- Input sections yang bisa ditambahkan oleh user -->
        <div class="dynamic-inputs">
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
                    <select name="targetpic[]" class="form-select" required>
                        <option value="">Pilih PIC</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->nama_user }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tombol Add More berada di bawah input -->
        <div>
            <button type="button" class="btn btn-secondary" id="addMore">Add More</button>
        </div>

    <hr>

    <div class="row mb-3">
        <label for="inputIssue" class="col-sm-2 col-form-label"  "><strong>Before</strong></label>
        <div class="col-sm-7">
            <textarea name="before" placeholder="Masukkan Deskripsi Saat Ini atau sebelum tindakan lanjut (mitigasi) dilakukan" class="form-control" rows="3" ></textarea>
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
    // Membuat elemen input baru
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
                <select name="targetpic[]" class="form-select" required>
                    <option value="">Pilih PIC</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->nama_user }}</option>
                    @endforeach
                </select>
            </div>
        </div>`;
    // Menambahkan input baru setelah input yang ada
    var dynamicContainer = document.querySelector('.dynamic-inputs');
    dynamicContainer.insertAdjacentHTML('beforeend', newInputSection);
});

</script>

@endsection
