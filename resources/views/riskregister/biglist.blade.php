@extends('layouts.main')

@section('content')
<div class="container">

    <!-- Tampilkan pesan sukses jika ada -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Filter -->
    <form method="GET" action="{{ route('riskregister.biglist') }}">

        <div class="container">
            <div class="row mb-3">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label" for="nama_divisi"><strong>Departemen:</strong></label>
                        <div class="col-sm-8">
                            <select name="nama_divisi" id="nama_divisi" class="form-control">
                                <option value="">--Semua Departemen--</option>
                                @foreach ($divisiList as $divisi)
                                    <option value="{{ $divisi->nama_divisi }}" {{ request('nama_divisi') == $divisi->nama_divisi ? 'selected' : '' }}>
                                        {{ $divisi->nama_divisi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label" for="year"><strong>Tahun Penyelesaian:</strong></label>
                        <div class="col-sm-8">
                            <select name="year" id="year" class="form-control">
                                <option value="">--Semua Tahun--</option>
                                @for($year = date('Y'); $year >= 2000; $year--)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label" for="tingkatan"><strong>Tingkatan:</strong></label>
                        <div class="col-sm-8">
                            <select name="tingkatan" id="tingkatan" class="form-control">
                                <option value="">--Semua Tingkatan--</option>
                                <option value="LOW" {{ request('tingkatan') == 'LOW' ? 'selected' : '' }}>LOW</option>
                                <option value="MEDIUM" {{ request('tingkatan') == 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                                <option value="HIGH" {{ request('tingkatan') == 'HIGH' ? 'selected' : '' }}>HIGH</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label" for="status"><strong>Status:</strong></label>
                        <div class="col-sm-8">
                            <select name="status" id="status" class="form-control">
                                <option value="">--Semua Status--</option>
                                <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                                <option value="ON PROGRES" {{ request('status') == 'ON PROGRES' ? 'selected' : '' }}>ON PROGRESS</option>
                                <option value="CLOSE" {{ request('status') == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                                <option value="open_on_progres" {{ request('status') == 'open_on_progres' ? 'selected' : '' }}>OPEN & ON PROGRES</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tombol Submit Filter -->
        <button type="submit" class="btn btn-primary">Filter</button>

            <!-- Tombol Export Excel -->
            <a href="{{ route('riskregister.exportFilteredExcel', array_merge(request()->all(), ['id' => $divisi->id, 'export' => 'excel'])) }}" class="btn btn-success">
                <i class="bi bi-printer-fill"></i>
            </a>

        {{-- <a href="{{ route('riskregister.exportFilteredExcel', ['id' => $divisi->id, 'export' => 'excel']) }}" class="btn btn-success">Cetak</a> --}}
        {{-- <a href="{{ route('export.pdf') }}" class="btn btn-primary">Export to PDF</a> --}}


    </form>

    <h1 class="card-title">All Risk Register & Opportunity of Issues</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Issue</th>
                <th>Pihak Berkepentingan & Tindakan Lanjut</th>
                <th>Risiko</th>
                <th>Peluang</th>
                <th>Tingkatan</th>
                <th>Skor</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($formattedData as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data['issue'] }}</td>

                    <!-- Gabungkan Pihak Berkepentingan dan Tindakan Lanjut -->
                    <td>
                        <ul>
                            @foreach ($data['pihak'] as $index => $pihak)
                                <li>
                                    <strong>{{ $pihak }}</strong>
                                    <ul>
                                        <li>{{ $data['tindak_lanjut'][$index] }}</li>
                                    </ul>
                                </li>
                                <hr>
                            @endforeach
                        </ul>
                    </td>

                    <td>
                        @foreach ($data['risiko'] as $risiko)
                            {{ $risiko }}<br>
                        @endforeach
                    </td>

                    <td>{{ $data['peluang'] }}</td>

                    <td>
                        @foreach ($data['tingkatan'] as $tingkatan)
                            {{ $tingkatan }}<br>
                        @endforeach
                    </td>

                    <!-- Skor -->
                    <td>
                        @foreach ($data['scores'] as $score)
                            @php
                                $colorClass = '';
                                if ($score >= 1 && $score <= 2) {
                                    $colorClass = 'bg-success text-white'; // Hijau
                                } elseif ($score >= 3 && $score <= 4) {
                                    $colorClass = 'bg-warning text-white'; // Kuning
                                } elseif ($score >= 5 && $score <= 25) {
                                    $colorClass = 'bg-danger text-white'; // Merah
                                }
                            @endphp
                            <span class="badge {{ $colorClass }}">{{ $score }}</span><br>
                        @endforeach
                    </td>

                    <!-- Status -->
                    <td>
                        @foreach ($data['status'] as $status)
                            <span class="badge
                                @if($status == 'OPEN')
                                    bg-success
                                @elseif($status == 'ON PROGRES')
                                    bg-warning
                                @elseif($status == 'CLOSE')
                                    bg-danger
                                @endif">
                                {{ $status }}
                            </span><br>
                        @endforeach
                    </td>

                <!-- Action Buttons -->
                <td>
                    @if(auth()->user()->role === 'admin')
                        <div class="btn-group" role="group">
                            <a href="{{ route('resiko.matriks', $data['id']) }}" title="Detail Risiko" class="btn btn-secondary btn-sm me-1">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="{{ route('riskregister.destroy', $data['id']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="ri ri-delete-bin-fill"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
