@extends('layouts.main')

@section('content')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title text-center text-uppercase fw-bold text-primary">Edit Verifikasi Proses Peningkatan Kinerja</h5>
        <hr class="mb-4" style="border: 1px solid #0d6efd;">

        <form method="POST" action="{{ route('ppk.update3', $ppk->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" name="id_formppk" value="{{ $ppk->id_formppk }}">

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
            <span style="font-size: 2rm;"><strong>4. Verifikasi Tindakan</strong></span>
            <p></p>

            <div class="row mb-3">
                <label for="verifikasi" class="col-sm-2 col-form-label"><strong>Catatan</strong></label>
                <div class="col-sm-10">
                    <textarea name="verifikasi" class="form-control" placeholder="Masukkan Catatan">{{ old('verifikasi', $ppk->verifikasi ?? '') }}</textarea>
                </div>
            </div>

            <p style="font-size: 2rm;"><strong>5. a) Tinjauan Status penyelesaian Tindakan Penanggulangan</strong></p>
            <p style="font-size: 2rm; padding-left: 20px;">
                <strong>b) Tinjauan efektivitas atas hasil Tindakan Pencegahan</strong>
            </p>

            <div class="row mb-3">
                <label for="tinjauan" class="col-sm-2 col-form-label"><strong>Catatan</strong></label>
                <div class="col-sm-10">
                    <textarea name="tinjauan" class="form-control" placeholder="Masukkan Tinjauan">{{ old('tinjauan', $ppk->tinjauan ?? '') }}</textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="verifikasi_img" class="col-sm-2 col-form-label"><strong>Gambar Verifikasi</strong></label>
                <div class="col-sm-10">
                    @php
                        // Decode the JSON field to an array of file paths
                        $verifikasi_imgs = json_decode($ppk->verifikasi_img ?? '[]', true);
                    @endphp

                    @if(is_array($verifikasi_imgs) && count($verifikasi_imgs) > 0)
                        <div id="verifikasiPreviewContainer" class="d-flex flex-wrap mt-3">
                            @foreach($verifikasi_imgs as $index => $verifikasi_img)
                                @php
                                    $filePath = asset('storage/' . $verifikasi_img);  // Generate the correct file path
                                    $fileExtension = pathinfo($verifikasi_img, PATHINFO_EXTENSION);  // Get the file extension
                                @endphp
                                <div class="evidence-item text-center me-3 mb-2">
                                    @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ $filePath }}" alt="Verifikasi Image" class="img-thumbnail" style="max-width: 150px; height: auto;">
                                    @else
                                        <a href="{{ $filePath }}" target="_blank">{{ basename($verifikasi_img) }}</a>
                                    @endif
                                    <br>
                                    <a href="{{ $filePath }}" download="{{ basename($verifikasi_img) }}" title="Download Image" class="btn btn-sm btn-primary mt-2">
                                        <i class="bi bi-cloud-download"></i> Download
                                    </a>
                                    <br>
                                    <input type="checkbox" name="delete_verifikasi[]" value="{{ $verifikasi_img }}" id="delete_{{ $index }}">
                                    <label for="delete_{{ $index }}" class="text-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>No uploaded images.</p>
                    @endif

                    <div class="mt-3">
                        <label for="verifikasi_img"><strong>Tambah Gambar (jika ada)</strong></label>
                        <input type="file" name="verifikasi_img[]" id="verifikasi_img" class="form-control" multiple onchange="previewImages()">
                    </div>

                    <div id="imagePreview" class="d-flex flex-wrap mt-3">
                        <!-- Preview images will appear here -->
                    </div>
                </div>
            </div>

            <script>
                function previewImages() {
                    const fileInput = document.getElementById('verifikasi_img');
                    const previewContainer = document.getElementById('imagePreview');

                    // Clear previous previews
                    previewContainer.innerHTML = '';

                    // Loop through the selected files
                    const files = fileInput.files;
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const reader = new FileReader();

                        // Preview the image
                        reader.onload = function(e) {
                            const imgElement = document.createElement('img');
                            imgElement.src = e.target.result;
                            imgElement.classList.add('img-thumbnail');
                            imgElement.style.maxWidth = '150px';
                            imgElement.style.height = 'auto';
                            previewContainer.appendChild(imgElement);
                        };

                        // Read the file as a data URL
                        reader.readAsDataURL(file);
                    }
                }
            </script>

            <!-- Status -->
            <div class="row mb-3">
                <label for="status" class="col-sm-2 col-form-label"><strong>Status</strong></label>
                <div class="col-sm-10">
                    <!-- Radio Button TRUE -->
                    <div class="form-check">
                        <input type="radio" name="status" id="status_true" value="TRUE" class="form-check-input"
                            {{ old('status', $ppk->status) == 'TRUE' ? 'checked' : '' }}
                            onclick="toggleNewPpk()">
                        <label for="status_true" class="form-check-label">
                            Efektif, dalam 1 bulan masalah yang sama tidak muncul lagi & tindakan penanggulangan sudah selesai
                        </label>
                    </div>

                    <br>

                    <!-- Radio Button FALSE -->
                    <div class="form-check">
                        <input type="radio" name="status" id="status_false" value="FALSE" class="form-check-input"
                            {{ old('status', $ppk->status) == 'FALSE' ? 'checked' : '' }}
                            onclick="toggleNewPpk()">
                        <label for="status_false" class="form-check-label">
                            Tidak efektif, dilanjutkan dengan PPK No.
                        </label>

                        <!-- Input New PPK -->
                        <div class="mt-2">
                            <input type="text" name="newppk" id="newppk" class="form-control"
                                placeholder="Masukkan No PPK apabila tidak efektif"
                                value="{{ old('newppk', $ppk->newppk ?? '') }}"
                                style="{{ old('status', $ppk->status) == 'FALSE' ? 'display: block;' : 'display: none;' }}">
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function toggleNewPpk() {
                    // Ambil elemen radio buttons dan input
                    const radioFalse = document.getElementById('status_false');
                    const radioTrue = document.getElementById('status_true');
                    const input = document.getElementById('newppk');

                    // Tampilkan input hanya jika radio FALSE dipilih
                    if (radioFalse.checked) {
                        input.style.display = 'block';
                    } else if (radioTrue.checked) {
                        input.style.display = 'none';
                        input.value = ''; // Reset nilai input jika disembunyikan
                    }
                }

                // Pastikan input ditampilkan atau disembunyikan sesuai status saat halaman dimuat
                window.onload = function() {
                    toggleNewPpk();
                };
            </script>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route('ppk.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update <i class="ri-save-3-fill"></i></button>
            </div>
        </form>
    </div>
</div>

@endsection
