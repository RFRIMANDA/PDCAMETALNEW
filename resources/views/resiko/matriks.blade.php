@extends('layouts.main')

@section('content')

<div class="container">
    <h1 class="card-title">Matriks Risiko: <br>{{ $resiko_nama }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><strong>Tingkatan = {{ $tingkatan }}</strong></h5>
            <p class="card-text"><strong>Probability = {{ $probability }}</strong></p>
            <p class="card-text"><strong>Severity = {{ $severity }}</strong></p>
            <p class="card-text"><strong>Probability x Severity = {{ $riskscore }}</strong></p>
        </div>
    </div>

    <table class="table table-striped" style="display: none;">
        <thead>
            <tr>
                <th scope="col">Kriteria</th>
                <th scope="col">Detail</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kriteriaData as $k)
                <tr>
                    <td rowspan="{{ count(json_decode($k->desc_kriteria, true) ?? []) }}">{{ $k->nama_kriteria }}</td>
                    <td>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nilai</th>
                                    <th>Deskripsi</th>
                                    <th>Matriks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $descArray = explode(',', $k->desc_kriteria);
                                    $nilaiArray = explode(',', $k->nilai_kriteria);
                                @endphp
                                @foreach ($descArray as $index => $desc)
                                    <tr>
                                        <!-- Nilai dan Deskripsi -->
                                        <td>{{ $nilaiArray[$index] ?? '' }}</td>
                                        <td>{{ $desc }}</td>

                                        <!-- Matriks pada baris yang relevan -->
                                        <td>
                                            <table class="table table-bordered text-center mb-0">
                                                <tr>
                                                    @foreach($matriks_used[$index] as $j => $value)
                                                        <td style="background-color: {{ $colors_used[$index][$j] }}; color: black;">
                                                            @if(($index + 1) == $severity && ($j + 1) == $probability)
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
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Tidak ada data kriteria</td>
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
            @php
                // Ambil deskripsi dan pastikan validitasnya
                $description = $descArray[$i] ?? '';
            @endphp
            <tr>
                <!-- Kolom Nilai -->
                <td rowspan="2">{{ $nilaiArray[$i] ?? '' }}</td>

                <!-- Kolom Deskripsi -->
                <td>{{ $description }}</td>

                <!-- Matriks -->
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
            <!-- Baris tambahan untuk deskripsi -->
            <tr>

            </tr>
            @endforeach
        </tbody>
    </table>

    <a class="btn btn-danger" href="{{ route('riskregister.tablerisk', ['id' => $three]) }}" title="Back">
        <i class="ri-arrow-go-back-line"></i>
    </a>

    <a class="btn btn-success" href="{{ route('resiko.edit', ['id' => $lol]) }}" title="Back">
        <i class="bx bx-edit"></i>
    </a>

</div>

@endsection
