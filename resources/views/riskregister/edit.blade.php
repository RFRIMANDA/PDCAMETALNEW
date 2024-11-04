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

        <!-- Issue -->
        <div class="row mb-3">
            <label for="issue" class="col-sm-2 col-form-label"><strong>Issue</strong></label>
            <div class="col-sm-7">
                <textarea name="issue" id="issue" class="form-control">{{ old('issue', $riskregister->issue) }}</textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label for="inex" class="col-sm-2 col-form-label"><strong>Ext/Int</strong></label>
            <div class="col-sm-7">
                <select name="inex" id="inex" class="form-control">
                    <option value="I" {{ old('inex', $riskregister->inex) == 'I' ? 'selected' : '' }}>INTERNAL</option>
                    <option value="E" {{ old('inex', $riskregister->inex) == 'E' ? 'selected' : '' }}>EXTERNAL</option>
                </select>
            </div>
        </div>

        <!-- Nama Risiko -->

       <!-- Peluang -->
        <div class="row mb-3">
            <label for="peluang" class="col-sm-2 col-form-label"><strong>Peluang</strong></label>
            <div class="col-sm-7">
                <textarea name="peluang" id="peluang" class="form-control">{{ old('peluang', $riskregister->peluang) }}</textarea>
            </div>
        </div>

        <!-- Pihak Berkepentingan -->
        <div class="row mb-3">
            <label for="pihak" class="col-sm-2 col-form-label"><strong>Pihak Berkepentingan</strong></label>
            <div class="col-sm-7">
                <textarea name="pihak" id="pihak" class="form-control" required>{{ old('pihak', $riskregister->pihak) }}</textarea>
            </div>
        </div>

        <!-- Target Penyelesaian -->
        <div class="row mb-3">
            <label for="target_penyelesaian" class="col-sm-2 col-form-label"><strong>Target Penyelesaian</strong></label>
            <div class="col-sm-7">
                <input type="date" name="target_penyelesaian" id="target_penyelesaian" class="form-control" value="{{ old('target_penyelesaian', $riskregister->target_penyelesaian) }}">
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
                    <label for="targetpic_{{ $tindakan->id }}" class="col-sm-2 col-form-label"><strong>Target PIC</strong></label>
                    <div class="col-sm-7">
                        <textarea name="targetpic[{{ $tindakan->id }}]" id="targetpic_{{ $tindakan->id }}" class="form-control" required>{{ old('targetpic.' . $tindakan->id, $tindakan->targetpic) }}</textarea>
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
            <textarea placeholder="Masukkan Tindakan Lanjut" name="tindakan[]" class="form-control" placeholder="Masukkan Tindakan Lanjut" rows="3" required></textarea>
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
