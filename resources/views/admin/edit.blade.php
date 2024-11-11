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

            <!-- User Name -->
            <div class="row mb-3">
                <label for="name" class="col-sm-2 col-form-label"><strong>Nama User:</strong></label>
                <div class="col-sm-10">
                    <input type="text" name="nama_user" class="form-control" id="name" value="{{ old('nama_user', $user->nama_user) }}" required>
                </div>
            </div>

            <!-- Email -->
            <div class="row mb-3">
                <label for="email" class="col-sm-2 col-form-label"><strong>Email User:</strong></label>
                <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <!-- Role Dropdown -->
            <div class="row mb-3">
                <label for="role" class="col-sm-2 col-form-label"><strong>Role:</strong></label>
                <div class="col-sm-3">
                    <select name="role" class="form-select" id="role" required>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manajemen" {{ old('role', $user->role) == 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                    </select>
                </div>
            </div>

            <!-- Divisi Dropdown -->
            <div class="row mb-3">
                <label for="divisi" class="col-sm-2 col-form-label"><strong>Divisi:</strong></label>
                <div class="col-sm-10">
                    <select name="divisi" class="form-control">
                        <option value="" disabled selected>--Pilih Divisi--</option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id }}"
                                    {{ old('divisi', $user->divisi) == $d->id ? 'selected' : '' }}>
                                {{ $d->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Hak Akses Divisi -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label"><strong>Hak Akses Divisi:</strong></label>
                <div class="col-sm-10">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownDivisiAkses" data-bs-toggle="dropdown" aria-expanded="false">
                            Pilih Akses Divisi
                        </button>
                        <ul class="dropdown-menu checkbox-group" aria-labelledby="dropdownDivisiAkses">
                            <li>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                    <label class="form-check-label" for="select-all">Pilih Semua</label>
                                </div>
                            </li>
                            @foreach ($divisi as $d)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="type[]" value="{{ $d->id }}" id="divisi{{ $d->id }}"
                                            @if(in_array($d->id, old('type', $selectedDivisi ?? []))) checked @endif>
                                        <label class="form-check-label" for="divisi{{ $d->id }}">
                                            {{ $d->nama_divisi }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <hr>

            <div class="row mb-3">
                <label for="password" class="col-sm-2 col-form-label"><strong>Password Baru:</strong></label>
                <div class="col-sm-10 input-group">
                    <input type="password" name="password" class="form-control" id="password" placeholder="(Kosongkan jika tidak ingin mengubah)">
                    <span class="input-group-text" onclick="togglePasswordVisibility('password', this)">
                        <i class="ri-eye-line"></i>
                    </span>
                </div>
            </div>

            <div class="row mb-3">
                <label for="password_confirmation" class="col-sm-2 col-form-label"><strong>Konfirmasi Password:</strong></label>
                <div class="col-sm-10 input-group">
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="(Kosongkan jika tidak ingin mengubah)">
                    <span class="input-group-text" onclick="togglePasswordVisibility('password_confirmation', this)">
                        <i class="ri-eye-line"></i>
                    </span>
                </div>
            </div>

            <script>
                function togglePasswordVisibility(inputId, toggleIcon) {
                    var input = document.getElementById(inputId);
                    if (input.type === "password") {
                        input.type = "text";
                        toggleIcon.innerHTML = '<i class="ri-eye-off-line"></i>';
                    } else {
                        input.type = "password";
                        toggleIcon.innerHTML = '<i class="ri-eye-line"></i>';
                    }
                }
            </script>

            <button type="submit" class="btn btn-primary">Save</button>
            <a href="javascript:history.back()" class="btn btn-danger">Cancel</a>

        </form>
      </div>
    </div>
  </div>
</section>

<style>
    .dropdown-menu {
        max-height: 200px;
        overflow-y: auto;
    }

    .checkbox-group {
        padding: 0 10px;
    }

    .form-check {
        margin-bottom: 5px;
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
