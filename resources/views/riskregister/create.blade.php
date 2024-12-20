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

<style>
    /* CSS untuk memaksimalkan ruang dan menampilkan deskripsi di bawah nilai */
#severity option {
    white-space: normal;  /* Memungkinkan teks untuk membungkus ke baris baru */
    word-wrap: break-word; /* Membungkus kata jika panjangnya melebihi lebar dropdown */
}
#dropdownMenuSeverityButton {
    min-width: 200px; /* Menentukan lebar minimal agar teks bisa lebih leluasa */
    font-size: 0.9rem;
}
</style>

<form action="{{ route('riskregister.store') }}" method="POST">
    @csrf
    <input type="hidden" name="id_divisi" value="{{ $enchan }}" required>

    <!-- Bagian untuk mengisi Issue -->
    <div class="row mb-3">
        <label for="inputIssue" class="col-sm-2 col-form-label" ><strong>Issue*</strong></label>
        <div class="col-sm-7">
            <textarea name="issue" class="form-control" rows="3" placeholder="Masukkan Issue" required></textarea>
        </div>
    </div>

    <br>

    <div class="row mb-3">
        <label for="inex" class="col-sm-2 col-form-label"><strong>I/E*</strong></label>
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
    <label class="col-sm-2 col-form-label"><strong>Pihak Berkepentingan*</strong></label>
    <div class="col-sm-10">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownDivisiAkses" data-bs-toggle="dropdown" aria-expanded="false">
                Pilih Pihak Berkepentingan
            </button>
            <ul class="dropdown-menu checkbox-group" aria-labelledby="dropdownDivisiAkses" style="max-height: 200px; overflow-y: auto;">
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
                            <label class="form-check-label" for="divisi{{ $d->id }}">{{ $d->nama_divisi }}</label>
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
    document.addEventListener('DOMContentLoaded', function () {
    const dropdownButton = document.getElementById('dropdownDivisiAkses');
    const checkboxes = document.querySelectorAll('.checkbox-group input[type="checkbox"]');
    const otherCheckbox = document.getElementById('otherCheckbox');
    const otherInput = document.getElementById('pihakOther');
    const otherInputContainer = document.getElementById('otherInputContainer');
    const selectAllCheckbox = document.getElementById('select-all');

    // Toggle the input field for 'Other'
    otherCheckbox.addEventListener('change', function () {
        if (this.checked) {
            otherInputContainer.style.display = 'block'; // Show the input field
        } else {
            otherInputContainer.style.display = 'none'; // Hide the input field
            otherInput.value = ''; // Clear the input if unchecked
        }
        updateDropdown();
    });

    // Handle 'Select All' functionality
    selectAllCheckbox.addEventListener('change', function () {
        checkboxes.forEach(checkbox => {
            if (checkbox !== selectAllCheckbox) {
                checkbox.checked = this.checked; // Check/uncheck all
            }
        });
        updateDropdown();
    });

    // Update dropdown text when checkboxes change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDropdown);
    });

    // Update dropdown text when "Other" input changes
    otherInput.addEventListener('input', updateDropdown);

    function updateDropdown() {
        const selectedValues = [];

        // Collect checked checkboxes except 'Select All' and 'Other'
        checkboxes.forEach(checkbox => {
            if (checkbox.checked && checkbox !== selectAllCheckbox && checkbox !== otherCheckbox) {
                const label = document.querySelector(`label[for="${checkbox.id}"]`);
                if (label) {
                    selectedValues.push(label.textContent.trim());
                }
            }
        });

        // Add 'Other' input value if applicable
        if (otherCheckbox.checked && otherInput.value.trim() !== '') {
            selectedValues.push(otherInput.value.trim());
        }

        // Update the dropdown button text
        dropdownButton.textContent = selectedValues.length > 0
            ? selectedValues.join(', ') // Join selected items with a comma
            : 'Pilih Pihak Berkepentingan'; // Default text
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
<!-- Severity Dropdown -->
<div class="row mb-3">
    <label for="severity" class="col-sm-2 col-form-label"><strong>Severity / Dampak*</strong></label>
    <div class="col-sm-10">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="dropdownMenuSeverityButton" data-bs-toggle="dropdown" aria-expanded="false">
                Pilih Severity
            </button>
            <ul class="dropdown-menu dropdown-menu-severity p-3 w-100" aria-labelledby="dropdownMenuSeverityButton" style="max-height: 200px; overflow-y: auto;">
                @foreach ($severityOptions as $group)
                    <div class="mb-2">
                        <strong>{{ $group['nama_kriteria'] }}</strong>
                        <div>
                            @foreach ($group['options'] as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="severity[]" id="severity-{{ $option['value'] }}" value="{{ $option['value'] }}" data-id="{{ $option['kriteria_id'] }}" data-nama_kriteria="{{ $option['desc'] }}" data-desc="{{ $option['desc'] }}">

                                    <label style="font-size: 0.9rem;" class="form-check-label" for="severity-{{ $option['value'] }}">{{ Str::limit($option['desc'], 200) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </ul>
        </div>
    </div>
</div>


<ul class="dropdown-menu dropdown-menu-severity p-3 w-100" aria-labelledby="dropdownMenuSeverityButton" style="max-height: 200px; overflow-y: auto;">
    @foreach ($severityOptions as $group)
    <div class="mb-2">
        <strong>{{ $group['nama_kriteria'] }}</strong>
        <div>
            @foreach ($group['options'] as $option)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="severity" id="severity-{{ $option['value'] }}" value="{{ $option['value'] }}" data-nama_kriteria="{{ $group['nama_kriteria'] }}" data-desc="{{ $option['desc'] }}">
                    <label style="font-size: 0.9rem;" class="form-check-label" for="severity-{{ $option['value'] }}">{{ Str::limit($option['desc'], 200) }}</label>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
</ul>
<input type="hidden" name="kriteria" id="kriteriaHidden">

<!-- Probability Dropdown -->
<div class="row mb-3">
    <label for="probability" class="col-sm-2 col-form-label"><strong>Probability / Kemungkinan*</strong></label>
    <div class="col-sm-4">
        <select class="form-select" name="probability" id="probability" onchange="calculateTingkatan();">
            <option value="">--Silahkan Pilih Probability--</option>
            <option value="1" {{ old('probability') == 1 ? 'selected' : '' }}>1. Sangat jarang terjadi</option>
            <option value="2" {{ old('probability') == 2 ? 'selected' : '' }}>2. Jarang terjadi</option>
            <option value="3" {{ old('probability') == 3 ? 'selected' : '' }}>3. Dapat Terjadi</option>
            <option value="4" {{ old('probability') == 4 ? 'selected' : '' }}>4. Sering terjadi</option>
            <option value="5" {{ old('probability') == 5 ? 'selected' : '' }}>5. Selalu terjadi</option>
        </select>
    </div>
</div>

<!-- Tingkatan Display -->
<div class="row mb-3">
    <label for="tingkatan" class="col-sm-2 col-form-label"><strong>Tingkatan</strong></label>
    <div class="col-sm-4">
        <input type="text" placeholder="Nilai Otomatis" class="form-control" readonly name="tingkatan" id="tingkatan">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const severityCheckboxes = document.querySelectorAll('.dropdown-menu-severity input[type="checkbox"]');
    const dropdownSeverityButton = document.getElementById('dropdownMenuSeverityButton');
    const kriteriaHidden = document.getElementById('kriteriaHidden');
    const tingkatanInput = document.getElementById('tingkatan'); // Ensure you have an input with id "tingkatan"
    const probabilityDropdown = document.getElementById('probability'); // Ensure you have a dropdown with id "probability"
    function updateKriteria() {
    const selectedCheckbox = Array.from(severityCheckboxes).find(checkbox => checkbox.checked);

    if (selectedCheckbox) {
        // Hanya satu checkbox yang boleh dipilih, jadi deselect semua checkbox lainnya
        severityCheckboxes.forEach(checkbox => {
            if (checkbox !== selectedCheckbox) {
                checkbox.checked = false; // Hapus centang
            }
        });

        // Ambil ID Kriteria dari data-id
        const kriteriaId = selectedCheckbox.dataset.id; // ID Kriteria dari data-id

        // Update input hidden
        kriteriaHidden.value = kriteriaId;

        // Perbarui tombol dropdown
        dropdownSeverityButton.innerHTML = `Terpilih: <br>${selectedCheckbox.dataset.desc}`;
    } else {
        // Jika tidak ada checkbox yang dipilih, reset semuanya
        kriteriaHidden.value = ''; // Kosongkan jika tidak ada pilihan
        dropdownSeverityButton.textContent = 'Pilih Severity'; // Reset label dropdown
    }
}


    // Perbarui kriteria saat checkbox dipilih atau diubah
    severityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateKriteria);
    });

    // Fungsi untuk menghitung tingkatan berdasarkan severity dan probability
    function calculateTingkatan() {
        const probability = parseInt(probabilityDropdown.value, 10); // Ambil nilai probability
        const severityValues = Array.from(severityCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => parseInt(checkbox.value)); // Ambil nilai severity dari value

        let tingkatan = ''; // Default kosong

        if (probability && severityValues.length > 0) {
            // Hitung nilai severity terbesar
            const maxSeverity = Math.max(...severityValues);
            const score = probability * maxSeverity;

            // Tentukan tingkatan berdasarkan score
            if (score >= 1 && score <= 2) {
                tingkatan = 'LOW';
            } else if (score >= 3 && score <= 4) {
                tingkatan = 'MEDIUM';
            } else if (score >= 5 && score <= 25) {
                tingkatan = 'HIGH';
            }
        }

        // Update input tingkatan
        tingkatanInput.value = tingkatan;
    }

    // Event listener untuk setiap checkbox severity
    severityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateKriteria(); // Perbarui input kriteria
            calculateTingkatan(); // Hitung tingkatan
        });
    });

    // Event listener untuk dropdown probability
    probabilityDropdown.addEventListener('change', calculateTingkatan);
});
    </script>


    <hr>
    <h5 class="card-title">Tindakan Lanjut </h5>

    <!-- Bagian untuk mengisi Tindakan, Pihak, Target, dan PIC -->
    <div id="inputContainer">
        <!-- Input sections yang bisa ditambahkan oleh user -->
        <div class="dynamic-inputs">
            <div class="row mb-3">
                <label for="inputTindakan" class="col-sm-2 col-form-label"><strong>Tindakan Lanjut*</strong></label>
                <div class="col-sm-7">
                    <textarea placeholder="Masukkan Tindakan" name="nama_tindakan[]" class="form-control" placeholder="Masukkan Tindakan Lanjut" rows="3" required></textarea>
                </div>
            </div>
            <div class="row mb-3">
                <label for="inputTarget" class="col-sm-2 col-form-label"><strong>Target Tanggal Tindakan Lanjut*</strong></label>
                <div class="col-sm-7">
                    <input type="date" name="tgl_penyelesaian[]" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="inputPIC" class="col-sm-2 col-form-label"><strong>PIC*</strong></label>
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
    <label for="inputIssue" class="col-sm-2 col-form-label"><strong>Before</strong></label>
    <div class="col-sm-7">
        <textarea name="before" placeholder="Masukkan Deskripsi Saat Ini atau sebelum tindakan lanjut (mitigasi) dilakukan" class="form-control" rows="3"></textarea>
    </div>
</div>

<div class="row mb-3">
    <label for="inputTarget" class="col-sm-2 col-form-label"><strong>Target Tanggal Besar Penyelesaian Issue*</strong></label>
    <div class="col-sm-7">
        <input type="date" name="target_penyelesaian" class="form-control" required>
    </div>
</div>

<!-- Tombol Submit -->
<div class="text-center mt-3">
    <button type="submit" class="btn btn-primary">Submit</button>
</div>

<div class="dynamic-inputs"></div> <!-- This is where the dynamic sections will be added -->
<script>
    document.getElementById('addMore').addEventListener('click', function() {
        // Membuat elemen input baru
        var newInputSection = `
            <div class="dynamic-input mb-3">
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
                <hr>
                <button type="button" class="btn btn-danger btn-sm deleteSection" style="margin-top: 10px;">Delete</button>
            </div>
        `;
        // Menambahkan input baru setelah input yang ada
        var dynamicContainer = document.querySelector('.dynamic-inputs');
        dynamicContainer.insertAdjacentHTML('beforeend', newInputSection);
    });

    // Event listener untuk tombol delete
    document.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('deleteSection')) {
            var sectionToRemove = event.target.closest('.dynamic-input');
            sectionToRemove.remove();
        }
});
</script>
@endsection
