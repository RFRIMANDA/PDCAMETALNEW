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

    <br>

    <!-- Search Button (Trigger Modal) -->
    <div class="card text-center">
        <div class="card-body">
            <h5 class="card-title" style="font-size: 25px; font-weight: 700; letter-spacing: 2px;">
                ALL REPORT RISK & OPPORTUNITY REGISTER
            </h5>
        </div>
    </div>
    <div style="display: flex; justify-content: space-between; width: 100%;">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal" style="font-weight: 500; font-size: 12px; padding: 6px 12px;">
            <i class="fa fa-filter" style="font-size: 14px;"></i> Filter Options
        </button>

        <a href="{{ route('admin.kriteria') }}" class="btn btn-primary" title="Setting Kriteria" style="font-weight: 500; font-size: 12px; padding: 6px 12px;">
            <i class="ri-settings-5-line"></i>
        </a>
    </div>

<br>

<style>
    .badge.bg-purple {
background-color: #ADD8E6;

color: rgb(0, 0, 0);
}
.table-wrapper {
    position: relative;
    max-height: 400px; /* Adjust height as needed */
    overflow-y: auto;
}

.table th {
    position: sticky;
    top: 0;
    background-color: #fff; /* Optional: to make sure the header has a white background */
    z-index: 1; /* Ensure the header is above the table rows */
}

</style>

