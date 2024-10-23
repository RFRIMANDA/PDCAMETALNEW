@extends('layouts.main')

@section('content')
<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Edit Divisi</h5>

          <form method="POST" action="{{ route('admin.divisi.update', $divisi->id) }}">
    @csrf
    @method('PUT')

    <div class="row mb-3">
        <label for="divisi" class="col-sm-2 col-form-label"><strong>Nama Divisi:</strong></label>
        <div class="col-sm-10">
            <input type="text" name="nama_divisi" class="form-control" id="divisi" value="{{ old('nama_user', $divisi->nama_divisi) }}" required>
        </div>
    </div>

    <a href="javascript:history.back()" class="btn btn-danger" title="Kembali">
        <i class="ri-arrow-go-back-line"></i>
    </a>

    <button type="submit" class="btn btn-primary">Save
        <i class="ri-save-3-fill"></i>
    </button>
</form>


        </div>
      </div>
    </div>
  </div>
</section>
@endsection
