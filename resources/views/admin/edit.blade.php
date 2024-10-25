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
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                        <label class="form-check-label" for="select-all">
                            Select All
                        </label>
                    </div>
                    <div class="checkbox-group">
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

<style>
    .checkbox-group {
        display: flex;
        flex-wrap: wrap; /* Agar checkbox bisa pindah ke baris baru jika tidak cukup ruang */
        gap: 15px; /* Jarak antar checkbox */
        margin-top: 10px; /* Jarak antara Select All dan checkbox lainnya */
    }

    .checkbox-group .form-check {
        flex: 0 1 200px; /* Setiap checkbox akan mengambil lebar maksimal 200px, lalu wrap */
        margin-bottom: 10px; /* Jarak antara tiap checkbox dengan baris bawah */
    }

    .form-check-input {
        margin-right: 10px; /* Jarak antara checkbox dengan labelnya */
    }

    /* Untuk memastikan label dan checkbox align secara vertikal */
    .form-check-label {
        vertical-align: middle;
    }

    /* Untuk tampilan Select All di baris terpisah */
    .form-check:first-child {
        margin-bottom: 10px;
    }
</style>

<script>
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.checkbox-group .form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>

@endsection
