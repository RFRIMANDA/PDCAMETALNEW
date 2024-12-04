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

              <!-- PIC Penanggulangan -->
              <div class="mb-3">
                <label for="pic1" class="form-label fw-bold">PIC Penanggulangan</label>
                <select name="pic1" class="form-select" id="pic1">
                    <option value="">Pilih PIC</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('pic1', $ppk->pic1) == $user->id ? 'selected' : '' }}>{{ $user->nama_user }}</option>
                    @endforeach
                    <option value="other" {{ old('pic1', $ppk->pic1) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <!-- Input field for 'Other' PIC Penanggulangan -->
                <input type="text" name="pic1_other" class="form-control mt-2" id="pic1_other" placeholder="Enter custom PIC" value="{{ old('pic1_other', ($ppk->pic1 == 'other' ? $ppk->pic1_other : '')) }}" style="display: none;">
            </div>

            <hr>
            <hr>

            <!-- Pencegahan -->
            <div class="mb-3">
                <label for="pencegahan" class="form-label fw-bold">Pencegahan</label>
                <textarea name="pencegahan" class="form-control" placeholder="Masukkan tindakan pencegahan">{{ old('pencegahan', $ppk->pencegahan) }}</textarea>
            </div>

            <!-- Target Tanggal Pencegahan -->
            <div class="mb-3">
                <label for="tgl_pencegahan" class="form-label fw-bold">Target Tanggal Pencegahan</label>
                <input type="date" name="tgl_pencegahan" class="form-control" value="{{ old('tgl_pencegahan', $ppk->tgl_pencegahan) }}">
            </div>

             <!-- PIC Pencegahan -->
             <div class="mb-3">
                <label for="pic2" class="form-label fw-bold">PIC Pencegahan</label>
                <select name="pic2" class="form-select" id="pic2">
                    <option value="">Pilih PIC</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('pic2', $ppk->pic2) == $user->id ? 'selected' : '' }}>{{ $user->nama_user }}</option>
                    @endforeach
                    <option value="other" {{ old('pic2', $ppk->pic2) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <!-- Input field for 'Other' PIC Pencegahan -->
                <input type="text" name="pic2_other" class="form-control mt-2" id="pic2_other" placeholder="Enter custom PIC" value="{{ old('pic2_other', ($ppk->pic2 == 'other' ? $ppk->pic2_other : '')) }}" style="display: none;">
            </div>

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
