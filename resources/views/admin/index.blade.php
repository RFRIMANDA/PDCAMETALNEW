@extends('layouts.main')

@section('content')

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Kelola Data User</h5>
          <a href="/admin/create" title="Buat User" class="btn btn-sm btn-primary mb-3">Add
            <i class="bi bi-person-plus-fill"></i>
          </a>

          <!-- Filter Form -->
          <form method="GET" action="{{ route('admin.kelolaakun') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="nama_user" class="form-control" placeholder="Cari berdasarkan Nama" value="{{ request('nama_user') }}">
                </div>

                <div class="col-md-4">
                    <select name="divisi" class="form-control" >
                        <option value="" disabled selected>Pilih Divisi</option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id }}" {{ request('divisi') == $d->id ? 'selected' : '' }}>
                                {{ $d->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <select name="role" class="form-control">
                            <option value="">--Semua Role--</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="manajemen" {{ request('role') == 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                            <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        </select>

                    </div>
                </div>
            </div><br>
            <button type="submit" class="btn btn-primary">Cari</button> <!-- Added 'ms-2' for margin -->
            <a href="{{ route('admin.kelolaakun') }}" class="btn btn-secondary">Reset</a>
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

          <!-- User Data Table -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col" style="width: 80px;">No</th>
                <th scope="col">Nama</th>
                <th scope="col">Email</th>
                <th scope="col">Divisi</th>
                <th scope="col" style="width: 80px;">Role</th>
                <th scope="col" style="width: 150px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->nama_user }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->divisi}}</td>
                <td>{{ $user->role }}</td>
                <td>
                  <a href="{{ route('admin.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Edit">
                    <i class="bx bx-edit"></i>
                  </a>

                  <form action="{{ route('admin.destroy', $user->id) }}" method="POST" style="display:inline;" title="Delete" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
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
