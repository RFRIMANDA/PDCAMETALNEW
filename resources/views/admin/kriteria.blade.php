@extends('layouts.main')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Kelola Kriteria</h5>
                    <a href="{{ route('admin.kriteriacreate') }}" title="Buat Kriteria" class="btn btn-sm btn-primary mb-3">
                        Add <i class="bi bi-plus-circle-fill"></i>
                    </a>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.kriteria') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="nama_kriteria" class="form-control" placeholder="Cari berdasarkan Nama Kriteria" value="{{ request('nama_kriteria') }}">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="desc_kriteria" class="form-control" placeholder="Cari berdasarkan Deskripsi" value="{{ request('desc_kriteria') }}">
                            </div>
                        </div>
                    </form>
                    <!-- End Filter Form -->

                    <!-- Tampilkan pesan sukses jika ada -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('danger'))
                        <div class="alert alert-danger">
                            {{ session('danger') }}
                        </div>
                    @endif

                    <!-- Kriteria Data Table -->
                    <table class="table table-striped" style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 80px;">No</th>
                                <th scope="col">Nama Kriteria</th>
                                <th scope="col">Deskripsi Kriteria</th>
                                <th scope="col">Nilai Kriteria</th>
                                <th scope="col" style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kriteria as $k)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $k->nama_kriteria }}</td>

                                    <!-- Tampilkan Deskripsi dan Nilai Kriteria Secara Berpasangan -->
                                    <td colspan="2">
                                        <table class="table table-bordered">
                                            <tbody>
                                                @php
                                                    // Decode JSON menjadi array
                                                    $descArray = json_decode($k->desc_kriteria, true) ?? [];
                                                    $nilaiArray = json_decode($k->nilai_kriteria, true) ?? [];
                                                @endphp

                                                <!-- Iterasi dan tampilkan setiap pasangan deskripsi dan nilai -->
                                                @foreach ($descArray as $index => $desc)
                                                    <tr>
                                                        <td>{{ $desc }}</td>
                                                        <td>{{ $nilaiArray[$index] ?? '' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.kriteriaedit', $k->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.kriteriadestroy', $k->id) }}" method="POST" style="display:inline;" title="Delete" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="ri ri-delete-bin-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data kriteria</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- End Kriteria Data Table -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
