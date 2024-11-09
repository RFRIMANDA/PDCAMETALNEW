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
                            <label for="divisi" class="col-sm-2 col-form-label"><strong>Divisi:</strong></label>
                            <div class="col-sm-10">
                                <select name="divisi" class="form-control" required>
                                    <option value="" disabled selected>--Pilih Divisi--</option>
                                    @foreach ($divisi as $d)
                                        <option value="{{ $d->id }}" {{ old('divisi') == $d->id ? 'selected' : '' }}>
                                            {{ $d->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="role" class="col-sm-2 col-form-label"><strong>Role:</strong></label>
                            <div class="col-sm-3">
                            <select name="role" class="form-control" id="role" required>
                                <option value="" disabled selected>--Pilih Role--</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="manajemen" {{ old('role') == 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                                <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            </select>
                            </div>
                        </div>

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
                                                        @if(is_array(old('type', $selectedDivisi ?? [])) && in_array($d->id, old('type', $selectedDivisi ?? []))) checked @endif>
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

                        <a href="javascript:history.back()" class="btn btn-danger" title="Kembali">
                            <i class="ri-arrow-go-back-line"></i>
                        </a>

                        <button type="submit" class="btn btn-primary">Save
                            <i class="ri-save-3-fill"></i>
                        </button>
                        <br>
                        <br>

                        <h5 class="card-title">Catatan : Password untuk user baru sudah auto= "password123"</h5>
                    </form>
                </div>
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
    // Script for select-all checkbox functionality
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.checkbox-group .form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>

@endsection
