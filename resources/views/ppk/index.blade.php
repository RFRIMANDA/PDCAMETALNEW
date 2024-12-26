@extends('layouts.main')

@section('content')
<div class="container">

    @if($ppks->isEmpty())
        <div class="alert alert-warning">Tidak ada data yang tersedia.</div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal" style="font-weight: 500; font-size: 12px; padding: 6px 12px;">
                <i class="fa fa-filter" style="font-size: 14px;"></i> Filter Options
            </button>
            <a href="/formppk" class="btn btn-success" style="font-weight: 500; font-size: 12px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa fa-plus" style="font-size: 14px;"></i> Proses Peningkatan Kinerja
            </a>
        </div>
    @else

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
                PROSES PENINGKATAN KINERJA
            </h5>
        </div>
    </div>

    <!-- Form Filter Tanggal -->
    <form method="GET" action="{{ route('ppk.index') }}" class="mb-4">
        <div class="d-flex justify-content-between align-items-center mt-3">
            <!-- Tombol Filter -->
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal" style="font-weight: 500; font-size: 12px; padding: 6px 12px;">
                <i class="fa fa-filter" style="font-size: 14px;"></i> Filter Options
            </button>

            <a href="/formppk" class="btn btn-success" style="font-weight: 500; font-size: 12px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa fa-plus" style="font-size: 14px;"></i> Proses Peningkatan Kinerja
            </a>
        </div>
    </form>
    <br>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No. Proses Peningkatan Kinerja</th> <!-- Mengatur lebar kolom No dan meratakan teks ke tengah -->
                    <th style="width: 60px; text-align: center;">Action Create</th> <!-- Mengatur lebar kolom Action dan meratakan teks ke tengah -->
                    <th style="width: 100px; text-align: center;">Action Edit</th> <!-- Mengatur lebar kolom Action dan meratakan teks ke tengah -->
                </tr>
            </thead>
                <tbody>
                    @foreach ($ppks as $ppk)
                <tr>
                    <td style="text-align: center;">
                        <a href="{{ route('ppk.pdf', $ppk->id) }}" title="Export to PDF">
                            {{ $ppk->nomor_surat ?? 'Tidak ada nomor surat' }}
                        </a>
                    </td>
                <td style="text-align: center; display: flex; justify-content: center; align-items: center; gap: 10px;">
                    @if ($ppk->statusppk === 'CANCEL')
                        <!-- Tampilkan teks CANCEL -->
                        <span class="text-danger fw-bold">CANCEL <i class="bi bi-x-circle"></i></span>
                    @elseif ($ppk->statusppk === 'IDENTIFIKASI ULANG')
                        <!-- Tampilkan tombol edit untuk IDENTIFIKASI ULANG -->
                        <a href="{{ route('ppk.edit2', $ppk->id) }}" class="btn btn-secondary btn-sm" title="Edit Identifikasi Ulang">
                            <i class="bi bi-pencil"> IDENTIFIKASI ULANG</i>
                        </a>
                    @elseif ($ppk->statusppk === 'CLOSE (Tidak Efektif)')
                    <!-- Tampilkan teks CLOSE (Tidak Efektif) -->
                    <span class="text-muted fw-bold">CLOSE (Tidak Efektif) <i class="bi bi-slash-circle"></i></span>
                    @else
                        <!-- Identifikasi -->
                        @if ($ppk->formppk2 && is_null($ppk->formppk2->signaturepenerima))
                            <a href="{{ route('ppk.create2', $ppk->id) }}" class="btn btn-warning btn-sm" title="Form PPK Identifikasi">
                                <i class="bi bi-bell"></i>
                            </a>
                        @elseif ($ppk->formppk2 && !is_null($ppk->formppk2->signaturepenerima))

                            @if ((empty($ppk->formppk2->pencegahan) || empty($ppk->formppk2->penanggulangan))
                                && !is_null($ppk->formppk2->signaturepenerima)
                                || empty($ppk->formppk2->tgl_penanggulangan)
                                || empty($ppk->formppk2->tgl_pencegahan))
                                <a href="{{ route('ppk.edit2', $ppk->id) }}" class="btn btn-danger btn-sm" title="Edit Identifikasi">
                                    <i class="bi bi-bell"></i>
                                </a>
                            @endif

                            <!-- Verifikasi -->
                            @if ($ppk->formppk3)
                                @if (is_null($ppk->formppk3->verifikasi) && !empty($ppk->formppk2->pencegahan) && !empty($ppk->formppk2->penanggulangan) && !empty($ppk->formppk2->identifikasi))
                                    @if ($ppk->pembuat == auth()->id())
                                        <!-- Show Create3 button for pembuat when all fields in formppk2 are filled -->
                                        @if ($ppk->formppk2->identifikasi && $ppk->formppk2->penanggulangan && $ppk->formppk2->pencegahan)
                                            <a href="{{ route('ppk.create3', $ppk->id) }}" class="btn btn-success btn-sm" title="Form PPK Verifikasi">
                                                <i class="bi bi-bell"></i>
                                            </a>
                                        @else
                                            <!-- Show WAITING message if formppk2 fields are not complete -->
                                            <span class="text-warning fw-bold">WAITING <i class="bi bi-hourglass-split"></i></span>
                                        @endif
                                    @else
                                        <!-- Show WAITING for penerima if verifikasi is null -->
                                        <span class="text-warning fw-bold">WAITING <i class="bi bi-hourglass-split"></i></span>
                                    @endif
                                @elseif (!is_null($ppk->formppk3->verifikasi) && !empty($ppk->formppk2->penanggulangan) && !empty($ppk->formppk2->pencegahan))
                                    <!-- Show VERIFIED if verifikasi is not null and other fields are filled -->
                                    <span class="text-success fw-bold">VERIFIED <i class="bi bi-check-circle-fill"></i></span>
                                @endif
                            @endif
                        @endif
                    @endif
                </td>

                <td style="text-align: center;">
                    @if($ppk->statusppk == 'CLOSE (Tidak Efektif)')
                        <!-- Menampilkan teks CLOSE (Tidak Efektif) jika status adalah CLOSE -->
                        <span class="text-muted fw-bold">CLOSE (Tidak Efektif) <i class="bi bi-slash-circle"></i></span>
                    @else
                        @if($ppk->statusppk != 'CANCEL')
                            @if(auth()->id() == $ppk->pembuat)
                                <a href="{{ route('ppk.edit', $ppk->id) }}" class="btn btn-primary btn-sm" title="Edit Judul PPK">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                            @endif

                            @if (
                                !is_null($ppk->formppk2->identifikasi) &&
                                !is_null($ppk->formppk2->signaturepenerima) &&
                                !is_null($ppk->formppk2->penanggulangan) &&
                                !is_null($ppk->formppk2->pencegahan) &&
                                !is_null($ppk->formppk2->tgl_penanggulangan) &&
                                !is_null($ppk->formppk2->tgl_pencegahan)
                            )
                                <a href="{{ route('ppk.edit2', $ppk->id) }}" class="btn btn-danger btn-sm" title="Edit Identifikasi">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                            @endif

                            @if(auth()->id() == $ppk->pembuat)
                                @php
                                    $updated_at = \Carbon\Carbon::parse($ppk->formppk3->updated_at);
                                    $isExpired = $updated_at->diffInMinutes(now()) >= 1;
                                @endphp

                                @if ($isExpired && $ppk->statusppk != 'IDENTIFIKASI ULANG')  <!-- Add condition to exclude IDENTIFIKASI ULANG -->
                                    <a href="{{ route('ppk.edit3', $ppk->id) }}" class="btn btn-success btn-sm" title="Edit Verifikasi">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                @else
                                    <!-- Show WAITING message if less than 1 minute or status IDENTIFIKASI ULANG -->
                                    @if($ppk->statusppk == 'IDENTIFIKASI ULANG')
                                        <!-- Do not show the button for IDENTIFIKASI ULANG status -->
                                    @else
                                        <span class="text-warning fw-bold">WAITING <i class="bi bi-hourglass-split"></i></span>
                                    @endif
                                @endif
                            @endif
                        @else
                            <span class="text-danger fw-bold">CANCEL <i class="bi bi-x-circle"></i></span>
                        @endif
                    @endif
                </td>

                    </tr>
                        @endforeach

                        <script>
                            @foreach ($ppks as $ppk)
                                @if ($ppk->formppk2 && !is_null($ppk->formppk2->pencegahan) && !is_null($ppk->formppk2->penanggulangan))
                                    // Simpan timestamp di localStorage jika belum ada
                                    if (!localStorage.getItem('verifikasiTimestamp-{{ $ppk->id }}')) {
                                        localStorage.setItem('verifikasiTimestamp-{{ $ppk->id }}', Date.now());
                                    }

                                    // Periksa apakah 10 detik sudah berlalu
                                    setInterval(function () {
                                        var savedTimestamp = localStorage.getItem('verifikasiTimestamp-{{ $ppk->id }}');
                                        if (savedTimestamp && (Date.now() - savedTimestamp >= 1000)) {
                                            var verifikasiContainer = document.getElementById('verifikasi-container-{{ $ppk->id }}');
                                            if (verifikasiContainer) {
                                                verifikasiContainer.style.display = 'block';
                                            }
                                        }
                                    }, 1000); // Periksa setiap 1 detik
                                @endif
                            @endforeach
                        </script>
                    </tbody>
                </table>
            @endif

                    {{-- Modal --}}
                    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="GET" action="{{ route('ppk.index') }}">
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

                                            <!-- Filter Semester -->
                                            <div class="col-md-4">
                                                <label for="semester" class="form-label"><strong>Semester</strong></label>
                                                <select id="semester" name="semester" class="form-select">
                                                    <option value="">Pilih Semester</option>
                                                    <option value="SEM 1" {{ request('semester') == 'SEM 1' ? 'selected' : '' }}>SEM 1</option>
                                                    <option value="SEM 2" {{ request('semester') == 'SEM 2' ? 'selected' : '' }}>SEM 2</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <!-- Filter Nomor PPK -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="keyword" class="form-label fw-bold">Cari Nomor PPK</label>
                                                    <textarea name="keyword" id="keyword" class="form-control" placeholder="Masukkan nomor PPK" rows="3">{{ request('keyword') }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Filter Status -->
                                            <div class="col-md-4">
                                                <label for="status" class="form-label"><strong>Status</strong></label>
                                                <select id="status" name="status" class="form-select">
                                                    <option value="">Pilih Status</option>
                                                    <option value="VERIFIED" {{ request('status') == 'VERIFIED' ? 'selected' : '' }}>VERIFIED</option>
                                                    <option value="WAITING" {{ request('status') == 'WAITING' ? 'selected' : '' }}>WAITING</option>
                                                </select>
                                            </div>
                                        </div>


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
