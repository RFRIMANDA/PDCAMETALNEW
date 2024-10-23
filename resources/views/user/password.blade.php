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
                <input class="form-control" type="password" name="old_password" />
            </div>
            <div class="mb-3">
                <label><strong>Password Baru*</strong><span class="text-danger"></span></label>
                <input class="form-control" type="password" name="new_password" />
            </div>
            <div class="mb-3">
                <label><strong>Konfirmasi Password Baru*</strong><span class="text-danger"></span></label>
                <input class="form-control" type="password" name="new_password_confirmation" />
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


@endsection
