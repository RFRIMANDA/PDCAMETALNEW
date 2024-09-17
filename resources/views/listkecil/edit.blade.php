@extends('layouts.main')

@section('content')
<div class="container">
    <h2>Edit Detail Tindakan: {{ $selectedTindakan }}</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('listkecil.update', $listkecil->id) }}" method="POST">
    @csrf
    @method('POST')

    <input type="hidden" name="index" value="{{ $index }}">

        <table class="table">
            <thead>
                <tr>
                    <th>Realisasi</th>
                    <th>Tanggal</th>
                    <th>Responsible</th>
                    <th>Accountable</th>
                    <th>Consulted</th>
                    <th>Informed</th>
                    <th>Goal</th>
                    <th>Budget</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="realisasi" class="form-control" value="{{ $listKecil->realisasi }}" required></td>
                    <td><input type="date" name="date" class="form-control" value="{{ $listKecil->date }}"></td>
                    <td><input type="text" name="responsible" class="form-control" value="{{ $listKecil->responsible }}"></td>
                    <td><input type="text" name="accountable" class="form-control" value="{{ $listKecil->accountable }}"></td>
                    <td><input type="text" name="consulted" class="form-control" value="{{ $listKecil->consulted }}"></td>
                    <td><input type="text" name="informed" class="form-control" value="{{ $listKecil->informed }}"></td>
                    <td><input type="text" name="anumgoal" class="form-control" value="{{ $listKecil->anumgoal }}"></td>
                    <td><input type="text" name="anumbudget" class="form-control" value="{{ $listKecil->anumbudget }}"></td>
                    <td><input type="text" name="desc" class="form-control" value="{{ $listKecil->desc }}"></td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>

</div>
@endsection
