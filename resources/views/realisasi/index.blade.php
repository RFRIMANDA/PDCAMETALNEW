@extends('layouts.main')

@section('content')

<div class="container">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <h1 class="card-title">Track Record: <br>{{$tindak}}</h1>
    <hr>

    <style>
        .form-control {
            min-height: 40px; /* Untuk input biasa */
        }

        textarea.form-control {
            height: auto;
            min-height: 100px; /* Minimal tinggi untuk textarea */
            resize: vertical; /* User bisa menyesuaikan tinggi jika diinginkan */
        }
    </style>

    <!-- Form untuk Menambahkan Realisasi Baru -->
    <form action="{{ route('realisasi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_tindakan" value="{{ $id }}" required>

        <div class="row">
            {{-- AKTIVITY --}}
            <div class="col-md-4 col-sm-12 mb-3">
                <label for="nama_realisasi"><strong>Nama Activity</strong></label>
                <textarea name="nama_realisasi[]" class="form-control" rows="3" placeholder="Masukan Aktivitas" required></textarea>
            </div>

            {{-- PIC --}}
            <div class="col-md-4 col-sm-12 mb-3">
                <label for="target"><strong>PIC</strong></label>
                <textarea name="target[]" class="form-control" rows="3" placeholder="Masukan Nama PIC" required></textarea>
            </div>

            {{-- DESC --}}
            <div class="col-md-4 col-sm-12 mb-3">
                <label for="desc"><strong>Noted</strong></label>
                <textarea name="desc[]" class="form-control" rows="3" placeholder="Masukan Catatan"></textarea>
            </div>

        </div>

        <div class="row">
            {{-- TANGGAL PENYELESAIAN --}}
            <div class="col-md-3 col-sm-12 mb-3">
                <label for="tgl_realisasi"><strong>Tanggal Penyelesaian</strong></label>
                <input type="date" name="tgl_realisasi[]" class="form-control" required>
            </div>

            {{-- PERSENTASE % --}}
            <div class="col-md-3 col-sm-12 mb-3">
                <label for="presentase"><strong>Persentase</strong></label>
                <input type="number" name="presentase[]" class="form-control" placeholder="%" step="0.01">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add Activity
                </button>
            </div>
        </div>
    </form>


    <h1 class="card-title">PIC: {{$pic}}<br><hr>Target Tanggal: {{$deadline}}</h1>


    <!-- Tabel Data Realisasi -->
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Activity</th>
                <th scope="col">PIC</th>
                <th scope="col">Noted</th>
                <th scope="col">Tanggal Penyelesaian</th>
                <th scope="col">Persentase</th>
                <th scope="col">Action</th> <!-- Kolom untuk tindakan -->
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1; // Inisialisasi variabel untuk nomor urut
            @endphp

            @foreach ($realisasiList as $realisasi)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $realisasi->nama_realisasi ?? '-' }}</td>
                <td>{{ $realisasi->target ?? '-' }}</td>
                <td>{{ $realisasi->desc ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($realisasi->tgl_realisasi)->format('d-m-Y') ?? '-' }}</td>
                <td>{{ $realisasi->presentase ?? '-' }}%</td>
                <td>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#basicModal{{ $realisasi->id }}">
                        <i class="bx bx-edit"></i>
                    </button>

                <!-- Modal untuk Edit Data -->
                <div class="modal fade" id="basicModal{{ $realisasi->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Activity</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('realisasi.update', $realisasi->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="nama_realisasi" class="form-label">Nama Activity</label>
                                        <textarea name="nama_realisasi" class="form-control" required>{{ $realisasi->nama_realisasi }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="target" class="form-label">PIC</label>
                                        <textarea name="target" class="form-control" required>{{ $realisasi->target }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tgl_realisasi" class="form-label">Tanggal Penyelesaian</label>
                                        <input type="date" name="tgl_realisasi" class="form-control" required value="{{ \Carbon\Carbon::parse($realisasi->tgl_realisasi)->format('Y-m-d') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="desc" class="form-label">Noted</label>
                                        <textarea name="desc" class="form-control">{{ $realisasi->desc }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="presentase" class="form-label">Presentase</label>
                                        <input type="number" name="presentase" class="form-control" value="{{ $realisasi->presentase }}">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

    <!-- Form untuk Mengupdate Status -->
    <form action="{{ route('realisasi.update', $realisasiList->first()->id ?? 0) }}" method="POST" class="mt-4">
        @csrf
        @method('PUT')

        <label for="nilai_akhir"><strong> Nilai Akhir Persentase: {{ number_format($realisasiList->first()->nilai_akhir ?? 0, 0) }}%</strong></label>

        <div class="row">
            <div class="col-md-4">
                <br>
                <label for="status"><strong>Status</strong></label>
                <select name="status" class="form-control">
                    <option value="">--Pilih Status--</option>
                    <option value="ON PROGRES" {{ old('status', $realisasiList->first()->status ?? '') == 'ON PROGRES' ? 'selected' : '' }}>ON PROGRES</option>
                    <option value="CLOSE" {{ old('status', $realisasiList->first()->status ?? '') == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                </select>
            </div>
            <div class="col-md-4">
                <br>
                <button type="submit" class="btn btn-success mt-4">Update</button>
            </div>
        </div>
    </form>

    <div class="mt-3">
        <a class="btn btn-danger" href="{{ route('riskregister.tablerisk', $divisi) }}" title="Back">
            <i class="ri-arrow-go-back-line"></i>
        </a>
    </div>
</div>
@endsection
