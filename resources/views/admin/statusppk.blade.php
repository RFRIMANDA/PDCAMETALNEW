@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Kelola Status PPK</h5>

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

                <!-- Tombol untuk membuka modal tambah -->
                <button class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                    Status PPK <i class="bi bi-plus-circle-fill"></i>
                </button>

                <!-- Tabel Data Status PPK -->
                <table class="table table-striped" style="font-size: 15px;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 80px;">No</th>
                            <th scope="col">Nama Status</th>
                            <th scope="col" style="width: 150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $k)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $k->nama_statusppk }}</td>

                                <td>
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editModal-{{ $k->id }}" title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <!-- Form Delete -->
                                    <form action="{{ route('statusppk.destroy', $k->id) }}" method="POST" style="display:inline;" title="Delete" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="ri ri-delete-bin-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal-{{ $k->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $k->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel-{{ $k->id }}">Edit Status PPK</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('statusppk.update', $k->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="nama_statusppk" class="form-label">Nama Status</label>
                                                    <input type="text" class="form-control" id="nama_statusppk" name="nama_statusppk" value="{{ $k->statusppk }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal Edit -->
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada Status PPK</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- End Tabel Data Status PPK -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Tambah Status PPK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.statusppk.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_statusppk" class="form-label">Nama Status</label>
                        <input type="text" class="form-control" id="nama_statusppk" name="nama_statusppk" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal Tambah -->

@endsection
