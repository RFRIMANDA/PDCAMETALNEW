@extends('layouts.main')

@section('content')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">PROSES PENINGKATAN KINERJA</h5>

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
            <div class="row mb-3">
                <label for="verifikasi" class="col-sm-2 col-form-label"><strong>Verifikasi</strong></label>
                <div class="col-sm-10">
                    <textarea name="verifikasi" class="form-control" placeholder="Masukkan verifikasi">{{ old('verifikasi') }}</textarea>
                </div>
            </div>

            <!-- Tinjauan -->
            <div class="row mb-3">
                <label for="tinjauan" class="col-sm-2 col-form-label"><strong>Tinjauan</strong></label>
                <div class="col-sm-10">
                    <textarea name="tinjauan" class="form-control" placeholder="Masukkan tinjauan">{{ old('tinjauan') }}</textarea>
                </div>
            </div>

            <!-- Status -->
            <div class="row mb-3">
                <label for="status" class="col-sm-2 col-form-label"><strong>Status</strong></label>
                <div class="col-sm-10">
                    <select name="status" class="form-select" required>
                        <option value="">--Silahkan Pilih Status--</option>
                        <option value="TRUE" {{ old('status') == 'TRUE' ? 'selected' : '' }}>TRUE</option>
                        <option value="FALSE" {{ old('status') == 'FALSE' ? 'selected' : '' }}>FALSE</option>
                    </select>
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

@endsection
