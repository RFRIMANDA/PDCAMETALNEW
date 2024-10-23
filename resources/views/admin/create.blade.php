@extends('layouts.main')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add User Baru</h5>
                    <form method="POST" action="{{ route('admin.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="nama_user" class="col-sm-2 col-form-label"><strong>Nama User:</strong></label>
                            <div class="col-sm-10">
                                <input type="text" name="nama_user" class="form-control" id="nama_user" value="{{ old('nama_user') }}" placeholder="Masukan nama User" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-sm-2 col-form-label"><strong>Email:</strong></label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="user@tatalogam.com" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="role" class="col-sm-2 col-form-label"><strong>Role:</strong></label>
                            <div class="col-sm-3">
                            <select name="role" class="form-control" id="role" required>
                                <option value="" disabled selected>--Pilih Role--</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="role" class="col-sm-2 col-form-label"><strong>Divisi:</strong></label>
                            <div class="col-sm-3">
                                @foreach ($divisi as $d)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="type[]" value="{{ $d->id }}" id="divisi{{ $d->id }}">
                                        <label class="form-check-label" for="divisi{{ $d->id }}">
                                            {{ $d->nama_divisi }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>




                        <a href="javascript:history.back()" class="btn btn-danger" title="Kembali">
                            <i class="ri-arrow-go-back-line"></i>
                        </a>

                        <button type="submit" class="btn btn-primary">Save
                            <i class="ri-save-3-fill"></i>
                        </button>
                        <br>
                        <br>

                        <h5 class="card-title">Catatan :Password untuk user baru sudah auto= "password123"</h5>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
