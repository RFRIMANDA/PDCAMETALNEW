@extends('layouts.main')

@section('content')
<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Edit User</h5>

          <form method="POST" action="{{ route('admin.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" name="nama_user" class="form-control" id="name" value="{{ old('nama_user', $user->nama_user) }}" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $user->email) }}" required>
    </div>

    <div class="mb-3">
      <label for="role" class="form-label">Role</label>
      <select name="role" class="form-select" id="role" required>
          <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>user</option>
          <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>admin</option>
      </select>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
        <input type="password" name="password" class="form-control" id="password">
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
    </div>

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
</form>


        </div>
      </div>
    </div>
  </div>
</section>
@endsection
