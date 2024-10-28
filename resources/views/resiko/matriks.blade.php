@extends('layouts.main') <!-- Ganti dengan layout yang Anda gunakan -->

@section('content')

<div class="container">
    <h1 class="card-title">Matriks Risiko: <br>{{ $resiko_nama }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><strong>Tingkatan = {{ $tingkatan }}</strong></h5>
            <p class="card-text"><strong>Probability = {{ $severity }}</strong></p>
            <p class="card-text"><strong>Severity = {{ $probability }}</strong></p>
            <p class="card-text"><strong>Probability x Severity = {{ $riskscore }}</strong></p>
            {{-- <p class="card-text"><strong>Nilai Actual Risk: {{$actual}}%</strong></p> --}}
        </div>
    </div>

    <h4><strong>MATRIKS BEFORE</strong></h4>
    <table class="table table-bordered text-center">
        <!-- Tabel matriks pertama -->
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Severity / Keparahan
                    <p>{{$kategori}}
                </th>
                <th colspan="5">Probability / Dampak (Likelihood)</th>
            </tr>
            <tr>
                <th>1 (Sangat Jarang Terjadi)</th>
                <th>2 (Jarang Terjadi)</th>
                <th>3 (Dapat Terjadi)</th>
                <th>4 (Sering Terjadi)</th>
                <th>5 (Selalu Terjadi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matriks_used as $i => $row) <!-- Menggunakan matriks_used -->
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    @if(isset($deskripsiSeverity[$i]))
                        {{ $deskripsiSeverity[$i] }}
                    @else
                        None
                    @endif
                </td>
                @foreach($row as $j => $value)
                <td style="background-color: {{ $colors_used[$i][$j] }}; color: black;"> <!-- Menggunakan colors_used -->
                    @if(($i + 1) == $severity && ($j + 1) == $probability)
                        <div class="d-flex align-items-center">
                            <div class="spinner-grow text-info" role="status" style="width: 1.5rem; height: 1.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">{{ $value }}</span>
                        </div>
                    @else
                        {{ $value }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <br>
    <hr>
    <br>
    <br>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><strong>ACTUAL RISK</strong></h5>
            <p class="card-text"><strong>Probability = {{ $severityrisk }}</strong></p>
            <p class="card-text"><strong>Severity = {{ $probabilityrisk }}</strong></p>
            <p class="card-text"><strong>Probability x Severity = {{ $riskscorerisk }}</strong></p>
            <p class="card-text"><strong>Nilai Actual Risk: {{$actual}}%</strong></p>
        </div>
    </div>

    <h4><strong>MATRIKS AFTER</strong></h4>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Severity / Keparahan
                    <p>{{$kategori}}
                </th>
                <th colspan="5">Probability / Dampak (Likelihood)</th>
            </tr>
            <tr>
                <th>1 (Sangat Jarang Terjadi)</th>
                <th>2 (Jarang Terjadi)</th>
                <th>3 (Dapat Terjadi)</th>
                <th>4 (Sering Terjadi)</th>
                <th>5 (Selalu Terjadi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matriks_used as $i => $row) <!-- Menggunakan matriksnew untuk matriks kedua -->
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    @if(isset($deskripsiSeverity[$i]))
                        {{ $deskripsiSeverity[$i] }}
                    @else
                        None
                    @endif
                </td>
                @foreach($row as $j => $value)
                <td style="background-color: {{ $colors_used[$i][$j] }}; color: black;">
                    @if(($i + 1) == $severityrisk && ($j + 1) == $probabilityrisk)
                        <div class="d-flex align-items-center">
                            <div class="spinner-grow text-warning" role="status" style="width: 1.5rem; height: 1.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">{{ $value }}</span>
                        </div>
                    @else
                        {{ $value }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('riskregister.index')}}" class="btn btn-danger " title="Kembali">
        <i class="ri-arrow-go-back-line"></i>
    </a>

    <a class="btn btn-warning" href="{{ route('resiko.edit', ['id' => $same]) }}" title="Back">
        <i class="bx bx-edit"></i>
    </a>
</div>

@endsection
