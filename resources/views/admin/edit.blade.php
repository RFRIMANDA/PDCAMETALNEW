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

            <div class="row mb-3">
                <label for="name" class="col-sm-2 col-form-label"><strong>Nama User:</strong></label>
                <div class="col-sm-10">
                    <input type="text" name="nama_user" class="form-control" id="name" value="{{ old('nama_user', $user->nama_user) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="email" class="col-sm-2 col-form-label"><strong>Email User:</strong></label>
                <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="role" class="col-sm-2 col-form-label"><strong>Role:</strong></label>
                <div class="col-sm-3">
                    <select name="role" class="form-select" id="role" required>
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label"><strong>Divisi:</strong></label>
                <div class="col-sm-10">
                    @foreach ($divisi as $d)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="type[]" value="{{ $d->id }}" id="divisi{{ $d->id }}"
                            @if(is_array(old('type', $selectedDivisi ?? [])) && in_array($d->id, old('type', $selectedDivisi ?? []))) checked @endif>
                        <label class="form-check-label" for="divisi{{ $d->id }}">
                            {{ $d->nama_divisi }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>


            <div class="row mb-3">
                <label for="password" class="col-sm-2 col-form-label"><strong>Password Baru:</strong></label>
                <div class="col-sm-10">
                    <input type="text" name="password" class="form-control" id="password" placeholder="(Kosongkan jika tidak ingin mengubah)">
                </div>
            </div>

            <div class="row mb-3">
                <label for="password_confirmation" class="col-sm-2 col-form-label"><strong>Konfirmasi Password:</strong></label>
                <div class="col-sm-10">
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="(Kosongkan jika tidak ingin mengubah)">
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
