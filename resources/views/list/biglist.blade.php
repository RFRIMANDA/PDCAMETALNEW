@extends('layouts.main')

@section('content')

<div class="container mt-5">
    <div class="text-center mb-4">
        <h1><span class="badge bg-info">Table Risk Register TML</span></h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('list.biglist') }}" class="btn btn-primary mb-3">View All Big Lists</a>

    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 80px;">No.</th>
                    <th style="width: 200px;">Issue</th>
                    <th style="width: 200px;">Pihak Berkepentingan</th>
                    <th style="width: 200px;">Resiko (R)</th>
                    <th style="width: 150px;">Peluang (P)</th>
                    <th style="width: 100px;">Tingkatan</th>
                    <th style="width: 300px;">Tindak Lanjut</th>
                    <th style="width: 150px;">Target PIC</th>
                    <th style="width: 150px;">Status</th>
                    <th style="width: 100px;">Actual Risk</th>
                </tr>
            </thead>
            <tbody>
                @foreach($Alldata as $all)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $all->issue }}</td>
                    <td>
                        @foreach($Tindak->where('id_listform', $all->id) as $tindakan)
                            @php
                                $divisiName = $Divisi->where('id', $tindakan->pihak)->first()->nama_divisi ?? 'Unknown';
                            @endphp
                            {{ $divisiName }}<br>
                            <hr>
                        @endforeach
                    </td>
                    <td>
                        @foreach($Tindak->where('id_listform', $all->id) as $tindakan)
                            {{ $tindakan->resiko }}<br>
                            <hr>
                        @endforeach
                    </td>
                    <td>{{ $all->peluang }}</td>
                    <td>{{ $all->tingkatan }}</td>
                    <td>
                        @foreach($Tindak->where('id_listform', $all->id) as $tindakan)
                            {{ $tindakan->nama_tindakan }}<br>
                            <hr>
                        @endforeach
                    </td>
                    <td>
                        @foreach($Tindak->where('id_listform', $all->id) as $tindakan)
                            {{ $tindakan->pic }}<br>
                            <hr>
                        @endforeach
                    </td>
                    <td>{{ $all->status }}</td>
                    <td>{{ $all->risk }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
