@extends('layouts.main')

@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h4><strong>Changes Password</strong></h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        @foreach($errors->all() as $err)
                            <div class="alert alert-danger">{{ $err }}</div>
                        @endforeach
                    @endif

                    <form action="{{ route('password.action') }}" method="POST">
                        @csrf

                        <!-- Current Password -->
                        <div class="mb-4">
                            <label for="old_password" class="form-label"><strong>Password Sekarang*</strong></label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="old_password" id="old_password" placeholder="Masukkan password saat ini" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('old_password', this)">
                                    <i class="ri-eye-line"></i>
                                </span>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label for="new_password" class="form-label"><strong>Password Baru*</strong></label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="new_password" id="new_password" placeholder="Masukkan password baru" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('new_password', this)">
                                    <i class="ri-eye-line"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label"><strong>Konfirmasi Password Baru*</strong></label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder="Konfirmasi password baru" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('new_password_confirmation', this)">
                                    <i class="ri-eye-line"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a class="btn btn-danger" href="/">
                                <i class="bx bx-arrow-back"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-3-fill"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(fieldId, iconElement) {
        const passwordField = document.getElementById(fieldId);
        const icon = iconElement.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('ri-eye-line');
            icon.classList.add('ri-eye-off-line');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('ri-eye-off-line');
            icon.classList.add('ri-eye-line');
        }
    }
</script>

@endsection
