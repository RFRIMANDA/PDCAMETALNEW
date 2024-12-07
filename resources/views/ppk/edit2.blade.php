@extends('layouts.main')

@section('content')

<div class="card shadow-lg border-0">
    <div class="card-body">
        <h5 class="card-title text-center text-uppercase fw-bold text-primary">Edit Identifikasi Proses Peningkatan Kinerja</h5>
        <hr class="mb-4" style="border: 1px solid #0d6efd;">

        <!-- Edit Form -->
        <form method="POST" action="{{ route('ppk.update2', $ppk->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Method spoofing for update -->

            <!-- Hidden field for ID -->
            <input type="hidden" name="id_formppk" value="{{ $ppk->id }}">
            <div class="mb-3">
                <input type="hidden" name="signaturepenerima" class="form-control" value="{{ old('signaturepenerima', $ppk->signaturepenerima) }}">
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Identifikasi -->
            <div class="mb-3">
                <label for="identifikasi" class="form-label fw-bold">2. Identifikasi, evaluasi & pastikan akar penyebab masalah/Root Cause</label>
                <br>
                <br>
                <textarea placeholder="Masukan identifikasi" name="identifikasi" class="form-control" id="identifikasi" rows="3">{{ old('identifikasi', $ppk->identifikasi) }}</textarea>
                <span style="font-size: 0.750em;">*Gunakan metode 5WHYS untuk menentukan Root Cause; Fish Bone; Diagram alir; Penilaian situasi; Kendali proses dan peningkatan.</span>
            </div>
            <hr>
            <hr>

            <!-- Penanggulangan -->
            <span style="font-size: 2rm;"><strong>3. Usulan tindakan: Jelaskan apa, siapa dan kapan akan dilaksanakan dan siapa yang akan melakukan tindakan Penanggulangan/Pencegahan tersebut dan kapan akan diselesaikan.</strong></span>
            <div class="mb-3">
                <br>
                <br>
                <label for="penanggulangan" class="form-label fw-bold">Penanggulangan</label>
                <textarea name="penanggulangan" class="form-control" placeholder="Masukkan tindakan penanggulangan">{{ old('penanggulangan', $ppk->penanggulangan) }}</textarea>
            </div>

            <!-- Target Tanggal Penanggulangan -->
            <div class="mb-3">
                <label for="tgl_penanggulangan" class="form-label fw-bold">Target Tanggal Penanggulangan</label>
                <input type="date" name="tgl_penanggulangan" class="form-control" value="{{ old('tgl_penanggulangan', $ppk->tgl_penanggulangan) }}">
            </div>

            <!-- Pilihan untuk memilih antara dropdown atau input teks PIC Penanggulangan -->
<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="pic1-option" {{ old('pic1', $ppk->pic1) ? 'checked' : '' }}>
        <label class="form-check-label" for="pic1-option">Pilih PIC dari Daftar</label>
    </div>
</div>

<!-- PIC Penanggulangan Dropdown -->
<div class="mb-3" id="pic1-dropdown">
    <label for="pic1" class="form-label fw-bold">PIC Penanggulangan</label>
    <select name="pic1" class="form-select" id="pic1"
            {{ old('pic1', $ppk->pic1) || old('pic1_other', $ppk->pic1_other) ? 'readonly' : '' }}>
        <option value="">Pilih PIC</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ old('pic1', $ppk->pic1) == $user->id ? 'selected' : '' }}>{{ $user->nama_user }}</option>
        @endforeach
    </select>
</div>

<!-- PIC Penanggulangan Input Text -->
<div class="mb-3" id="pic1-other">
    <label for="pic1_other" class="form-label fw-bold">PIC OTHER</label>
    <input type="text" name="pic1_other" class="form-control" value="{{ old('pic1_other', $ppk->pic1_other) }}"
           {{ old('pic1', $ppk->pic1) || old('pic1_other', $ppk->pic1_other) ? 'readonly' : '' }}>
</div>

