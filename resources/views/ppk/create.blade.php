@extends('layouts.main')

@section('content')

<body>
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <h5 class="card-title text-center text-uppercase fw-bold text-primary">Proses Peningkatan Kinerja</h5>
            <hr class="mb-4" style="border: 1px solid #0d6efd;">

            <form method="POST" action="{{ route('ppk.store') }}" enctype="multipart/form-data">
                @csrf

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

                <!-- Judul PPK -->
                <div class="mb-3">
                    <label for="inputJudul" class="form-label fw-bold">1. Jelaskan ketidaksesuaian yang terjadi atau peningkatan yang akan dibuat*</label>
                    <textarea name="judul" class="form-control" placeholder="" rows="3">{{ old('judul') }}</textarea>
                </div>

                <!-- Evidence -->
                <div class="mb-3">
                    <label for="evidence" class="form-label fw-bold">Evidence*</label>
                    <div>
                        <input type="file" id="evidence" name="evidence[]" class="form-control" multiple>
                        <!-- Preview container for uploaded files -->
                        <div id="evidencePreviewContainer" class="mt-3 d-flex flex-wrap gap-3"></div>
                    </div>
                </div>

                <!-- Jenis Ketidaksesuaian -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Jenis Ketidaksesuaian*</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="SISTEM">
                        <label class="form-check-label">Sistem</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="PROSES">
                        <label class="form-check-label">Proses</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="PRODUK">
                        <label class="form-check-label">Produk</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="AUDIT">
                        <label class="form-check-label">Audit</label>
                    </div>
                </div>

                <!-- Pembuat dan Divisi Pembuat -->
                <div class="row g-3 mb-3">
                    <!-- Nama Inisiator -->
                    <div class="col-md-6">
                        <label for="pembuat" class="form-label fw-bold">Nama Inisiator*</label>
                        <select id="pembuat" name="pembuat" class="form-select">
                            <option value="">Pilih Pembuat</option>
                            @foreach($data as $user)
                                <option value="{{ $user->nama_user }}"
                                        data-email="{{ $user->email }}"
                                        data-divisi="{{ $user->divisi }}">
                                    {{ $user->nama_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Divisi Inisiator -->
                    <div class="col-md-6">
                        <label for="divisipembuat" class="form-label fw-bold">Divisi Inisiator*</label>
                        <input type="text" id="divisipembuat" name="divisipembuat"class="form-control"placeholder="Divisi" value="{{ old('divisipembuat') }}"readonly>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Email Inisiator -->
                    <div class="col-md-6">
                        <label for="emailpembuat" class="form-label fw-bold">Email Inisiator*</label>
                        <input type="email" id="emailpembuat" name="emailpembuat" class="form-control"placeholder="Email" value="{{ old('emailpembuat') }}"readonly>
                    </div>
                </div>

                <hr>
                <!-- Penerima -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="penerima" class="form-label fw-bold">Nama Penerima*</label>
                        <select id="penerima" name="penerima" class="form-select">
                            <option value="">Pilih Penerima</option>
                            @foreach($data as $user)
                                <option value="{{ $user->id }}" data-email="{{ $user->email }}" data-divisi="{{ $user->divisi }}">
                                    {{ $user->nama_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="divisipenerima" class="form-label fw-bold">Divisi Penerima*</label>
                        <input placeholder="Divisi" type="text" name="divisipenerima" id="divisipenerima" class="form-control" value="{{ old('divisipenerima') }}" readonly>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <!-- Email Inisiator -->
                    <div class="col-md-6">
                        <label for="emailpenerima" class="form-label fw-bold">Email Penerima*</label>
                        <input type="email" id="emailpenerima" name="emailpenerima" class="form-control" placeholder="Email" value="{{ old('emailpenerima') }}"readonly>
                    </div>
                </div>
                <br>

                <!-- Tanda Tangan -->
                <div class="row mb-3">
                    <label for="signature" class="col-sm-2 col-form-label fw-bold">Tanda Tangan* (Pilih Opsi)</label>
                    <div class="col-sm-10">
                        <!-- Pilihan Opsi -->
                        <div class="row mb-3 mt-1">
                            <div class="form-check mb-2">
                                <div class="col-sm-10">
                                    <input class="form-check-input" type="radio" name="signature_option" id="option1" value="1" checked>
                                    <label class="form-check-label" for="option1"><strong>1. Tanda tangan langsung</strong></label>
                                </div>
                            </div>
                            <div class="form-check">
                                <div class="col-sm-10">
                                <input class="form-check-input" type="radio" name="signature_option" id="option2" value="2">
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
                            <input type="hidden" name="signature" id="signature">
                        </div>

                        <!-- Divider -->
                        <div class="border-top my-4"></div>

                        <!-- Opsi untuk Mengunggah Tanda Tangan -->
                        <div id="option2-container" class="mb-3 border p-3 rounded d-none" style="background-color: #f8f9fa;">
                            <p class="fw-bold">Opsi 2: Unggah file tanda tangan</p>
                            <input type="file" name="signature_file" id="signature-file" class="form-control">
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

            <div class="mb-3">
                <label class="form-label fw-bold">CC Email</label>
                <div id="cc-email-container" style="max-width: 400px;">
                    <div class="input-group mb-2">
                        <input type="email" name="cc_email[]" class="form-control" style="width: 70%;" placeholder="Masukkan CC Email">
                        <button type="button" class="btn btn-outline-primary add-cc-email" style="width: 10%;">+</button>
                    </div>
                </div>
            </div>
{{--
            <div class="mb-3">
                <label for="statusppk" class="form-label"><strong>Status PPK</strong></label>
                <select name="statusppk" class="form-select">
                    <option value="">--Pilih Status--</option>
                    @foreach ($status as $s)
                        <option value="{{ $s->nama_statusppk }}" {{ old('statusppk') == $s->nama_statusppk ? 'selected' : '' }}>
                            {{ $s->nama_statusppk }}
                        </option>
                    @endforeach
                </select>
            </div> --}}

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route('ppk.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Save <i class="ri-save-3-fill"></i></button>
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

        // Add more CC email input
        $('.add-cc-email').click(function () {
            $('#cc-email-container').append(`
                <div class="input-group mb-2">
                    <input type="email" name="cc_email[]" class="form-control" placeholder="Masukkan CC Email">
                    <button type="button" class="btn btn-outline-warning remove-cc-email">-</button>
                </div>
            `);
        });

        // Remove CC email input
        $(document).on('click', '.remove-cc-email', function () {
            $(this).closest('.input-group').remove();
        });

        // Menyimpan tanda tangan sebagai data URL dalam input hidden saat form disubmit
        document.querySelector('form').addEventListener('submit', function (e) {
            if (!signaturePad.isEmpty()) {
                // Jika pengguna menggambar di canvas, simpan hasilnya ke input hidden
                const signatureDataUrl = signaturePad.toDataURL();
                document.getElementById('signature').value = signatureDataUrl;
            } else if (document.getElementById("signature-file").files.length === 0) {
                // Jika tidak ada tanda tangan di canvas dan tidak ada file diunggah, tampilkan peringatan
                alert("Silakan buat tanda tangan di canvas atau unggah file tanda tangan.");
                e.preventDefault();
            }
        });

        // Resize event
        window.addEventListener('resize', function () {
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            ctx.scale(ratio, ratio);
        });

        // Mengisi data pembuat
        document.getElementById("pembuat").addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById("emailpembuat").value = selectedOption.getAttribute("data-email");
            document.getElementById("divisipembuat").value = selectedOption.getAttribute("data-divisi");
        });

        // Mengisi data penerima
        document.getElementById("penerima").addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById("emailpenerima").value = selectedOption.getAttribute("data-email");
            document.getElementById("divisipenerima").value = selectedOption.getAttribute("data-divisi");
        });

        // Preview image sebelum diunggah
        document.getElementById("evidence").addEventListener("change", function () {
            const file = this.files[0];
            if (file && file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("evidencePreview").src = e.target.result;
                    document.getElementById("evidencePreview").style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById("evidencePreview").style.display = 'none';
            }
        });
    });
</script>

@endsection
