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

    <style>
        .badge.bg-purple {
            background-color: #ADD8E6;
            color: rgb(0, 0, 0);
        }
        .table-wrapper {
            position: relative;
            max-height: 600px; /* Adjust height as needed */
            overflow-y: auto;
        }
        .table th {
            position: sticky;
            top: 0;
            background-color: #ffffff; /* Optional: to make sure the header has a white background */
            z-index: 1; /* Ensure the header is above the table rows */
        }

        i.fa-check {
        color: green;
        font-size: 16px;
        margin-right: 5px;
    }
    .judul {
        width: 300px; /* Atur lebar kolom sesuai kebutuhan */
    }

    .no {
        width: 30px; /* Atur lebar kolom sesuai kebutuhan */
    }

    .surat {
        width: 260px; /* Atur lebar kolom sesuai kebutuhan */
        word-wrap: break-word;
    }

    .tanggal {
        width: 150px; /* Atur lebar kolom sesuai kebutuhan */
    }

    </style>

    <!-- Form Filter Tanggal -->
    <form method="GET" action="{{ route('ppk.index2') }}" class="mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3 mt-3">
            <!-- Tombol Filter -->
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal" style="font-weight: 500; font-size: 12px; padding: 6px 12px;">
                <i class="fa fa-filter" style="font-size: 14px;"></i> Filter Options
            </button>

            <!-- Tombol Setting PPK -->
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.statusppk') }}" class="btn btn-primary" title="Setting PPK" style="font-weight: 500; font-size: 12px; padding: 6px 12px;">
                <i class="ri-settings-5-line"></i>
            </a>

            @endif
        </div>
    </form>



    @if(auth()->user()->role === 'admin')
    <div class="card">
        <div class="card text-center">
            <h5 class="card-title" style="font-size: 30px; font-weight: 700; letter-spacing: 2px;">
                    ALL PROSES PENINGKATAN KINERJA
            </h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto;">
                <div class="table-wrapper">
                    <table class="table table-striped" style="width: 300%; font-size: 10px;">
                        <thead>
                            <tr>
                                <th class="no">No</th>
                                <th class="tanggal">Tanggal Terbit</th>
                                <th class="surat">No. PPK</th>
                                <th class="judul">Nama PPK</th>
                                <th class="tanggal">Status PPK</th>
                                <th class="tanggal">Target Verifikasi</th>
                                <th class="tanggal">Departemen Inisiator</th>
                                <th class="tanggal">Pembuat / Inisiator</th>
                                <th class="tanggal">Penerima</th>
                                <th class="tanggal">Departemen Penerima</th>
                                <th class="tanggal">Alamat Email Penerima</th>
                                <th class="tanggal">Jenis Ketidaksesuaian (SISTEM)</th>
                                <th class="tanggal">Jenis Ketidaksesuaian (PROSES)</th>
                                <th class="tanggal">Jenis Ketidaksesuaian (PRODUK)</th>
                                <th class="tanggal">Jenis Ketidaksesuaian (AUDIT)</th>
                                <th class="tanggal">CC Email</th>
                                <th class="tanggal">Email Address</th>
                                <th class="tanggal">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ppks as $ppk)
                                <tr>
                                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ppk->created_at)->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('ppk.pdf', $ppk->id) }}" title="Export to PDF">
                                            {{ $ppk->nomor_surat ?? 'Tidak ada nomor surat' }}
                                        </a>
                                    <td>{{ $ppk->judul }}</td>
                                    <td>{{ $ppk->statusppk }}</td>
                                    <td>{{ $ppk->formppk3 ? \Carbon\Carbon::parse($ppk->formppk3->created_at)->format('Y-m-d') : 'Tidak Ada' }}</td>
                                    <td>{{ $ppk->divisipembuat }}</td>
                                    <td>{{ $ppk->pembuatUser->nama_user ?? 'null' }}</td>
                                    <td>{{ $ppk->penerimaUser->nama_user ?? 'null' }}</td>
                                    <td>{{ $ppk->divisipenerima }}</td>
                                    <td>{{ $ppk->emailpenerima }}</td>
                                    <td>
                                        @if(strpos($ppk->jenisketidaksesuaian, 'SISTEM') !== false)
                                            <i class="fa fa-check"></i> <!-- Menampilkan ikon ceklis jika ada 'SISTEM' -->
                                        @endif
                                    </td>

                                    <td>
                                        @if(strpos($ppk->jenisketidaksesuaian, 'PROSES') !== false)
                                            <i class="fa fa-check"></i> <!-- Menampilkan ikon ceklis jika ada 'PROSES' -->
                                        @endif
                                    </td>

                                    <td>
                                        @if(strpos($ppk->jenisketidaksesuaian, 'PRODUK') !== false)
                                            <i class="fa fa-check"></i> <!-- Menampilkan ikon ceklis jika ada 'PRODUK' -->
                                        @endif
                                    </td>

                                    <td>
                                        @if(strpos($ppk->jenisketidaksesuaian, 'AUDIT') !== false)
                                            <i class="fa fa-check"></i> <!-- Menampilkan ikon ceklis jika ada 'AUDIT' -->
                                        @endif
                                    </td>

                                    <td style="max-width: 100%; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                        {!! str_replace(',', '<br>', $ppk->cc_email) !!}
                                    </td>

                                    <td>{{ $ppk->emailpembuat }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                Edit
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('ppk.edit', $ppk->id) }}" class="dropdown-item">
                                                        <i class="bx bx-edit"></i> Evidence
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('ppk.edit2', $ppk->id) }}" class="dropdown-item">
                                                        <i class="bx bx-edit"></i> Identifikasi
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('ppk.edit3', $ppk->id) }}" class="dropdown-item">
                                                        <i class="bx bx-edit"></i> Verifikasi
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <form action="{{ route('ppk.destroy', $ppk->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                <i class="ri ri-delete-bin-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    <hr>
    <div class="card">
        <div class="card text-center">
            <h5 class="card-title" style="font-size: 20px; font-weight: 700; letter-spacing: 2px;">
                    TABLE SENDING & ACCEPTING PPK
            </h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto;">
                <div class="table-wrapper">
                    <table class="table table-striped" style="width: 100%; font-size: 10px;">
                        <thead>
                            <tr>
                                <th style="text-align: center; font-size:20px;">Sending</th>
                                <th style="text-align: center; font-size:20px;">Accepting</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ppks as $ppk)
                            <tr>
                                <!-- Menampilkan nomor_surat untuk Sending -->
                                <td>
                                    @if($ppk->pembuat == auth()->user()->id)
                                        <a href="{{ route('ppk.pdf', $ppk->id) }}" title="Export to PDF"><!-- Jika PPK dibuat oleh user -->
                                            {{ $ppk->nomor_surat }}
                                        </a>
                                    @else
                                        <!-- Kosongkan jika bukan data yang dibuat oleh user -->
                                        -
                                    @endif

                                </td>

                                <td>
                                    @if($ppk->penerima == auth()->user()->id) <!-- Jika PPK diterima oleh user -->
                                        <a href="{{ route('ppk.pdf', $ppk->id) }}" title="Export to PDF"><!-- Jika PPK dibuat oleh user -->
                                            {{ $ppk->nomor_surat }}
                                        </a>
                                    @else
                                    <!-- Kosongkan jika bukan data yang diterima oleh user -->
                                    -
                                @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
                        <div class="row mb-4">
                            <!-- Tanggal Awal -->
                            <div class="col-md-4">
                                <label for="start_date" class="form-label"><strong>Tanggal Awal</strong></label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>

                            <!-- Tanggal Akhir -->
                            <div class="col-md-4">
                                <label for="end_date" class="form-label"><strong>Tanggal Akhir</strong></label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>

                            <!-- Semester -->
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
                            <!-- Pengguna -->
                            <div class="col-md-4">
                                <label for="user" class="form-label"><strong>Pengguna</strong></label>
                                <select id="user" name="user" class="form-select">
                                    <option value="">Pilih Pengguna</option>
                                    @foreach ($userList as $id => $nama_user)
                                        <option value="{{ $id }}" {{ request('user') == $id ? 'selected' : '' }}>{{ $nama_user }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status PPK -->
                            <div class="col-md-4">
                                <label for="status" class="form-label"><strong>Status PPK</strong></label>
                                <select id="status" name="status" class="form-select">
                                    <option value="">Pilih Status</option>
                                    @foreach ($statusPpkList as $statusItem)
                                        <option value="{{ $statusItem->nama_statusppk }}"
                                            {{ request('status') == $statusItem->nama_statusppk ? 'selected' : '' }}>
                                            {{ $statusItem->nama_statusppk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nomor PPK -->
                            <div class="col-md-4">
                                <label for="keyword" class="form-label"><strong>Cari Nomor PPK</strong></label>
                                <textarea name="keyword" id="keyword" class="form-control" placeholder="Masukkan nomor PPK" rows="3">{{ request('keyword') }}</textarea>
                            </div>
                        </div>



                        </div>
                    </div>

                    <!-- Tombol Filter -->
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-warning px-4 d-flex align-items-center" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise me-2"></i> Reset
                            </button>

                            <script>
                                function resetForm() {
                                    const form = document.querySelector('form');
                                    form.reset();
                                    window.location.href = "{{ route('ppk.index2') }}";
                                }
                            </script>
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

@endsection