<script>
    // Fungsi untuk toggle dropdown atau input teks berdasarkan checkbox
    document.getElementById('pic1-option').addEventListener('change', function() {
        const pic1Option = document.getElementById('pic1-option');
        const pic1Dropdown = document.getElementById('pic1-dropdown');
        const pic1Other = document.getElementById('pic1-other');
        const pic1Select = document.getElementById('pic1');
        const pic1OtherInput = document.getElementsByName('pic1_other')[0];

        // Jika checkbox dicentang, tampilkan dropdown dan sembunyikan input teks
        if (pic1Option.checked) {
            pic1Dropdown.style.display = 'block';
            pic1Other.style.display = 'none';

            // Remove readonly on pic1 dropdown
            pic1Select.removeAttribute('readonly');
            pic1OtherInput.setAttribute('readonly', 'readonly');
        } else {
            pic1Dropdown.style.display = 'none';
            pic1Other.style.display = 'block';

            // Remove readonly on pic1_other input
            pic1OtherInput.removeAttribute('readonly');
            pic1Select.setAttribute('readonly', 'readonly');
        }
    });

    // Inisialisasi tampilan berdasarkan status checkbox saat halaman pertama dimuat
    window.onload = function() {
        const pic1Option = document.getElementById('pic1-option');
        const pic1Dropdown = document.getElementById('pic1-dropdown');
        const pic1Other = document.getElementById('pic1-other');
        const pic1Select = document.getElementById('pic1');
        const pic1OtherInput = document.getElementsByName('pic1_other')[0];

        // If either pic1 or pic1_other has a value, disable the other
        if ({{ old('pic1', $ppk->pic1) ? 'true' : 'false' }} || {{ old('pic1_other', $ppk->pic1_other) ? 'true' : 'false' }}) {
            // Disable pic1_other or pic1 based on which one is filled
            if ({{ old('pic1', $ppk->pic1) ? 'true' : 'false' }}) {
                pic1Select.setAttribute('readonly', 'readonly');
                pic1OtherInput.removeAttribute('readonly');
            } else {
                pic1OtherInput.setAttribute('readonly', 'readonly');
                pic1Select.removeAttribute('readonly');
            }

            // Display corresponding input field based on initial value
            pic1Option.checked = true;
            pic1Dropdown.style.display = 'block';
            pic1Other.style.display = 'none';
        } else {
            pic1Option.checked = false;
            pic1Dropdown.style.display = 'none';
            pic1Other.style.display = 'block';
            pic1Select.removeAttribute('readonly');
            pic1OtherInput.removeAttribute('readonly');
        }

        // Triggers for initial state based on old value
        pic1Option.dispatchEvent(new Event('change'));
    }
</script>

            <hr>
            <hr>

            <!-- Pencegahan -->
            <div class="mb-3">
                <label for="pencegahan" class="form-label fw-bold">Pencegahan</label>
                <textarea name="pencegahan" class="form-control" placeholder="Masukkan tindakan pencegahan">{{ old('pencegahan', $ppk->pencegahan) }}</textarea>
            </div>
            <!-- Target Tanggal Pencegahan -->


            <!-- Target Tanggal Pencegahan -->
            <div class="mb-3">
                <label for="tgl_pencegahan" class="form-label fw-bold">Target Tanggal Pencegahan</label>
                <input type="date" name="tgl_pencegahan" class="form-control" value="{{ old('tgl_pencegahan', $ppk->tgl_pencegahan) }}">
            </div>

             <!-- Pilihan untuk memilih antara dropdown atau input teks PIC Pencegahan -->
<div class="mb-3">
    <label class="form-check-label fw-bold">Pilih Tipe PIC Pencegahan</label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="pic2-option" {{ old('pic2', $ppk->pic2) ? 'checked' : '' }}>
        <label class="form-check-label" for="pic2-option">Pilih PIC dari Daftar</label>
    </div>
</div>

<!-- PIC Pencegahan Dropdown -->
<div class="mb-3" id="pic2-dropdown">
    <label for="pic2" class="form-label fw-bold">PIC Pencegahan</label>
    <select name="pic2" class="form-select" id="pic2"
            {{ old('pic2', $ppk->pic2) || old('pic2_other', $ppk->pic2_other) ? 'readonly' : '' }}>
        <option value="">Pilih PIC</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ old('pic2', $ppk->pic2) == $user->id ? 'selected' : '' }}>{{ $user->nama_user }}</option>
        @endforeach
    </select>
</div>

