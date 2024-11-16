@extends('layouts.main')

@section('content')

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Kelola Departemen</h5>
          <a href="{{ route('admin.divisi.create') }}" title="Tambah Divisi" class="btn btn-sm btn-primary mb-3">
            <i class="fa fa-plus"></i> Add Departemen
          </a>

          <form method="GET" action="{{ route('admin.divisi') }}" class="mb-4">
            <div class="row">
              <div class="col-md-4">
                <input type="text" name="nama_divisi" class="form-control" placeholder="Cari berdasarkan Nama Divisi" value="{{ request('nama_divisi') }}">
              </div>
              <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Cari</button>
              </div>
            </div>
          </form>

          <table class="table table-striped" style="font-size: 15px;">
            <thead>
              <tr>
                <th scope="col" style="width: 80px;">No</th>
                <th scope="col">Nama Departemen</th>
                <th scope="col" style="width: 150px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($divisis as $divisi)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $divisi->nama_divisi }}</td>
                <td>
                  <a href="{{ route('admin.divisi.edit', $divisi->id) }}" class="btn btn-sm btn-primary" title="Edit">
                    <i class="bx bx-edit"></i>
                  </a>

                  <form action="{{ route('admin.divisi.destroy', $divisi->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Departemen ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                      <i class="ri ri-delete-bin-fill"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>

          <!-- End User Data Table -->
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
