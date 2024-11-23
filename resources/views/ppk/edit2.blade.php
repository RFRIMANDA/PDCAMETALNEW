@extends('layouts.main')

@section('content')

<div class="card shadow-lg border-0">
    <div class="card-body">
        <h5 class="card-title text-center text-uppercase fw-bold text-primary">Proses Peningkatan Kinerja - Edit</h5>
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
                <label for="identifikasi" class="form-label fw-bold">Identifikasi</label>
                <textarea placeholder="Masukan identifikasi" name="identifikasi" class="form-control" id="identifikasi" rows="3">{{ old('identifikasi', $ppk->identifikasi) }}</textarea>
            </div>

            <!-- Penanggulangan -->
            <div class="mb-3">
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
                <select name="pic1" class="form-select">
                    <option value="">Pilih PIC</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('pic1', $ppk->pic1) == $user->id ? 'selected' : '' }}>{{ $user->nama_user }}</option>
                    @endforeach
                </select>
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
                <select name="pic2" class="form-select">
                    <option value="">Pilih PIC</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('pic2', $ppk->pic2) == $user->id ? 'selected' : '' }}>{{ $user->nama_user }}</option>
                    @endforeach
                </select>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Update <i class="ri-save-3-fill"></i></button>
            </div>
        </form>
    </div>
</div>

@endsection