<!-- PIC Pencegahan Input Text -->
<div class="mb-3" id="pic2-other">
    <label for="pic2_other" class="form-label fw-bold">PIC 2 OTHER</label>
    <input type="text" name="pic2_other" class="form-control" value="{{ old('pic2_other', $ppk->pic2_other) }}"
           {{ old('pic2_other', $ppk->pic2_other) || old('pic2_other', $ppk->pic2_other) ? 'readonly' : '' }}>
</div>

<script>
    // Script untuk toggle dropdown atau input teks berdasarkan checkbox
    document.getElementById('pic2-option').addEventListener('change', function() {
        const pic2Option = document.getElementById('pic2-option');
        const pic2Dropdown = document.getElementById('pic2-dropdown');
        const pic2Other = document.getElementById('pic2-other');
        const pic2Select = document.getElementById('pic2');
        const pic2OtherInput = document.getElementsByName('pic2_other')[0];

        // Jika checkbox dicentang, tampilkan dropdown dan sembunyikan input teks
        if (pic2Option.checked) {
            pic2Dropdown.style.display = 'block';
            pic2Other.style.display = 'none';

            // Remove readonly on pic2 dropdown
            pic2Select.removeAttribute('readonly');
            pic2OtherInput.setAttribute('readonly', 'readonly');
        } else {
            pic2Dropdown.style.display = 'none';
            pic2Other.style.display = 'block';

            // Remove readonly on pic2_other input
            pic2OtherInput.removeAttribute('readonly');
            pic2Select.setAttribute('readonly', 'readonly');
        }
    });

    // Inisialisasi tampilan berdasarkan status checkbox saat halaman pertama dimuat
    window.onload = function() {
        const pic2Option = document.getElementById('pic2-option');
        const pic2Dropdown = document.getElementById('pic2-dropdown');
        const pic2Other = document.getElementById('pic2-other');
        const pic2Select = document.getElementById('pic2_other');
        const pic2OtherInput = document.getElementsByName('pic2_other')[0];

        // If either pic2 or pic2_other has a value, disable the other
        if ({{ old('pic2_other', $ppk->pic2_other) ? 'true' : 'false' }} || {{ old('pic2_other', $ppk->pic2_other) ? 'true' : 'false' }}) {
            // Disable pic2_other or pic2 based on which one is filled
            if ({{ old('pic2_other', $ppk->pic2_other) ? 'true' : 'false' }}) {
                pic2Select.setAttribute('readonly', 'readonly');
                pic2OtherInput.removeAttribute('readonly');
            } else {
                pic2OtherInput.setAttribute('readonly', 'readonly');
                pic2Select.removeAttribute('readonly');
            }

            // Display corresponding input field based on initial value
            pic2Option.checked = true;
            pic2Dropdown.style.display = 'block';
            pic2Other.style.display = 'none';
        } else {
            pic2Option.checked = false;
            pic2Dropdown.style.display = 'none';
            pic2Other.style.display = 'block';
            pic2Select.removeAttribute('readonly');
            pic2OtherInput.removeAttribute('readonly');
        }

        // Triggers for initial state based on old value
        pic2Option.dispatchEvent(new Event('change'));
    }
</script>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route('ppk.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update <i class="ri-save-3-fill"></i></button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle for pic2
        const pic2Select = document.getElementById('pic2');
        const pic2OtherInput = document.getElementById('pic2_other');
        if (pic2Select.value === 'other' || '{{ old('pic2') }}' === 'other') {
            pic2OtherInput.style.display = 'block';
        } else {
            pic2OtherInput.style.display = 'none';
        }

        pic2Select.addEventListener('change', function () {
            if (this.value === 'other') {
                pic2OtherInput.style.display = 'block';
            } else {
                pic2OtherInput.style.display = 'none';
            }
        });

        // Toggle for pic1
        const pic1Select = document.getElementById('pic1');
        const pic1OtherInput = document.getElementById('pic1_other');
        if (pic1Select.value === 'other' || '{{ old('pic1') }}' === 'other') {
            pic1OtherInput.style.display = 'block';
        } else {
            pic1OtherInput.style.display = 'none';
        }

        pic1Select.addEventListener('change', function () {
            if (this.value === 'other') {
                pic1OtherInput.style.display = 'block';
            } else {
                pic1OtherInput.style.display = 'none';
            }
        });
    });
</script>

@endsection
