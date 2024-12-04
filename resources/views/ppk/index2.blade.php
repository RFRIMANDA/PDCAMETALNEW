@extends('layouts.main')

@section('content')
<div class="container">

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card text-center">
        <div class="card-body">
            <h5 class="card-title" style="font-size: 25px; font-weight: 700; letter-spacing: 2px;">
                ALL PROSES PENINGKATAN KINERJA
            </h5>
        </div>
    </div>


    <!-- Form Filter Tanggal -->
    <form method="GET" action="{{ route('ppk.index2') }}" class="mb-4">
        <div class="d-flex justify-content-start gap-3 mt-3">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal" style="font-weight: 500; font-size: 12px; padding: 6px 12px;">
                <i class="fa fa-filter" style="font-size: 14px;"></i> Filter Options
            </button>
        </div>
    </form>

    @if($sendingPpks->isEmpty() && $acceptingPpks->isEmpty())
        <div class="alert alert-warning">Tidak ada data yang tersedia.</div>
    @else

    <div class="tables-container d-flex justify-content-between">
        <!-- Table Sending -->
        <div class="table-wrapper">
            <h3 class="card-title" style="text-align: center;">Sending</h3>
            @if($sendingPpks->isEmpty())
                <div class="alert alert-warning">Tidak ada data yang dikirimkan.</div>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Nomor Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($sendingPpks as $ppk)
                        <tr>
                            <td style="text-align: center;">
                                <a href="{{ route('ppk.pdf', $ppk->id) }}" title="Export to PDF">
                                    {{ $ppk->nomor_surat ?? 'Tidak ada nomor surat' }}
                                </a>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Spacer -->
        <div style="width: 30px;"></div>

        <!-- Table Accepting -->
        <div class="table-wrapper">
            <h3 class="card-title" style="text-align: center;">Accepting</h3>
            @if($acceptingPpks->isEmpty())
                <div class="alert alert-warning">Tidak ada data yang diterima.</div>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Nomor Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($acceptingPpks as $ppk)
                        <tr>
                            <td style="text-align: center;">
                                <a href="{{ route('ppk.accept', $ppk->id) }}" title="Detail Accepting">
                                    {{ $ppk->nomor_surat ?? 'Tidak ada nomor surat' }}
                                </a>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    @endif
</div>

{{-- Modal --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('ppk.index2') }}">
                    <div class="row mb-4">
                        <!-- Filter Tanggal -->
                        <div class="col-md-4">
                            <label for="start_date" class="form-label"><strong>Tanggal Awal</strong></label>
                            <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label"><strong>Tanggal Akhir</strong></label>
                            <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>

                        <!-- Filter Divisi -->
                        <div class="col-md-4">
                            <label for="semester" class="form-label"><strong>Semester</strong></label>
                            <select id="semester" name="semester" class="form-select">
                                <option value="">Pilih Semester</option>
                                <option value="SEM 1" {{ request('semester') == 'SEM 1' ? 'selected' : '' }}>SEM 1</option>
                                <option value="SEM 2" {{ request('semester') == 'SEM 2' ? 'selected' : '' }}>SEM 2</option>
                            </select>
                        </div>
                        <div class="row mb-4">
                            <!-- Filter User -->
                            <div class="col-md-4">
                                <label for="user" class="form-label"><strong>Pengguna</strong></label>
                                <select id="user" name="user" class="form-select">
                                    <option value="">Pilih Pengguna</option>
                                    @foreach ($userList as $id => $nama_user)
                                        <option value="{{ $id }}" {{ request('user') == $id ? 'selected' : '' }}>{{ $nama_user }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5">
                                    <label class="form-label fw-bold">Cari Nomor PPK</label>
                                    <textarea name="keyword" class="form-control" placeholder="Masukkan nomor PPK" rows="3">{{ request('keyword') }}</textarea>
                            </div>


                        </div>
                    </div>

                    <!-- Tombol Filter -->
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button type="reset" class="btn btn-warning px-4 d-flex align-items-center">
                                <i class="bi bi-arrow-clockwise me-2"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<style>
.tables-container {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-top: 20px;
}

.table-wrapper {
    flex: 1;
    max-width: 45%;
}
</style>
@endsection
