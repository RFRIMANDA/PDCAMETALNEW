@extends('layouts.main')

@section('content')
<div class="container">
    
    <h3><span class="badge bg-success">Selamat Datang</span></h3>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nama Divisi</th>
                <th>Create Register</th>
            </tr>
        </thead>
        <tbody>
            @foreach($divisi as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{ $item->nama_divisi }}</td>
                    <td>
                    <a href="{{ route('list.create', $item->id) }}" class="btn btn-info btn-sm" title="Create List Register">
    <i class="fas fa-plus"></i>
</a>

                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

