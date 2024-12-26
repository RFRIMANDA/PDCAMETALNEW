@extends('layouts.main')

@section('content')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card shadow-lg border-0">
    <div class="card-body">
        <h5 class="card-title text-center text-uppercase fw-bold text-primary">Identifikasi Proses Peningkatan Kinerja*</h5>
        <hr class="mb-4" style="border: 1px solid #0d6efd;">

        <!-- General Form Elements -->
        <form method="POST" action="{{ route('ppk.store2') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_formppk" value="{{ $id }}">

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
                <label for="identifikasi" class="form-label fw-bold">2. Identifikasi, evaluasi & pastikan akar penyebab masalah/Root Cause*</label>
                <textarea placeholder="" name="identifikasi" class="form-control" id="identifikasi" rows="7">{{ old('identifikasi', $ppk->identifikasi ?? '') }}</textarea>
                <span style="font-size: 0.750em;">*Gunakan metode 5WHYS untuk menentukan Root Cause; Fish Bone; Diagram alir; Penilaian situasi; Kendali proses dan peningkatan.</span>

            </div>
            <hr>
            <hr>

            <!-- Penanggulangan -->
            <span style="font-size: 2rm;"><strong>3. Usulan tindakan: Jelaskan apa, siapa dan kapan akan dilaksanakan dan siapa yang akan melakukan tindakan Penanggulangan/Pencegahan tersebut dan kapan akan diselesaikan.</strong></span>
            <div class="mb-3">
                <br>
                <br>
                <label for="penanggulangan" class="form-label fw-bold">Penanggulangan*</label>
                <textarea name="penanggulangan" class="form-control" placeholder="" cols="50" rows="7">{{ old('penanggulangan') }}</textarea>
            </div>


            <!-- Target Tanggal Penanggulangan -->
            <div class="mb-3">
                <label for="tgl_penanggulangan" class="form-label fw-bold">Target Tanggal Penanggulangan*</label>
                <input type="date" name="tgl_penanggulangan" class="form-control" value="{{ old('tgl_penanggulangan') }}">
            </div>

            <div class="mb-3" id="pic1-dropdown">
                <label class="form-label fw-bold">Pilih PIC</label>

                <!-- Checkbox untuk memilih antara PIC 1 atau PIC Other -->
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="use-pic1" name="use_pic1" value="1"
                           {{ old('use_pic1', $ppk->use_pic1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="use-pic1">Opsi</label>
                </div>

                <!-- Dropdown PIC 1 -->
                <div id="pic1-container" class="pic1-dropdown">
                    @php
                        $oldPic1 = old('pic1', explode(',', $ppk->pic1));
                    @endphp

                    @foreach($oldPic1 as $selectedPic1)
                        <div class="input-group mb-2">
                            <select name="pic1[]" class="form-select">
                                <option value="">Pilih PIC</option>
                                @foreach($data as $user)
                                    <option value="{{ $user->id }}" {{ $selectedPic1 == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama_user }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-danger remove-pic">-</button>
                        </div>
                    @endforeach
                </div>

                <!-- Textarea PIC Other -->
                <div id="pic1-other" class="pic1other" style="display: none;">
                    <textarea name="pic1_other" id="pic1_other" class="form-control" placeholder="Silahkan masukan PIC diluar option">{{ old('pic1_other', $ppk->pic1_other) }}</textarea>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="button" class="btn btn-outline-primary add-pic" data-target="pic1-container">
                    <i class="fa fa-plus"></i> Tambah PIC
                </button>
            </div>

            <script>
                // Menambahkan event listener untuk checkbox
                document.getElementById('use-pic1').addEventListener('change', function() {
                    var pic1Container = document.getElementById('pic1-container');
                    var pic1Other = document.getElementById('pic1-other');

                    if (this.checked) {
                        pic1Container.style.display = 'block';
                        pic1Other.style.display = 'none';
                    } else {
                        pic1Container.style.display = 'none';
                        pic1Other.style.display = 'block';
                    }
                });
            </script>

            <hr>

            <!-- Pencegahan -->
            <div class="mb-3">
                <label for="pencegahan" class="form-label fw-bold">Pencegahan*</label>
                <textarea name="pencegahan" class="form-control" placeholder="" cols="50" rows="7">{{ old('pencegahan') }}</textarea>
            </div>

            <!-- Target Tanggal Pencegahan -->
            <div class="mb-3">
                <label for="tgl_pencegahan" class="form-label fw-bold">Target Tanggal Pencegahan*</label>
                <input type="date" name="tgl_pencegahan" class="form-control" value="{{ old('tgl_pencegahan') }}">
            </div>

            <!-- PIC 2 -->
            <div class="mb-3" id="pic2-dropdown">
                <label class="form-label fw-bold">Pilih PIC 2*</label>

                <!-- Checkbox untuk memilih antara PIC 2 atau PIC Other -->
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="use-pic2" name="use_pic2" value="1"
                           {{ old('use_pic2', $ppk->use_pic2) ? 'checked' : '' }}>
                    <label class="form-check-label" for="use-pic2">Pilih Opsi</label>
                </div>

                <!-- Dropdown PIC 2 -->
                <div id="pic2-container" class="pic2-dropdown">
                    @php
                        $oldPic2 = old('pic2', explode(',', $ppk->pic2));
                    @endphp

                    @foreach($oldPic2 as $selectedPic2)
                        <div class="input-group mb-2">
                            <select name="pic2[]" class="form-select">
                                <option value="">Pilih PIC</option>
                                @foreach($data as $user)
                                    <option value="{{ $user->id }}" {{ $selectedPic2 == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama_user }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-danger remove-pic">-</button>
                        </div>
                    @endforeach
                </div>

                <!-- Textarea PIC Other -->
                <div id="pic2-other" class="pic2-other" style="display: none;">
                    <textarea name="pic2_other" id="pic2_other" class="form-control" placeholder="Silahkan masukan PIC diluar option">{{ old('pic2_other', $ppk->pic2_other) }}</textarea>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="button" class="btn btn-outline-primary add-pic" data-target="pic2-container">
                    <i class="fa fa-plus"></i> Tambah PIC
                </button>
            </div>

            <script>
                // Menambahkan event listener untuk checkbox PIC 2
                document.getElementById('use-pic2').addEventListener('change', function() {
                    var pic2Container = document.getElementById('pic2-container');
                    var pic2Other = document.getElementById('pic2-other');

                    if (this.checked) {
                        pic2Container.style.display = 'block';
                        pic2Other.style.display = 'none';
                    } else {
                        pic2Container.style.display = 'none';
                        pic2Other.style.display = 'block';
                    }
                });
            </script>


            <hr>

            <script>
                // Menambahkan dropdown PIC 1 baru
                document.querySelectorAll('.add-pic[data-target="pic1-container"]').forEach(button => {
                    button.addEventListener('click', function() {
                        var newPic = document.createElement('div');
                        newPic.classList.add('input-group', 'mb-2');
                        newPic.innerHTML = `
                            <select name="pic1[]" class="form-select">
                                <option value="">Pilih PIC</option>
                                @foreach($data as $user)
                                    <option value="{{ $user->id }}">{{ $user->nama_user }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-danger remove-pic">-</button>
                        `;
                        document.getElementById('pic1-container').appendChild(newPic);
                    });
                });

                // Menghapus dropdown PIC 1
                document.getElementById('pic1-container').addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('remove-pic')) {
                        e.target.closest('.input-group').remove();
                    }
                });

                // Menambahkan dropdown PIC 2 baru
                document.querySelectorAll('.add-pic[data-target="pic2-container"]').forEach(button => {
                    button.addEventListener('click', function() {
                        var newPic = document.createElement('div');
                        newPic.classList.add('input-group', 'mb-2');
                        newPic.innerHTML = `
                            <select name="pic2[]" class="form-select">
                                <option value="">Pilih PIC</option>
                                @foreach($data as $user)
                                    <option value="{{ $user->id }}">{{ $user->nama_user }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-danger remove-pic">-</button>
                        `;
                        document.getElementById('pic2-container').appendChild(newPic);
                    });
                });

                // Menghapus dropdown PIC 2
                document.getElementById('pic2-container').addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('remove-pic')) {
                        e.target.closest('.input-group').remove();
                    }
                });
            </script>
             <!-- Tanda Tangan -->
             <div class="row mb-3">
                <label for="signaturepenerima" class="col-sm-2 col-form-label fw-bold">Tanda Tangan Penerima* (Pilih Opsi)</label>
                <div class="col-sm-10">
                    <!-- Pilihan Opsi -->
                    <div class="row mb-3 mt-1">
                        <div class="form-check mb-2">
                            <div class="col-sm-10">
                                <input class="form-check-input" type="radio" name="signature_option" id="option1" required value="1" checked>
                                <label class="form-check-label" for="option1"><strong>1. Tanda tangan langsung</strong></label>
                            </div>
                        </div>
                        <div class="form-check">
                            <div class="col-sm-10">
                            <input class="form-check-input" type="radio" name="signature_option" id="option2" required value="2">
                            <label class="form-check-label" for="option2"><strong>2. Unggah file tanda tangan</strong></label>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi untuk Menggambar Tanda Tangan -->
                    <div id="option1-container" class="mb-3 border p-3 rounded" style="background-color: #f8f9fa;">
                        <p class="fw-bold">Opsi 1: Tanda tangan langsung</p>
                        <canvas id="signature-pad" class="border rounded" style="width: 100%; height: 200px;"></canvas>
                        <button id="clear" title="Clear" type="button" class="btn btn-outline-secondary mt-2">
                            <i class="bx bxs-eraser"></i> Clear
                        </button>
                        <input type="hidden" name="signaturepenerima" id="signature">
                    </div>

                    <!-- Divider -->
                    <div class="border-top my-4"></div>

                    <!-- Opsi untuk Mengunggah Tanda Tangan -->
                    <div id="option2-container" class="mb-3 border p-3 rounded d-none" style="background-color: #f8f9fa;">
                        <p class="fw-bold">Opsi 2: Unggah file tanda tangan</p>
                        <input type="file" name="signaturepenerima_file" id="signaturepenerima-file" class="form-control">
                        <small class="text-muted d-block mt-2">Format file yang didukung: jpg, jpeg, png</small>
                    </div>
                </div>
            </div>

            <script>
                // Mengontrol visibilitas opsi berdasarkan pilihan checkbox
                const option1Radio = document.getElementById('option1');
                const option2Radio = document.getElementById('option2');
                const option1Container = document.getElementById('option1-container');
                const option2Container = document.getElementById('option2-container');

                option1Radio.addEventListener('change', () => {
                    if (option1Radio.checked) {
                        option1Container.classList.remove('d-none');
                        option2Container.classList.add('d-none');
                    }
                });

                option2Radio.addEventListener('change', () => {
                    if (option2Radio.checked) {
                        option2Container.classList.remove('d-none');
                        option1Container.classList.add('d-none');
                    }
                });
            </script>


            <script>
                // JavaScript for handling the file preview
                document.getElementById('evidence').addEventListener('change', function(event) {
                    const previewContainer = document.getElementById('evidencePreviewContainer');
                    previewContainer.innerHTML = ''; // Clear previous previews

                    // Loop through selected files
                    Array.from(event.target.files).forEach(file => {
                        const fileReader = new FileReader();

                        fileReader.onload = function(e) {
                            const fileUrl = e.target.result;
                            const fileExtension = file.name.split('.').pop().toLowerCase();

                            // Create a container for each file preview
                            const filePreview = document.createElement('div');
                            filePreview.classList.add('file-preview', 'text-center');
                            filePreview.style.width = '200px';

                            if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                                // For image files, show image preview
                                const img = document.createElement('img');
                                img.src = fileUrl;
                                img.alt = file.name;
                                img.style = 'max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;';
                                filePreview.appendChild(img);
                            } else {
                                // For other file types, show a download link
                                const link = document.createElement('a');
                                link.href = fileUrl;
                                link.target = '_blank';
                                link.textContent = file.name;
                                link.classList.add('btn', 'btn-primary', 'btn-sm', 'w-100');
                                filePreview.appendChild(link);
                            }

                            // Add file name below the preview
                            const fileName = document.createElement('small');
                            fileName.textContent = file.name;
                            fileName.style = 'display: block; margin-top: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;';
                            filePreview.appendChild(fileName);

                            previewContainer.appendChild(filePreview);
                        };

                        fileReader.readAsDataURL(file); // Read the file as a data URL
                    });
                });
            </script>


            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route('ppk.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update <i class="ri-save-3-fill"></i></button>
            </div>
        </form>
    </div>
</div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@3.0.0/dist/signature_pad.umd.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi Signature Pad
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas);
    const ctx = canvas.getContext('2d');
    const ratio = Math.max(window.devicePixelRatio || 1, 1);

    // Set canvas size
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    ctx.scale(ratio, ratio);

    // Clear signature pad
    document.getElementById('clear').addEventListener('click', function () {
        signaturePad.clear();
    });

    // Menyimpan tanda tangan ke input hidden saat form disubmit
    document.querySelector('form').addEventListener('submit', function (e) {
        const signatureInput = document.getElementById('signature');
        const fileInput = document.getElementById('signaturepenerima-file');

        // Check apakah salah satu opsi sudah diisi
        const isCanvasSigned = !signaturePad.isEmpty();
        const isFileUploaded = fileInput.files.length > 0;

        if (isCanvasSigned) {
            // Jika ada tanda tangan di canvas, simpan datanya
            signatureInput.value = signaturePad.toDataURL();
        }

        if (!isCanvasSigned && !isFileUploaded) {
            // Jika keduanya kosong, tampilkan pesan kesalahan
            alert("Silakan buat tanda tangan di canvas atau unggah file tanda tangan.");
            e.preventDefault(); // Mencegah pengiriman form
        }
    });

    // Resize canvas saat jendela diubah ukurannya
    window.addEventListener('resize', function () {
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        ctx.scale(ratio, ratio);
    });
});

</script>


@endsection

