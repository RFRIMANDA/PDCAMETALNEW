@extends('layouts.main')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Kelola Kriteria</h5>
                    <a href="{{ route('admin.kriteriacreate') }}" title="Buat Kriteria" class="btn btn-sm btn-success mb-3">
                        Add Kriteria <i class="bi bi-plus-circle-fill"></i>
                    </a>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.kriteria') }}" class="mb-4">
                        <div class="row">
                            <!-- Dropdown untuk Nama Kriteria -->
                            <div class="col-md-4 mb-2">
                                <select name="nama_kriteria" class="form-select">
                                    <option value="">-- Pilih Nama Kriteria --</option>
                                    @foreach ($namaKriteriaList as $nama)
                                        <option value="{{ $nama }}" {{ request('nama_kriteria') == $nama ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Input Text untuk Deskripsi -->
                            <div class="col-md-4 mb-2">
                                <input type="text" name="desc_kriteria" class="form-control" placeholder="Cari berdasarkan Deskripsi" value="{{ request('desc_kriteria') }}">
                            </div>

                            <!-- Tombol Filter -->
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i>
                                </button>
                                <a href="{{ route('admin.kriteria') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                    <!-- End Filter Form -->

                    <!-- Tampilkan pesan sukses atau error -->
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
                    <table class="table table-striped table-bordered" style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 80px;">No</th>
                                <th scope="col">Nama Kriteria</th>
                                <th scope="col">Deskripsi & Nilai</th>
                                <th scope="col" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kriteria as $k)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $k->nama_kriteria }}</td>

                                    <!-- Tampilkan Deskripsi dan Nilai -->
                                    <td>
                                        <table class="table table-bordered table-sm">
                                            <tbody>
                                                @php
                                                    // Pisahkan string menggunakan tanda koma
                                                    $descArray = explode(',', $k->desc_kriteria);
                                                    $nilaiArray = explode(',', $k->nilai_kriteria);
                                                @endphp

                                                @foreach ($descArray as $index => $desc)
                                                    <tr>
                                                        <td style="width: 70%;">{{ $desc }}</td>
                                                        <td style="width: 30%;  text-align: center;">{{ $nilaiArray[$index] ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="display: flex; align-items: center; gap: 5px;">
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('admin.kriteriaedit', $k->id) }}"
                                           class="btn btn-sm btn-success"
                                           title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>

                                        <!-- Form Hapus -->
                                        <form action="{{ route('admin.kriteriadestroy', $k->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete">
                                                <i class="ri ri-delete-bin-fill"></i>
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data kriteria</td>
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
