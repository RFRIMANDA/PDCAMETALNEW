@extends('layouts.main')

@section('content')

<body>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">PROSES PENINGKATAN KINERJA</h5>

            <!-- General Form Elements -->
            <form method="POST" action="{{ route('ppk.storeformppkkedua') }}" enctype="multipart/form-data">
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

                <!-- Judul PPK -->
                <div class="row mb-3">
                    <label for="inputIdentifikasi" class="col-sm-2 col-form-label">Identifikasi</label>
                    <div class="col-sm-10">
                        <textarea name="identifikasi" class="form-control" placeholder="Masukkan Identifikasi">{{ old('identifikasi') }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="signature" class="col-sm-2 col-form-label">Tanda Tangan</label>
                    <div class="col-sm-10">
                        <!-- Opsi untuk Menggambar Tanda Tangan -->
                        <p><strong>Opsi 1:</strong> Tanda tangan langsung</p>
                        <canvas id="signature-pad" style="border: 1px solid #ccc; width: 100%; height: 200px;"></canvas>
                        <button id="clear" title="Clear" type="button" class="btn btn-secondary mt-2"><i class="bx bxs-eraser"></i> Clear</button>
                        <input type="hidden" name="signaturepenerima" id="signature">

                        <!-- Opsi untuk Mengunggah Tanda Tangan -->
                        <p class="mt-3"><strong>Opsi 2:</strong> Unggah file tanda tangan (jpg, jpeg, png)</p>
                        <input type="file" name="signature_file" id="signature-file" class="form-control" accept="image/*">
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
    });
</script>

@endsection
