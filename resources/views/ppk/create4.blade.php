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

        <form method="POST" action="{{ route('ppk.store4') }}" enctype="multipart/form-data">
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

            <div class="row mb-3">
                <label for="catatan" class="col-sm-2 col-form-label"><strong>Catatan</strong></label>
                <div class="col-sm-10">
                    <textarea name="catatan" class="form-control" placeholder="Masukkan Catatan">{{ old('catatan') }}</textarea>
                </div>
            </div>


            <div class="row mb-3">
                <label for="tgl_verif" class="col-sm-2 col-form-label"><strong>Tanggal Verifikasi</strong></label>
                <div class="col-sm-10">
                    <input type="date" name="tgl_verif" class="form-control" value="{{ old('tgl_verif') }}">
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
