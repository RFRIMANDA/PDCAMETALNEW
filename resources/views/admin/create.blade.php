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
