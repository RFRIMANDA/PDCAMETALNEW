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
                <textarea placeholder="Masukan identifikasi" name="identifikasi" class="form-control" id="identifikasi" rows="7">{{ old('identifikasi', $ppk->identifikasi) }}</textarea>
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
                <textarea name="penanggulangan" class="form-control" placeholder="Masukkan tindakan penanggulangan" rows="7">{{ old('penanggulangan', $ppk->penanggulangan) }}</textarea>
            </div>

            <!-- Target Tanggal Penanggulangan -->
            <div class="mb-3">
                <label for="tgl_penanggulangan" class="form-label fw-bold">Target Tanggal Penanggulangan</label>
                <input type="date" name="tgl_penanggulangan" class="form-control" value="{{ old('tgl_penanggulangan', $ppk->tgl_penanggulangan) }}">
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
                <div id="pic1-other" class="pic1-other" style="display: none;">
                    <textarea name="pic1_other" id="pic1_other" class="form-control" placeholder="Silahkan masukan PIC diluar option">{{ old('pic1_other', $ppk->pic1_other) }} </textarea>
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
        <hr>
    <!-- Pencegahan -->
    <div class="mb-3">
        <label for="pencegahan" class="form-label fw-bold">Pencegahan</label>
        <textarea name="pencegahan" class="form-control" placeholder="" cols="50" rows="7">{{ old('pencegahan') ?? $ppk->pencegahan }}</textarea>
    </div>

    <!-- Target Tanggal Pencegahan -->
    <div class="mb-3">
        <label for="tgl_pencegahan" class="form-label fw-bold">Target Tanggal Pencegahan</label>
        <input type="date" name="tgl_pencegahan" class="form-control" value="{{ old('tgl_pencegahan', $ppk->tgl_pencegahan) }}">
    </div>

    <div class="mb-3" id="pic2-dropdown">
        <label class="form-label fw-bold">Pilih PIC 2</label>

        <!-- Checkbox untuk memilih antara PIC 2 atau PIC Other -->
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="use-pic2" name="use_pic2" value="1"
                   {{ old('use_pic2', $ppk->use_pic2) ? 'checked' : '' }}>
            <label class="form-check-label" for="use-pic2">Pilih PIC 2</label>
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
            <textarea name="pic2_other" id="pic2_other" class="form-control">{{ old('pic2_other', $ppk->pic2_other) }}</textarea>
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



        <script>
            // Tambah dropdown PIC 1
        document.querySelector('.add-pic[data-target="pic1-container"]').addEventListener('click', function() {
            const container = document.getElementById('pic1-container');
            const newPic = document.createElement('div');
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
            container.appendChild(newPic);
        });

        // Hapus dropdown PIC 1
        document.getElementById('pic1-container').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-pic')) {
                e.target.closest('.input-group').remove();
            }
        });
        // Tambah dropdown PIC 2
        document.querySelector('.add-pic[data-target="pic2-container"]').addEventListener('click', function() {
            const container = document.getElementById('pic2-container');
            const newPic = document.createElement('div');
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
            container.appendChild(newPic);
        });

        // Hapus dropdown PIC 2
        document.getElementById('pic2-container').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-pic')) {
                e.target.closest('.input-group').remove();
            }
        });
        </script>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <a href="{{ route('ppk.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Update <i class="ri-save-3-fill"></i></button>
        </div>

    </div>
</div>
@endsection
