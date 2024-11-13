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

            <div class="row mb-3">
                <label for="penanggulangan" class="col-sm-2 col-form-label"><strong>Penanggulangan</strong></label>
                <div class="col-sm-10">
                    <textarea name="penanggulangan" class="form-control" placeholder="Masukkan penanggulangan">{{ old('penanggulangan') }}</textarea>
                </div>
            </div>

            <!-- Target PIC Penanggulangan-->
            <div class="row mb-3">
                <label for="pic1" class="col-sm-2 col-form-label"><strong>PIC Penanggulangan</strong></label>
                <div class="col-sm-7">
                    <select name="pic1" class="form-select" required>
                        <option value="">Pilih PIC</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('pic1') == $user->id ? 'selected' : '' }}>
                                {{ $user->nama_user }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="pencegahan" class="col-sm-2 col-form-label"><strong>Pencegahan</strong></label>
                <div class="col-sm-10">
                    <textarea name="pencegahan" class="form-control" placeholder="Masukkan pencegahan">{{ old('pencegahan') }}</textarea>
                </div>
            </div>

            <!-- Target PIC Pencegahan-->
            <div class="row mb-3">
                <label for="pic2" class="col-sm-2 col-form-label"><strong>PIC Pencegahan</strong></label>
                <div class="col-sm-7">
                    <select name="pic2" class="form-select" required>
                        <option value="">Pilih PIC</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('pic2') == $user->id ? 'selected' : '' }}>
                                {{ $user->nama_user }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="target_tgl" class="col-sm-2 col-form-label"><strong>Target Tanggal</strong></label>
                <div class="col-sm-10">
                    <input type="date" name="target_tgl" class="form-control" value="{{ old('target_tgl') }}">
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
