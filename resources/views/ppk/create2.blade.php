@extends('layouts.main')

@section('content')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card shadow-lg border-0">
    <div class="card-body">
        <h5 class="card-title text-center text-uppercase fw-bold text-primary">Proses Peningkatan Kinerja</h5>
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
                <label for="identifikasi" class="form-label fw-bold">Identifikasi</label>
                <textarea placeholder="Masukan identifikasi" name="identifikasi" class="form-control" id="identifikasi" rows="3">{{ old('identifikasi', $ppk->identifikasi ?? '') }}</textarea>
            </div>
            <hr>
            <hr>

            <!-- Penanggulangan -->
            <div class="mb-3">
                <label for="penanggulangan" class="form-label fw-bold">Penanggulangan</label>
                <textarea name="penanggulangan" class="form-control" placeholder="Masukkan tindakan penanggulangan">{{ old('penanggulangan') }}</textarea>
            </div>

            <!-- Target Tanggal Penanggulangan -->
            <div class="mb-3">
                <label for="tgl_penanggulangan" class="form-label fw-bold">Target Tanggal Penanggulangan</label>
                <input type="date" name="tgl_penanggulangan" class="form-control" value="{{ old('tgl_penanggulangan') }}">
            </div>

             <!-- PIC Penanggulangan -->
             <div class="mb-3">
                <label for="pic1" class="form-label fw-bold">PIC Penanggulangan</label>
                <select name="pic1" class="form-select">
                    <option value="">Pilih PIC</option>
                    @foreach($data as $user)
                        <option value="{{ $user->id }}" {{ old('pic1') == $user->id ? 'selected' : '' }}>{{ $user->nama_user }}</option>
                    @endforeach
                </select>
            </div>
            <hr>
            <hr>

            <!-- Pencegahan -->
            <div class="mb-3">
                <label for="pencegahan" class="form-label fw-bold">Pencegahan</label>
                <textarea name="pencegahan" class="form-control" placeholder="Masukkan tindakan pencegahan">{{ old('pencegahan') }}</textarea>
            </div>

            <!-- Target Tanggal Pencegahan -->
            <div class="mb-3">
                <label for="tgl_pencegahan" class="form-label fw-bold">Target Tanggal Pencegahan</label>
                <input type="date" name="tgl_pencegahan" class="form-control" value="{{ old('tgl_pencegahan') }}">
            </div>

            <!-- PIC Pencegahan -->
            <div class="mb-3">
                <label for="pic2" class="form-label fw-bold">PIC Pencegahan</label>
                <select name="pic2" class="form-select">
                    <option value="">Pilih PIC</option>
                    @foreach($data as $user)
                        <option value="{{ $user->id }}" {{ old('pic2') == $user->id ? 'selected' : '' }}>{{ $user->nama_user }}</option>
                    @endforeach
                </select>
            </div>

             <!-- Tanda Tangan -->
            <div class="row mb-3">
                <label for="signature" class="col-sm-2 col-form-label fw-bold">Tanda Tangan</label>
                <div class="col-sm-10">
                    <!-- Opsi untuk Menggambar Tanda Tangan -->
                    <div class="mb-3">
                        <p class="fw-bold">Opsi 1: Tanda tangan langsung</p>
                        <div class="border p-3 rounded" style="background-color: #f8f9fa;">
                            <canvas id="signature-pad" style="border: 1px solid #ccc; width: 100%; height: 200px; display: block;"></canvas>
                            <button id="clear" title="Clear" type="button" class="btn btn-secondary mt-2">
                                <i class="bx bxs-eraser"></i> Clear
                            </button>
                            <input type="hidden" name="signaturepenerima" id="signature">
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-top my-4"></div>

                    <!-- Opsi untuk Mengunggah Tanda Tangan -->
                    <div class="mb-3">
                        <p class="fw-bold">Opsi 2: Unggah file tanda tangan</p>
                        <div class="border p-3 rounded" style="background-color: #f8f9fa;">
                            <input type="file" name="signaturepenerima_file" id="signature-file" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-2">Format file yang didukung: jpg, jpeg, png</small>
                        </div>
                    </div>
                </div>
            </div>


            <div class="text-end">
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

