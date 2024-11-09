@extends('layouts.main')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Edit Kriteria</h5>

                    <form action="{{ route('admin.kriteria.update', $kriteria->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
                            <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria" value="{{ $kriteria->nama_kriteria }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="desc_kriteria" class="form-label">Deskripsi Kriteria</label>
                            <div id="desc_kriteria">
                                @php
                                    $descArray = json_decode($kriteria->desc_kriteria, true);
                                    $nilaiArray = json_decode($kriteria->nilai_kriteria, true);
                                @endphp

                                @foreach ($descArray as $index => $desc)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="desc_kriteria[]" value="{{ $desc }}" placeholder="Deskripsi Kriteria" required>
                                        <input type="text" class="form-control" name="nilai_kriteria[]" value="{{ $nilaiArray[$index] }}" placeholder="Nilai Kriteria" required>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-success" onclick="addDescription()">Tambah Deskripsi</button>
                        </div>

                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function addDescription() {
        var div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.innerHTML = '<input type="text" class="form-control" name="desc_kriteria[]" placeholder="Deskripsi Kriteria" required><input type="text" class="form-control" name="nilai_kriteria[]" placeholder="Nilai Kriteria" required>';
        document.getElementById('desc_kriteria').appendChild(div);
    }
</script>

@endsection
