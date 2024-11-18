@extends('layouts.main')

@section('content')

<div class="container">
    <h1 class="card-title">Matriks Risiko: <br>{{ $resiko_nama }}</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Kriteria </th>
                <th scope="col">Nilai </th>
                <th scope="col">Deskripsi Severity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kriteriaData as $k)  <!-- Loop through the filtered kriteriaData -->
                <tr>
                    <td>{{ $k->nama_kriteria }}</td>

                    <!-- Tampilkan Deskripsi dan Nilai Kriteria Secara Berpasangan -->
                    <td colspan="2">
                        <table class="table table-bordered">
                            <tbody>
                                @php
                                    // Decode JSON menjadi array
                                    $descArray = json_decode($k->desc_kriteria, true) ?? [];
                                    $nilaiArray = json_decode($k->nilai_kriteria, true) ?? [];
                                @endphp

                                <!-- Iterasi dan tampilkan setiap pasangan deskripsi dan nilai -->
                                @foreach ($descArray as $index => $desc)
                                    <tr>
                                        <td>{{ $nilaiArray[$index] ?? '' }}</td>

                                        <td>{{ $desc }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data kriteria</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    {{-- Matriks Before --}}
    <h4><strong>MATRIKS BEFORE</strong></h4>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                {{-- <th rowspan="2">No</th> --}}
                <th rowspan="2"></th>
                <th rowspan="2">Severity / Keparahan<br><small>{{ $kategori }}</small></th>
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
            @foreach($matriks_used as $i => $row)
            <tr>
                <td></td>
                <td></td>
                @foreach($row as $j => $value)
                <td style="background-color: {{ $colors_used[$i][$j] }}; color: black;">
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

    {{-- Matriks After --}}
    <h4><strong>MATRIKS AFTER</strong></h4>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                {{-- <th rowspan="2">No</th> --}}
                <th rowspan="2"></th>
                <th rowspan="2">Severity / Keparahan<br><small>{{ $kategori }}</small></th>
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
            @foreach($matriks_used as $i => $row)
            <tr>
                <td></td>
                <td></td>
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

    <a class="btn btn-danger" href="{{ route('riskregister.tablerisk', ['id' => $three]) }}" title="Back">
        <i class="ri-arrow-go-back-line"></i>
    </a>

    <a class="btn btn-warning" href="{{ route('resiko.edit', ['id' => $same]) }}" title="Back">
        <i class="bx bx-edit"></i>
    </a>
</div>

@endsection
