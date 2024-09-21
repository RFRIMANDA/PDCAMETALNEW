@extends('layouts.main')

@section('style')
<style>
    /* Mengatur padding dan perataan kolom */
    table th, table td {
        padding: 10px;
        text-align: center;
        vertical-align: middle;
    }

    /* Mengatur lebar tabel agar menggunakan seluruh lebar container */
    table {
        width: 100%;
    }

    /* Menghindari teks memanjang ke bawah */
    table th {
        white-space: nowrap;
    }

    /* Memastikan teks panjang terpotong otomatis */
    table td {
        word-wrap: break-word;
    }

    /* Menambah batas maksimal untuk kolom dengan banyak data */
    .truncate {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

</style>
@endsection

@section('content')

<div class="container mt-5">
    <div class="text-center mb-4">
        <h1><span class="badge bg-primary">Table Risk Register & Opporunity </span></h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    

    <!-- Form Filter -->
    <form action="{{ route('list.biglist') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <!-- Select Divisi -->
                <label for="nama_divisi" class="card-title">Filter Divisi</label>
                <select name="nama_divisi" id="nama_divisi" class="form-select">
                    <option value="">Semua Divisi</option>
                    @foreach($Divisi as $div)
                        <option value="{{ $div->nama_divisi }}" {{ request('nama_divisi') == $div->nama_divisi ? 'selected' : '' }}>
                            {{ $div->nama_divisi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <!-- Select Status -->
                <label for="status" class="card-title">Filter Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>OPEN</option>
                    <option value="ON PROGRESS" {{ request('status') == 'ON PROGRESS' ? 'selected' : '' }}>ON PROGRESS</option>
                    <option value="CLOSE" {{ request('status') == 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                </select>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-success">Apply Filters</button>
        </div>
    </form>

    <!-- Tabel Data -->
    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 80px;">No.</th>
                    <th style="width: 200px;">Issue</th>
                    <th style="width: 200px;">Pihak Berkepentingan</th>
                    <th style="width: 150px;">Resiko (R)</th>
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
                    <td class="truncate">{{ $all->issue }}</td>
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
