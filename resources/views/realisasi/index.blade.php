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

<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 400px; margin: auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); padding: 20px; text-align: left; color: #333; line-height: 1.6;">
    <h1 style="font-size: 16px; color: #2c3e50; border-bottom: 2px solid #2c3e50; padding-bottom: 8px; margin-bottom: 15px;">Track Record</h1>
    <p style="font-size: 12px; color: #555;">{{$tindak}}</p>

    <div style="margin-top: 25px; border-top: 1px solid #ddd; padding-top: 15px;">
        <h2 style="font-size: 18px; color: #2c3e50;">PIC: {{$pic}}</h2>
        <p style="font-size: 16px; color: #555; margin-top: 8px;">Target Tanggal: <strong>{{$deadline}}</strong></p>
    </div>
</div>



    <!-- Tabel Data Realisasi -->
<table class="table table-striped mt-4" style="width: 100%; font-size: 13px;">
    <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">Activity</th>
            <th scope="col">PIC</th>
            <th scope="col">Noted</th>
            <th scope="col">Tanggal Penyelesaian</th>
            <th scope="col">Persentase</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp

        @foreach ($realisasiList as $realisasi)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $realisasi->nama_realisasi ?? '-' }}</td>
            <!-- Dropdown PIC -->
            <td>
                <select name="target" class="form-control" required>
                    <option value="">--Pilih PIC--</option>
                    @foreach($usersInDivisi as $user)
                        <option value="{{ $user->id }}" {{ $user->id == $realisasi->target ? 'selected' : '' }}>
                            {{ $user->nama_user }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>{{ $realisasi->desc ?? '-' }}</td>
            <td>{{ $realisasi->tgl_realisasi ?? '-' }}</td>
            <td>{{ $realisasi->presentase ?? '-' }}%</td>
            <td>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#basicModal{{ $realisasi->id }}">
                    <i class="bx bx-edit"></i>
                </button>
                <form action="{{ route('realisasi.destroy', $realisasi->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this activity?');">
                        <i class="bx bx-trash"></i>
                    </button>
                </form>

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
                                        <label for="nama_realisasi" class="form-label"><strong>Nama Activity</strong></label>
                                        <textarea name="nama_realisasi" class="form-control" required>{{ $realisasi->nama_realisasi }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="target" class="form-label"><strong>PIC</strong></label>
                                        <select name="target" class="form-control" required>
                                            <option value="">--Pilih PIC--</option>
                                            @foreach($usersInDivisi as $user)
                                                <option value="{{ $user->id }}" {{ $user->id == $realisasi->target ? 'selected' : '' }}>
                                                    {{ $user->nama_user }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tgl_realisasi" class="form-label"><strong>Tanggal Penyelesaian</strong></label>
                                        <input type="date" name="tgl_realisasi" class="form-control" required value="{{ $realisasi->tgl_realisasi }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="desc" class="form-label"><strong>Noted</strong></label>
                                        <textarea name="desc" class="form-control">{{ $realisasi->desc }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="presentase" class="form-label"><strong>Presentase</strong></label>
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

    <!-- Button to Show/Hide Form -->
    <button type="button" id="toggleFormButton" class="btn btn-info mb-3">
        <i class="fa fa-eye"></i> Show Form
    </button>

    <!-- Form for Adding New Realisasi -->
    <form id="realisasiForm" action="{{ route('realisasi.store') }}" method="POST" style="display: none;">
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
                <select name="target[]" class="form-control" required>
                    <option value="">--Pilih PIC--</option>
                    @foreach($usersInDivisi as $user)
                        <option value="{{ $user->id }}" {{ old('target') == $user->id ? 'selected' : '' }}>
                            {{ $user->nama_user }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DESC --}}
            <div class="col-md-4 col-sm-12 mb-3">
                <label for="desc"><strong>Noted</strong></label>
                <textarea name="desc[]" class="form-control" rows="3" placeholder="Masukan Catatan"></textarea>
            </div>
        </div>

        <div class="row">
            {{-- TANGGAL REALISASI --}}
            <div class="col-md-3 col-sm-12 mb-3">
                <label for="tgl_realisasi"><strong>Tanggal Penyelesaian</strong></label>
                <input type="date" name="tgl_realisasi[]" class="form-control">
            </div>

            {{-- PERSENTASE % --}}
            <div class="col-md-3 col-sm-12 mb-3">
                <label for="presentase"><strong>Persentase</strong></label>
                <input type="number" name="presentase[]" class="form-control" placeholder="%" step="0.01">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-plus"></i> Add Activity
                </button>
            </div>
        </div>
    </form>

    <!-- JavaScript to Toggle the Form Visibility -->
    <script>
        document.getElementById('toggleFormButton').addEventListener('click', function() {
            var form = document.getElementById('realisasiForm');
            if (form.style.display === "none") {
                form.style.display = "block";
                this.innerHTML = '<i class="fa fa-eye-slash"></i> Hide Form'; // Change button text to 'Hide Form'
            } else {
                form.style.display = "none";
                this.innerHTML = '<i class="fa fa-eye"></i> Show Form'; // Change button text back to 'Show Form'
            }
        });
    </script>


    <!-- Form untuk Mengupdate Status -->
    <form action="{{ route('realisasi.update', $realisasiList->first()->id ?? 0) }}" method="POST" class="mt-4">
        @csrf
        @method('PUT')

        <label for="nilai_akhir"><strong> Persentase Tindakan Lanjut: {{ number_format($realisasiList->first()->nilai_akhir ?? 0, 0) }}%</strong></label>

        <div class="row">
            <div class="col-md-4">
                <br>
                <label for="status"><strong>Status</strong></label>
                <div class="d-flex align-items-center">
                    <select name="status" class="form-control">
                        <option value="">--Pilih Status--</option>
                        <option value="ON PROGRES" {{ old('status', $realisasiList->first()->status ?? '') == 'ON PROGRES' ? 'selected' : '' }}>ON PROGRES</option>
                        <option value="CLOSE" {{ old('status', $realisasiList->first()->status ?? '') == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                    </select>
                    <button type="submit" class="btn btn-primary ms-2">Update</button>
                </div>
            </div>

            <div class="col-md-4">
                <br>
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
