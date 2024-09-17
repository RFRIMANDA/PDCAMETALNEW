@extends('layouts.main')

@section('content')

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">User Data Table</h5>
          <a href="/admin/create" class="btn btn-sm btn-primary mb-3">Tambah User</a>
          
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


          <!-- User Data Table -->
          <table class="table table-responsive">
            <thead class="thead-dark">
              <tr>
                <th scope="col" style="width: 80px;">ID</th>
                <th scope="col">Nama</th>
                <th scope="col">Email</th>
                <th scope="col" style="width: 80px;">Role</th>
                <th scope="col" style="width: 150px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->nama_user }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                  <!-- Contoh aksi, misalnya edit atau delete -->
                  <a href="{{ route('admin.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
                  <!-- Jika ada opsi hapus, bisa ditambahkan di sini -->
                  <form action="{{ route('admin.destroy', $user->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
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

