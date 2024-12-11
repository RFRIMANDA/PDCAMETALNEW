@extends('layouts.main')

@section('content')
<div class="container">
    {{-- <h2 class="card-title">Edit Risk Register</h2> --}}

    <!-- Alert untuk menampilkan error -->
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terjadi kesalahan!</strong> Target tanggal tidak boleh melebih target penyelesaian:
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Form untuk edit riskregister -->
    <form action="{{ route('riskregister.update', $riskregister->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Divisi -->
        <input type="hidden" name="id_divisi" value="{{ $riskregister->id_divisi }}">

         <!-- Risk Register Fields -->
         <div class="card p-3 my-3">
            <h4 class="card-title mb-3">Edit Risk & Opportunity Register</h4>

        <!-- Issue -->
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label mt-1"><strong>Issue</strong></label>
            <div class="col-sm-10">
                <textarea name="issue" id="issue" class="form-control">{{ old('issue', $riskregister->issue) }}</textarea>
            </div>
        </div>

        <!-- Ext/Int -->
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label mt-1"><strong>I/E</strong></label>
            <div class="col-sm-10">
                <select name="inex" id="inex" class="form-select">
                    <option value="I" {{ old('inex', $riskregister->inex) == 'I' ? 'selected' : '' }}>Internal</option>
                    <option value="E" {{ old('inex', $riskregister->inex) == 'E' ? 'selected' : '' }}>External</option>
                </select>
            </div>
        </div>

        <!-- Opportunity -->
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label mt-1"><strong>Peluang</strong></label>
            <div class="col-sm-10">
                <textarea placeholder="Masukkan Peluang jika ada" name="peluang" id="peluang" class="form-control">{{ old('peluang', $riskregister->peluang) }}</textarea>
            </div>
        </div>


            <!-- Interested Parties -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label mt-2"><strong>Pihak yang Berkepentingan</strong></label> <!-- Menambahkan margin-top -->
                <div class="col-sm-10">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownDivisiAkses" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(old('pihak') || $selectedDivisi)
                                {{ implode(', ', old('pihak', $selectedDivisi ?? [])) }}
                            @else
                                Pilih Pihak Berkepentingan
                            @endif
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
                                            @if(in_array($d->nama_divisi, old('pihak', $selectedDivisi ?? []))) checked @endif>
                                        <label class="form-check-label" for="divisi{{ $d->id }}">
                                            {{ $d->nama_divisi }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                            <li>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="otherCheckbox"
                                           @if(old('pihak_other') || in_array('Other', old('pihak', []))) checked @endif>
                                    <label class="form-check-label" for="otherCheckbox">Other</label>
                                </div>
                                <div class="mt-2" id="otherInputContainer" style="display: @if(old('pihak_other') || in_array('Other', old('pihak', []))) block @else none @endif;">
                                    <input type="text" class="form-control" name="pihak_other" id="pihakOther" placeholder="Masukkan Pihak Berkepentingan Lainnya"
                                           value="{{ old('pihak_other') }}">
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const dropdownButton = document.getElementById('dropdownDivisiAkses');
                    const checkboxes = document.querySelectorAll('.form-check-input');
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

                    // Update dropdown text when checkboxes change
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', updateDropdown);
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

                    otherInput.addEventListener('input', updateDropdown); // Update on typing in "Other"

                    function updateDropdown() {
                        const selectedValues = [];

                        // Collect checked checkboxes
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
                        if (selectedValues.length > 0) {
                            dropdownButton.textContent = selectedValues.join(', '); // Join values with a comma
                        } else {
                            dropdownButton.textContent = 'Pilih Pihak Berkepentingan'; // Default text
                        }
                    }

                    // Call updateDropdown when the page is loaded to handle old data
                    updateDropdown();
                });
            </script>

        <!-- Target Penyelesaian -->
        <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label mt-2"><strong>Target Penyelesaian</strong></label> <!-- Menambahkan margin-top -->
            <div class="col-sm-10">
                <input type="date" name="target_penyelesaian" id="target_penyelesaian" class="form-control" value="{{ old('target_penyelesaian', $riskregister->target_penyelesaian) }}">
            </div>
        </div>

         <!-- Risk Edit Section -->
         <div class="card p-3 my-3">
            <h4 class="card-title">Edit Resiko</h4>

            <div id="riskInputContainer">
                @foreach($resikoList as $resiko)
                <div class="risk-card border p-3 my-2">
                    <div class="mb-3">
                        <label for="nama_resiko_{{ $resiko->id }}" class="form-label"><strong>Resiko</strong></label>
                        <textarea name="nama_resiko[{{ $resiko->id }}]" id="nama_resiko_{{ $resiko->id }}" class="form-control">{{ old('resiko.' . $resiko->id, $resiko->nama_resiko) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="before_{{ $resiko->id }}" class="form-label"><strong>Before</strong></label>
                        <textarea placeholder="Masukkan kondisi saat ini sebelum mitigasi atau tindakan lanjut dilakukan" name="before[{{ $resiko->id }}]" id="before_{{ $resiko->id }}" class="form-control">{{ old('resiko.' . $resiko->id, $resiko->before) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="after_{{ $resiko->id }}" class="form-label"><strong>After</strong></label>
                        <textarea placeholder="Masukkan setelah mitigasi atau tindakan lanjut dilakukan" name="after[{{ $resiko->id }}]" id="after_{{ $resiko->id }}" class="form-control">{{ old('resiko.' . $resiko->id, $resiko->after) }}</textarea>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <hr>
        <h3 class="card-title">Tindakan Lanjut</h3>

        <div id="inputContainer">
            @foreach($tindakanList as $tindakan)
            <div class="action-block" data-id="{{ $tindakan->id }}">

                <!-- Tindakan -->
                <div class="row mb-3">
                    <label for="tindakan_{{ $tindakan->id }}" class="col-sm-2 col-form-label"><strong>Tindakan</strong></label>
                    <div class="col-sm-7">
                        <textarea name="tindakan[{{ $tindakan->id }}]" id="tindakan_{{ $tindakan->id }}" class="form-control" required>{{ old('tindakan.' . $tindakan->id, $tindakan->nama_tindakan) }}</textarea>
                    </div>
                </div>

                <!-- Target PIC -->
                <div class="row mb-3">
                    <label for="inputPIC" class="col-sm-2 col-form-label"><strong>PIC</strong></label>
                    <div class="col-sm-7">
                        <select name="targetpic[{{ $tindakan->id }}]" class="form-select" required>
                            <option value="">Pilih PIC</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('targetpic.' . $tindakan->id, $tindakan->targetpic) == $user->id ? 'selected' : '' }}>
                                    {{ $user->nama_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Target Tanggal Penyelesaian -->
                <div class="row mb-3">
                    <label for="tgl_penyelesaian_{{ $tindakan->id }}" class="col-sm-2 col-form-label"><strong>Target Tanggal</strong></label>
                    <div class="col-sm-7">
                        <input type="date" name="tgl_penyelesaian[{{ $tindakan->id }}]" id="tgl_penyelesaian_{{ $tindakan->id }}" class="form-control" value="{{ old('tgl_penyelesaian.' . $tindakan->id, $tindakan->tgl_penyelesaian) }}" required>
                    </div>
                </div>

                <!-- Checkbox for Deleting -->
                <div class="row mb-3">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-7">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tindakan_to_delete[{{ $tindakan->id }}]" value="1">
                            <label class="form-check-label" for="tindakan_to_delete_{{ $tindakan->id }}">
                                Hapus Tindakan ini
                            </label>
                        </div>
                    </div>
                </div>

                <hr>
            </div>
            @endforeach
        </div>


        <!-- Tombol untuk menambah lebih banyak -->
        <div class="mt-2 d-flex">
            <button type="button" class="btn btn-secondary me-auto" id="addMore">Add More</button>
        </div>

        <!-- Tombol untuk menyimpan -->
        <div class="mt-3">
            <a href="javascript:history.back()" class="btn btn-danger" title="Kembali">
                <i class="ri-arrow-go-back-line"></i>
            </a>
            <button type="submit" class="btn btn-primary" title="Update">Save
                <i class="ri-save-3-fill"></i>
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('addMore').addEventListener('click', function() {
    var newInputSection = `
        <div class="input-section">
            <hr>
            <div class="row mb-3">
                <label for="inputTindakan" class="col-sm-2 col-form-label"><strong>Tindakan Lanjut</strong></label>
                <div class="col-sm-7">
                    <textarea placeholder="Masukkan Tindakan Lanjut" name="tindakan[]" class="form-control" rows="3" required></textarea>
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
            <!-- Tombol Delete -->
            <div class="mt-2 d-flex">
                <button type="button" class="btn btn-danger btn-sm delete-section ms-auto" title="Hapus">
                    <i class="ri-delete-bin-6-line"></i> Delete
                </button>
            </div>
        </div>
    `;
    document.getElementById('inputContainer').insertAdjacentHTML('beforeend', newInputSection);

    // Tambahkan event listener ke tombol delete baru
    attachDeleteListeners();
});

// Fungsi untuk menambahkan event listener pada tombol delete
function attachDeleteListeners() {
    document.querySelectorAll('.delete-section').forEach(button => {
        button.addEventListener('click', function() {
            button.closest('.input-section').remove(); // Hapus bagian input terkait
        });
    });
}

// Panggil fungsi untuk inisialisasi pertama kali
attachDeleteListeners();

</script>

@endsection
