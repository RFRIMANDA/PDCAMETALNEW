@extends('layouts.main')

@section('content')

<body>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">PROSES PENINGKATAN KINERJA</h5>

            <!-- General Form Elements -->
            <form method="POST" action="{{ route('ppk.store') }}" enctype="multipart/form-data">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Judul PPK -->
                <div class="row mb-3">
                    <label for="inputJudul" class="col-sm-2 col-form-label">Judul PPK</label>
                    <div class="col-sm-10">
                        <textarea name="judul" class="form-control" placeholder="Masukkan Judul PPK">{{ old('judul') }}</textarea>
                    </div>
                </div>

                <!-- Jenis Ketidaksesuaian -->
                <div class="row mb-3">
                    <label for="inputJenis" class="col-sm-2 col-form-label">Jenis Ketidaksesuaian</label>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="Sistem">
                            <label class="form-check-label">Sistem</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="Proses">
                            <label class="form-check-label">Proses</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="Produk">
                            <label class="form-check-label">Produk</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="Audit">
                            <label class="form-check-label">Audit</label>
                        </div>
                    </div>
                </div>

                <!-- Pembuat dan Divisi Pembuat -->
                <div class="row mb-3">
                    <label for="pembuat" class="col-sm-2 col-form-label">Nama Inisiator</label>
                    <div class="col-sm-10">
                        <select id="pembuat" name="pembuat" class="form-control">
                            <option value="">Pilih Pembuat</option>
                            @foreach($data as $user)
                                <option value="{{ $user->nama_user }}" data-email="{{ $user->email }}" data-divisi="{{ $user->divisi }}">
                                    {{ $user->nama_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="emailpembuat" class="col-sm-2 col-form-label">Email Inisiator</label>
                    <div class="col-sm-10">
                        <input type="email" id="emailpembuat" name="emailpembuat" class="form-control" value="{{ old('emailpembuat') }}" placeholder="Email Pembuat" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="divisipembuat" class="col-sm-2 col-form-label">Divisi Inisiator</label>
                    <div class="col-sm-10">
                        <input type="text" name="divisipembuat" id="divisipembuat" class="form-control" value="{{ old('divisipembuat') }}" placeholder="Divisi Pembuat" readonly>
                    </div>
                </div>

                <!-- Lakukan hal yang sama untuk Penerima -->
                <div class="row mb-3">
                    <label for="penerima" class="col-sm-2 col-form-label">Nama Penerima</label>
                    <div class="col-sm-10">
                        <select id="penerima" name="penerima" class="form-control">
                            <option value="">Pilih Penerima</option>
                            @foreach($data as $user)
                                <option value="{{ $user->id }}" data-email="{{ $user->email }}" data-divisi="{{ $user->divisi }}">
                                    {{ $user->nama_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="emailpenerima" class="col-sm-2 col-form-label">Email Penerima</label>
                    <div class="col-sm-10">
                        <input type="email" name="emailpenerima" id="emailpenerima" class="form-control" value="{{ old('emailpenerima') }}" placeholder="Email Penerima" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="divisipenerima" class="col-sm-2 col-form-label">Divisi Penerima</label>
                    <div class="col-sm-10">
                        <input type="text" name="divisipenerima" id="divisipenerima" class="form-control" value="{{ old('divisipenerima') }}" placeholder="Divisi Penerima" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="signature" class="col-sm-2 col-form-label">Tanda Tangan</label>
                    <div class="col-sm-10">
                        <!-- Opsi untuk Menggambar Tanda Tangan -->
                        <p><strong>Opsi 1:</strong> Tanda tangan langsung</p>
                        <canvas id="signature-pad" style="border: 1px solid #ccc; width: 100%; height: 200px;"></canvas>
                        <button id="clear" title="Clear" type="button" class="btn btn-secondary mt-2"><i class="bx bxs-eraser"></i> Clear</button>
                        <input type="hidden" name="signature" id="signature">

                        <!-- Opsi untuk Mengunggah Tanda Tangan -->
                        <p class="mt-3"><strong>Opsi 2:</strong> Unggah file tanda tangan (jpg, jpeg, png)</p>
                        <input type="file" name="signature_file" id="signature-file" class="form-control" accept="image/*">
                    </div>
                </div>


                <!-- Evidence Input with Preview -->
                <div class="row mb-3">
                    <label for="evidence" class="col-sm-2 col-form-label">Evidence</label>
                    <div class="col-sm-10">
                        <input type="file" name="evidence" id="evidence" class="form-control" accept="image/*">
                        <img id="evidencePreview" src="#" alt="Evidence Preview" style="display: none; margin-top: 10px; max-width: 200px;"/>
                    </div>
                </div>

                <!-- CC Email -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">CC Email</label>
                    <div class="col-sm-10">
                        <div id="cc-email-container">
                            <div class="input-group mb-2">
                                <input type="email" name="cc_email[]" class="form-control" placeholder="Masukkan CC Email">
                                <button type="button" class="btn btn-outline-warning add-cc-email">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Save <i class="ri-save-3-fill"></i></button>
                    </div>
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
                    <input type="email" name="cc_email[]" class="form-control" placeholder="Masukkan CC Email" required>
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
