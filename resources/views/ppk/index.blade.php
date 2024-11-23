@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Proses Peningkatan Kinerja</h1>

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

    @if($ppks->isEmpty())
        <div class="alert alert-warning">Tidak ada data yang tersedia.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">No</th> <!-- Mengatur lebar kolom No dan meratakan teks ke tengah -->
                    <th style="width: 60px; text-align: center;">Action Create</th> <!-- Mengatur lebar kolom Action dan meratakan teks ke tengah -->
                    <th style="width: 60px; text-align: center;">Action Edit</th> <!-- Mengatur lebar kolom Action dan meratakan teks ke tengah -->
                    <th style="width: 60px; text-align: center;">Detail</th> <!-- Mengatur lebar kolom Action dan meratakan teks ke tengah -->
                </tr>
            </thead>
            <tbody>
                @foreach ($ppks as $ppk)
                    <tr>
                        <td style="text-align: center;">
                            <a href="{{ route('ppk.export', $ppk->id) }}" title="Export to Excel">
                            {{ $ppk->nomor_surat ?? 'Tidak ada nomor surat' }}
                        </a>
                        </td>

                        {{-- <td>{{ $ppk->judul }}</td> --}}
                        <td style="text-align: center;"> <!-- Meratakan teks ke tengah -->
                            <button type="button" title="Track Record" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $ppk->id }}">
                                <i class="bi bi-eye-fill"></i>
                            </button>

                            {{-- <a href="{{ route('ppk.create', $ppk->id) }}" class="btn btn-secondary btn-sm" title="Form PPK Judul">
                                <i class="bi bi-pencil-fill"></i>
                            </a> --}}

                            <a href="{{ route('ppk.create2', $ppk->id) }}" class="btn btn-info btn-sm" title="Form PPK Identifikasi">
                                <i class="bi bi-pencil-fill"></i>
                            </a>

                            <a href="{{ route('ppk.create3', $ppk->id) }}" class="btn btn-danger btn-sm" title="Form PPK Verifikasi">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('ppk.edit', $ppk->id) }}" class="btn btn-secondary btn-sm" title="Edit Judul PPK">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="{{ route('ppk.edit2', $ppk->id) }}" class="btn btn-info btn-sm" title="Edit Identifikasi">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="{{ route('ppk.edit3', $ppk->id) }}" class="btn btn-danger btn-sm" title="Edit Verifikasi">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </td>
                        <td>

                            <a href="{{ route('ppk.detail', $ppk->id) }}" class="btn btn-dark btn-sm" title="Detail">
                                <i class="bx bxs-detail"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

<!-- Modal Detail Data PPK -->
@foreach ($ppks as $ppk)
    <div class="modal fade" id="detailModal{{ $ppk->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $ppk->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="card-title" id="detailModalLabel{{ $ppk->id }}">Track Record Proses Peningkatan Kinerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

</div>

<style>
.activity {
    margin-top: 20px; /* Memberikan jarak antara tabel dan aktivitas */
}

.activity-item {
    padding: 10px; /* Memberikan padding pada setiap item aktivitas */
    flex: 0 1 auto; /* Menjaga lebar item agar tidak menyusut */
    min-width: 150px; /* Menetapkan lebar minimum untuk setiap item aktivitas */
}

.activity-content {
    margin-left: 10px; /* Memberikan jarak antara ikon dan konten aktivitas */
}
</style>
@endsection