<!-- Modal for Filters -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Form Filter -->
            <form method="GET" action="{{ route('riskregister.biglist') }}">
                <div class="container py-3">
                    <div class="row mb-3">
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manajemen')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_divisi" class="form-label"><strong>Departemen:</strong></label>
                                    <select id="nama_divisi" name="nama_divisi" class="form-select">
                                        <option value="">--Semua Departemen--</option>
                                        @foreach ($divisiList as $divisi)
                                            <option value="{{ $divisi->nama_divisi }}" {{ request('nama_divisi') == $divisi->nama_divisi ? 'selected' : '' }}>
                                                {{ $divisi->nama_divisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="year" class="form-label"><strong>Tahun Penyelesaian:</strong></label>
                                <select id="year" name="year" class="form-select">
                                    <option value="">--Semua Tahun--</option>
                                    @for($year = date('Y'); $year >= 2000; $year--)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kriteria" class="form-label"><strong>Kriteria:</strong></label>
                                <select id="kriteria" name="kriteria" class="form-select">
                                    <option value="">--Semua Kriteria--</option>
                                    <option value="Unsur keuangan / Kerugian" {{ request('kriteria') == 'Unsur keuangan / Kerugian' ? 'selected' : '' }}>Unsur keuangan / Kerugian</option>
                                    <option value="Safety & Health" {{ request('kriteria') == 'Safety & Health' ? 'selected' : '' }}>Safety & Health</option>
                                    <option value="Enviromental (lingkungan)" {{ request('kriteria') == 'Enviromental (lingkungan)' ? 'selected' : '' }}>Enviromental (lingkungan)</option>
                                    <option value="Reputasi" {{ request('kriteria') == 'Reputasi' ? 'selected' : '' }}>Reputasi</option>
                                    <option value="Financial" {{ request('kriteria') == 'Financial' ? 'selected' : '' }}>Financial</option>
                                    <option value="Operational" {{ request('kriteria') == 'Operational' ? 'selected' : '' }}>Operational</option>
                                    <option value="Kinerja" {{ request('kriteria') == 'Kinerja' ? 'selected' : '' }}>Kinerja</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tingkatan" class="form-label"><strong>Tingkatan:</strong></label>
                                <select id="tingkatan" name="tingkatan" class="form-select">
                                    <option value="">--Semua Tingkatan--</option>
                                    <option value="LOW" {{ request('tingkatan') == 'LOW' ? 'selected' : '' }}>LOW</option>
                                    <option value="MEDIUM" {{ request('tingkatan') == 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                                    <option value="HIGH" {{ request('tingkatan') == 'HIGH' ? 'selected' : '' }}>HIGH</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label"><strong>Status:</strong></label>
                                <select id="status" name="status" class="form-select">
                                    <option value="">--Semua Status--</option>
                                    <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                                    <option value="ON PROGRES" {{ request('status') == 'ON PROGRES' ? 'selected' : '' }}>ON PROGRESS</option>
                                    <option value="CLOSE" {{ request('status') == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                                    <option value="open_on_progres" {{ request('status') == 'open_on_progres' ? 'selected' : '' }}>OPEN & ON PROGRES</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="top10" class="form-label"><strong>Top 10 Highest Risk:</strong></label>
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" id="top10" name="top10" value="1" {{ request('top10') ? 'checked' : '' }}>
                                    <label class="ms-2" for="top10">Tampilkan hanya 10 tertinggi</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="keyword" class="col-sm-2 col-form-label"><strong>Search:</strong></label>
                        <div class="col-sm-8">
                            <input type="text" id="keyword" name="keyword" class="form-control" placeholder="Search..." value="{{ request('keyword') }}">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-between">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                        <a href="{{ route('riskregister.biglist') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>

                        {{-- @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manajemen')
                            <a href="{{ route('riskregister.exportFilteredExcel', array_merge(request()->all(), ['id' => $divisiList->first()->id ?? '', 'export' => 'excel'])) }}" class="btn btn-success" title="Export to Excel">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </a>
                        @endif --}}

                        <a href="{{ route('riskregister.export-pdf', ['id' => $divisiList->first()->id ?? '']) }}?tingkatan={{ request('tingkatan') }}&status={{ request('status') }}&nama_divisi={{ request('nama_divisi') }}&year={{ request('year') }}&keyword={{ request('keyword') }}&kriteria={{ request('kriteria') }}&top10={{ request('top10') }}" class="btn btn-danger" title="Export to PDF">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<br>

    <!-- Small tables -->
    <div class="card">
        <div class="card-body">
            <div style="overflow-x: auto;">
                <div class="table-wrapper">
                    <table class="table table-striped" style="width: 180%; font-size: 13px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Issue</th>
                            <th>I/E</th>
                            <th>Pihak Berkepentingan</th>
                            <th>Risiko</th>
                            <th>Peluang</th>
                            <th>Tingkatan</th>
                            <th>Skor Before</th>
                            <th>Tindakan Lanjut</th>
                            <th>Actual Risk</th>
                            <th>Skor After</th>
                            <th>Before</th>
                            <th>After</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($formattedData as $data)
                            <tr>
                                <td>
                                    <a>
                                        {{ $loop->iteration }}
                                    </a>
                                </td>
                                <td>{{ $data['issue'] }}</td>
                                <td>{{ $data['inex'] }}</td>
                                <td>{{ $data['pihak'] }}</td>

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

                                <td>
                                    <ul>
                                        @foreach ($data['tindak'] as $index => $pihak)
                                            {{-- <li> --}}
                                                <strong>{{ $pihak }}</strong>
                                                <ul>
                                                    <li>{{ $data['tindak_lanjut'][$index] }}</li>
                                                </ul>
                                            {{-- </li> --}}
                                            <hr>
                                        @endforeach
                                    </ul>
                                </td>
                                <!-- Skor -->

                                <td>
                                    @foreach ($data['risk'] as $risiko)
                                        {{ $risiko }}<br>
                                    @endforeach
                                </td>

                                <td>
                                    @foreach ($data['scoreactual'] as $score)
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

                                <td>
                                    @foreach ($data['before'] as $risiko)
                                        {{ $risiko }}<br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($data['after'] as $risiko)
                                        {{ $risiko }}<br>
                                    @endforeach
                                </td>

                                <!-- Status -->
                                <td>
                                    @foreach ($data['status'] as $status)
                                        <span class="badge
                                            @if($status == 'OPEN')
                                                bg-danger
                                            @elseif($status == 'ON PROGRES')
                                                bg-warning
                                            @elseif($status == 'CLOSE')
                                                bg-success
                                            @endif">
                                            {{ $status }}<br>
                                            {{ $data['nilai_actual'] }}%
                                        </span><br>
                                    @endforeach
                                </td>

                            <!-- Action Buttons -->
                            <td>
                                <div class="btn-group" role="group">
                                    <!-- Edit Button -->
                                    <a href="{{ route('riskregister.edit', $data['id']) }}" title="Detail Risiko" class="btn btn-success btn-sm me-1">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('riskregister.destroy', $data['id']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="ri ri-delete-bin-fill"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
