@extends('layouts.main')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tambah Kriteria Baru</h5>
                    <form method="POST" action="{{ route('admin.kriteriastore') }}">
                        @csrf

                        <!-- Input untuk nama_kriteria -->
                        <div class="row mb-3">
                            <label for="nama_kriteria" class="col-sm-2 col-form-label"><strong>Nama Kriteria:</strong></label>
                            <div class="col-sm-10">
                                <input type="text" name="nama_kriteria" class="form-control" id="nama_kriteria" required>
                            </div>
                        </div>

                        <!-- Input dinamis untuk desc_kriteria dan nilai_kriteria -->
                        <div id="desc-container">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"><strong>Deskripsi Kriteria:</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" name="desc_kriteria[]" class="form-control" placeholder="Deskripsi">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="nilai_kriteria[]" class="form-control" placeholder="Nilai">
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="addDescField()">Tambah Deskripsi</button>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>

                    <script>
                    function addDescField() {
                        const container = document.getElementById('desc-container');
                        const index = container.children.length;

                        const row = `
                            <div class="row mb-3">
                                <div class="col-sm-8 offset-sm-2">
                                    <input type="text" name="desc_kriteria[]" class="form-control" placeholder="Deskripsi">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="nilai_kriteria[]" class="form-control" placeholder="Nilai">
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', row);
                    }
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
