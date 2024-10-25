<!-- resources/views/ppk/index.blade.php -->
@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Data PPK</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Jenis Ketidaksesuaian</th>
                <th>Pembuat</th>
                <th>Email Pembuat</th>
                <th>Divisi Pembuat</th>
                <th>Penerima</th>
                <th>Email Penerima</th>
                <th>Divisi Penerima</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ppks as $ppk)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ppk->judul }}</td>
                    <td>{{ $ppk->jenisketidaksesuaian }}</td>
                    <td>{{ $ppk->pembuat }}</td>
                    <td>{{ $ppk->emailpembuat }}</td>
                    <td>{{ $ppk->divisipembuat }}</td>
                    <td>{{ $ppk->penerima }}</td>
                    <td>{{ $ppk->emailpenerima }}</td>
                    <td>{{ $ppk->divisipenerima }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
