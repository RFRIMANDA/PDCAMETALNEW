@extends('layouts.main')

@section('content')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">VERIFIKASI PROSES PENINGKATAN KINERJA</h5>

        <form method="POST" action="{{ route('ppk.store3') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_formppk" value="{{ $id }}">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Verifikasi -->
            <span style="font-size: 2rm;"><strong>4. Verifikasi Tindakan.</strong></span>
            <p></p>

            <div class="row mb-3">
                <br>
                <label for="verifikasi" class="col-sm-2 col-form-label"><strong>Catatan</strong></label>
                <div class="col-sm-10">
                    <textarea name="verifikasi" class="form-control" placeholder="Masukkan Catatan">{{ old('verifikasi') }}</textarea>
                </div>
            </div>

            <!-- Tinjauan -->
            {{-- <span style="font-size: 2rm;"><strong>5.</strong></span> --}}
            <p style="font-size: 2rm;"><strong>5. a) Tinjauan Status penyelesaian Tindakan Penanggulangan</strong></p>
            <p style="font-size: 2rm; padding-left: 20px;">
                <strong>b) Tinjauan efektivitas atas hasil Tindakan Pencegahan</strong>
            </p>


            <p></p>
            <div class="row mb-3">
                <label for="tinjauan" class="col-sm-2 col-form-label"><strong>Catatan</strong></label>
                <div class="col-sm-10">
                    <textarea name="tinjauan" class="form-control" placeholder="Masukkan tinjauan">{{ old('tinjauan') }}</textarea>
                </div>
            </div>

            <div class="mb-3">
                <label for="verifikasi_img" class="form-label">Upload Gambar (jika ada)</label>
                <input type="file" name="verifikasi_img[]" id="verifikasi_img" class="form-control" multiple>
            </div>

           <!-- Status -->
            <div class="row mb-3">
                <label for="status" class="col-sm-2 col-form-label"><strong>Status</strong></label>
                <div class="col-sm-10">
                    <!-- Checkbox TRUE -->
                    <div class="form-check">
                        <input type="checkbox" name="status" id="status_true" value="TRUE" class="form-check-input"
                            {{ old('status') == 'TRUE' ? 'checked' : '' }}
                            onclick="handleCheckbox('status_true', 'status_false')">
                        <label for="status_true" class="form-check-label">
                            Efektif, dalam 1 bulan masalah yang sama tidak muncul lagi & tindakan penanggulangan sudah selesai
                        </label>
                    </div>

                    <br>

                    <!-- Checkbox FALSE -->
                    <div class="form-check">
                        <input type="checkbox" name="status" id="status_false" value="FALSE" class="form-check-input"
                            {{ old('status') == 'FALSE' ? 'checked' : '' }}
                            onclick="handleCheckbox('status_false', 'status_true'); toggleNewPpk()">
                        <label for="status_false" class="form-check-label">
                            Tidak efektif, dilanjutkan dengan PPK No.
                        </label>

                        <!-- Input New PPK -->
                        <div class="mt-2">
                            <input type="text" name="newppk" id="newppk" class="form-control"
                                placeholder="Masukkan No PPK apabila tidak efektif"
                                value="{{ old('newppk') }}"
                                style="{{ old('status') == 'FALSE' ? 'display: block;' : 'display: none;' }}">
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Fungsi untuk memastikan hanya satu checkbox yang dipilih
                function handleCheckbox(selectedId, otherId) {
                    const selected = document.getElementById(selectedId);
                    const other = document.getElementById(otherId);

                    if (selected.checked) {
                        other.checked = false; // Matikan checkbox lainnya
                    }

                    // Pastikan input New PPK ditutup jika "FALSE" tidak dipilih
                    toggleNewPpk();
                }

                // Fungsi untuk menampilkan atau menyembunyikan input newppk
                function toggleNewPpk() {
                    const checkbox = document.getElementById('status_false');
                    const input = document.getElementById('newppk');

                    if (checkbox.checked) {
                        input.style.display = 'block'; // Tampilkan input
                    } else {
                        input.style.display = 'none'; // Sembunyikan input
                        input.value = ''; // Reset nilai input jika disembunyikan
                    }
                }

                // Pastikan input ditampilkan atau disembunyikan sesuai status saat halaman dimuat
                window.onload = function() {
                    toggleNewPpk();
                };
            </script>

            <div class="row mb-3">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="{{ route('ppk.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Update <i class="ri-save-3-fill"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
