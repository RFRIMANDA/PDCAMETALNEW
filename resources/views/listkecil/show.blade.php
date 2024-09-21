@extends('layouts.main')

@section('content')

<div class="container mt-5">
    <div class="text-center mb-4">
        <h1><span class="badge bg-warning">Input Detail Track Record Tindak Lanjut</span></h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tampilkan tindakan yang dipilih -->
    <!-- Form untuk mengedit ListKecil -->
    <form action="{{ route('listkecil.update-detail', $listKecil->id) }}" method="POST">
    @csrf
    @method('POST')

        <!-- ID List Form -->
        <input type="hidden" name="id_listform" id="id_listform" class="form-control" value="{{ $listKecil['id_listform'] ?? '' }}">

        <!-- Field Realisasi -->
        <div class="row mb-3">
            <label for="realisasi" class="col-md-3 col-form-label">Realisasi</label>
            <div class="col-md-9">
                <textarea name="realisasi" id="realisasi" class="form-control" rows="3" placeholder="Masukan Realisasi">{{ $listKecil['realisasi'] ?? '' }}</textarea>
            </div>
        </div>

        <!-- Field Tanggal Penyelesaian -->
        <div class="row mb-3">
            <label for="date" class="col-md-3 col-form-label">Tanggal Penyelesaian Tindak Lanjut</label>
            <div class="col-md-9">
                <input type="date" name="date" id="date" class="form-control" value="{{ $listKecil['date'] ?? '' }}">
            </div>
        </div>

        <!-- Field Responsible -->
        <div class="row mb-3">
            <label for="responsible" class="col-md-3 col-form-label">Responsible</label>
            <div class="col-md-9">
                <input type="text" name="responsible" id="responsible" class="form-control" value="{{ $listKecil['responsible'] ?? '' }}" placeholder="Masukan Nama Responsible">
            </div>
        </div>

        <!-- Field Accountable -->
        <div class="row mb-3">
            <label for="accountable" class="col-md-3 col-form-label">Accountable</label>
            <div class="col-md-9">
                <input type="text" name="accountable" id="accountable" class="form-control" value="{{ $listKecil['accountable'] ?? '' }}" placeholder="Masukan Nama Accountable">
            </div>
        </div>

        <!-- Field Consulted -->
        <div class="row mb-3">
            <label for="consulted" class="col-md-3 col-form-label">Consulted</label>
            <div class="col-md-9">
                <input type="text" name="consulted" id="consulted" class="form-control" value="{{ $listKecil['consulted'] ?? '' }}" placeholder="Masukan Nama Consulted">
            </div>
        </div>

        <!-- Field Informed -->
        <div class="row mb-3">
            <label for="informed" class="col-md-3 col-form-label">Informed</label>
            <div class="col-md-9">
                <input type="text" name="informed" id="informed" class="form-control" value="{{ $listKecil['informed'] ?? '' }}" placeholder="Masukan Nama Informed">
            </div>
        </div>

        <!-- Field Anum Goal -->
        <div class="row mb-3">
            <label for="anumgoal" class="col-md-3 col-form-label">Anum Goal</label>
            <div class="col-md-9">
                <select name="anumgoal" id="anumgoal" class="form-control">
                    <option value="">--Silahkan Pilih--</option>
                    <option value="Efficiency cost" {{ (isset($listKecil['anumgoal']) && $listKecil['anumgoal'] == 'Efficiency cost') ? 'selected' : '' }}>Efficiency cost</option>
                    <option value="Time" {{ (isset($listKecil['anumgoal']) && $listKecil['anumgoal'] == 'Time') ? 'selected' : '' }}>Time</option>
                    <option value="Human Resources" {{ (isset($listKecil['anumgoal']) && $listKecil['anumgoal'] == 'Human Resources') ? 'selected' : '' }}>Human Resources</option>
                </select>
            </div>
        </div>

        <!-- Field Anum Budget -->
        <div class="row mb-3">
            <label for="anumbudget" class="col-md-3 col-form-label">Anum Budget</label>
            <div class="col-md-9">
                <input type="number" name="anumbudget" id="anumbudget" class="form-control" value="{{ isset($listKecil['anumbudget']) ? $listKecil['anumbudget'] : '' }}" placeholder="Masukan Nilai IDR." min="0" step="any">
            </div>
        </div>



        <!-- Field Deskripsi -->
        <div class="row mb-3">
            <label for="desc" class="col-md-3 col-form-label">Deskripsi</label>
            <div class="col-md-9">
                <textarea name="desc" id="desc" class="form-control" placeholder="Silahkan isi jika ada deskripsi">{{ $listKecil['desc'] ?? '' }}</textarea>
            </div>
        </div>
        

        <!-- Submit Button -->
        <div class="row">
            <div class="col-md-9 offset-md-3">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
        
    </form>
</div>

@endsection
