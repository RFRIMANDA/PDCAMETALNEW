@extends('layouts.main')

@section('content')
<div class="container">
    <h2 class="card-title">Edit Risk Register</h2>

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
            <div class="mb-3">
                <label for="issue" class="form-label"><strong>Issue</strong></label>
                <textarea name="issue" id="issue" class="form-control">{{ old('issue', $riskregister->issue) }}</textarea>
            </div>

            <!-- Ext/Int -->
            <div class="mb-3">
                <label for="inex" class="form-label"><strong>Internal/External</strong></label>
                <select name="inex" id="inex" class="form-select">
                    <option value="I" {{ old('inex', $riskregister->inex) == 'I' ? 'selected' : '' }}>Internal</option>
                    <option value="E" {{ old('inex', $riskregister->inex) == 'E' ? 'selected' : '' }}>External</option>
                </select>
            </div>

            <!-- Opportunity -->
            <div class="mb-3">
                <label for="peluang" class="form-label"><strong>Peluang</strong></label>
                <textarea name="peluang" id="peluang" class="form-control">{{ old('peluang', $riskregister->peluang) }}</textarea>
            </div>

            <!-- Interested Parties -->
            <div class="mb-3">
                <label for="pihak" class="form-label"><strong>Pihak Yang Berkepentingan</strong></label>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownPihak" data-bs-toggle="dropdown" aria-expanded="false">
                        --Pilih Pihak Yang Berkepentingan--
                    </button>
                    <ul class="dropdown-menu checkbox-group" aria-labelledby="dropdownPihak">
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all-pihak">
                                <label class="form-check-label" for="select-all-pihak">Select All</label>
                            </div>
                        </li>
                        @foreach ($divisi as $d)
                            <li>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="pihak[]" value="{{ $d->id }}" id="divisi{{ $d->id }}"
                                    @if(in_array($d->nama_divisi, old('pihak', $selectedDivisi))) checked @endif>
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
                                <input type="text" class="form-control" name="pihak_other" id="pihakOther" placeholder="Enter Other Interested Parties">
                            </div>
                        </li>
                    </ul>
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

        <!-- JavaScript for "select all" functionality -->
        <script>
            document.getElementById('select-all-pihak').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.checkbox-group input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.id !== 'select-all-pihak') { // Mengecualikan checkbox utama
                    checkbox.checked = this.checked;
                }
            });
        });

        </script>


        <!-- Target Penyelesaian -->
        <div class="row mb-3">
            <label for="target_penyelesaian" class="col-sm-2 col-form-label"><strong>Target Penyelesaian</strong></label>
            <div class="col-sm-7">
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
                        <textarea name="before[{{ $resiko->id }}]" id="before_{{ $resiko->id }}" class="form-control">{{ old('resiko.' . $resiko->id, $resiko->before) }}</textarea>
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
                <hr>
            </div>
            @endforeach
        </div>

        <!-- Tombol untuk menambah lebih banyak -->
        <button type="button" class="btn btn-secondary" id="addMore">Add More</button>

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
    `;
    document.getElementById('inputContainer').insertAdjacentHTML('beforeend', newInputSection);
});

</script>

@endsection
