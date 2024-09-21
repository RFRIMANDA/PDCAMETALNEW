@extends('layouts.main')

@section('content')

<div class="container mt-5">
    <div class="text-center mb-4">
        <h1><span class="badge bg-warning">Detail Tindak Lanjut</span></h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Tindak Lanjut</th>
                    <th>Realisasi</th>
                    <th>Tanggal Penyelesaian</th>
                </tr>
            </thead>
            <tbody>
            @foreach($listKecil as $kecil)
                        <tr>
                                @foreach($same as $tindak)
                                <td>{{ $tindak->nama_tindakan }}</td>
                                @endforeach
                            <td>{{ $kecil->realisasi ?? 'Tidak ada' }}</td>
                            <td>{{ $kecil->date ?? 'Tidak ada' }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Tabel Member PIC -->
                <table class="table table-striped table-bordered mt-4">
                    <thead class="thead-dark">
                        <tr>
                            <th colspan="2">PIC MEMBER</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Responsible</td>
                            <td>{{ $kecil->responsible ?? 'Tidak ada' }}</td>
                        </tr>
                        <tr>
                            <td>Accountable</td>
                            <td>{{ $kecil->accountable ?? 'Tidak ada' }}</td>
                        </tr>
                        <tr>
                            <td>Consulted</td>
                            <td>{{ $kecil->consulted ?? 'Tidak ada' }}</td>
                        </tr>
                        <tr>
                            <td>Informed</td>
                            <td>{{ $kecil->informed ?? 'Tidak ada' }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Tabel Anum Goal -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Anum Goal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $kecil->anumgoal ?? 'Tidak ada' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Anum Budget -->
                    <div class="col-md-6">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Anum Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ isset($kecil->anumbudget) ? 'IDR ' . number_format((float)$kecil->anumbudget, 0, ',', '.') : 'Tidak ada' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </div>

                <!-- Tabel Description -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $kecil->desc ?? 'Tidak ada deskripsi' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
            <a href="javascript:history.back()" class="btn btn-primary">Edit</a>
            <a class="btn btn-danger" href="{{ route('list.listregister') }}">Back</a>


        <!-- Tautan Edit -->
        <div class="text-right mt-4">
        </div>
    </div>
</div>

@endsection
