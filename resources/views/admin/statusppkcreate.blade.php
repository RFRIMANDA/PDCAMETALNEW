@extends('layouts.main')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tambah Status PPK Baru</h5>
                    <form method="POST" action="{{ route('admin.statusppk.store') }}">
                        @csrf

                        <!-- Input untuk nama_kriteria -->
                        <div class="row mb-3">
                            <label for="statusppk" class="col-sm-2 col-form-label"><strong>Nama Status:</strong></label>
                            <div class="col-sm-10">
                                <input type="text" name="statusppk" class="form-control" id="statusppk" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
