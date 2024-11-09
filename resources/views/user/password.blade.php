@extends('layouts.main')

@section('content')

<div class="row">
    <div class="col-md-6">
        @if(session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
        @endif
        @if($errors->any())
        @foreach($errors->all() as $err)
        <p class="alert alert-danger">{{ $err }}</p>
        @endforeach
        @endif
        <form action="{{ route('password.action') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label><strong>Password Sekarang*</strong><span class="text-danger"></span></label>
                <div class="input-group">
                    <input class="form-control" type="password" name="old_password" id="old_password" />
                    <span class="input-group-text" onclick="togglePasswordVisibility('old_password', this)">
                        <i class="ri-eye-line"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label><strong>Password Baru*</strong><span class="text-danger"></span></label>
                <div class="input-group">
                    <input class="form-control" type="password" name="new_password" id="new_password" />
                    <span class="input-group-text" onclick="togglePasswordVisibility('new_password', this)">
                        <i class="ri-eye-line"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label><strong>Konfirmasi Password Baru*</strong><span class="text-danger"></span></label>
                <div class="input-group">
                    <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" />
                    <span class="input-group-text" onclick="togglePasswordVisibility('new_password_confirmation', this)">
                        <i class="ri-eye-line"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <a class="btn btn-danger" href="/">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <button class="btn btn-primary">Save
                    <i class="ri-save-3-fill"></i>
                </button>
            </div>
        </form>
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
