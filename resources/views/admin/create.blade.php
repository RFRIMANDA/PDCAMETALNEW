@extends('layouts.main')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tambah User Baru</h5>
                    <form method="POST" action="{{ route('admin.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_user" class="form-label">Nama</label>
                            <input type="text" name="nama_user" class="form-control" id="nama_user" value="{{ old('nama_user') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" class="form-control" id="role" required>
                                <option value="" disabled selected>Pilih Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>admin</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>user</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <br>
                        <br>
                        
                        <h5 class="card-title">Noted :Password untuk user baru sudah auto= "password123"</h5>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
