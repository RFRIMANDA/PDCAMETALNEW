@extends('layouts.main') <!-- Ganti dengan layout yang Anda gunakan -->

@section('content')
<div class="container">
    <h1 class="card-title">Detail Risiko</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach ($resikos as $resiko)
        <div class="mb-4">

            <!-- Nama Resiko -->
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>Nama Risiko:</strong></div>
                        <div class="col-md-9">{{ $resiko->nama_resiko }}</div>
                    </div>
                </div>
            </div>

            <!-- Kriteria -->
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>Kriteria:</strong></div>
                        <div class="col-md-9">{{ $resiko->kriteria }}</div>
                    </div>
                </div>
            </div>

            <!-- Probability -->
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>Probability</strong>/Dampak:</div>
                        <div class="col-md-9">{{ $resiko->severity }}</div>
                    </div>
                </div>

            {{-- </div> --}}
            <!-- Severity -->
            {{-- <div class="card mb-2"> --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>Severity</strong>/Kemungkinan:</div>
                        <div class="col-md-9">{{ $resiko->probability }}</div>
                    </div>
                </div>
            </div>

            <!-- Tingkatan -->
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>Tingkatan:</strong></div>
                        <div class="col-md-9">{{ $resiko->tingkatan }}</div>
                    </div>
                </div>
            </div>


            <!-- Risk -->
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>Actual Risk:</strong></div>
                        <div class="col-md-9">
                            {{ $resiko->risk ?? '*Isi setelah tindakan lanjut berprogres' }}
                        </div>
                    </div>
                </div>
            </div>


            <!-- Status -->
            <div class="card mb-2" style="display: none;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>Status:</strong></div>
                        <div class="col-md-9">{{ $resiko->status }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>Before:</strong></div>
                        <div class="col-md-9">{{ $resiko->before ?? '*Isi setelah tindakan lanjut berprogres'}}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 font-weight-bold"><strong>After:</strong></div>
                        <div class="col-md-9">{{ $resiko->after ?? '*Isi setelah tindakan lanjut berprogres' }}</div>
                    </div>
                </div>
            </div>

            <!-- Action -->
                        <div class="col-md-10">
                            <a class="btn btn-danger" href="{{ route('riskregister.tablerisk', $resiko->riskregister->id_divisi) }}" title="Back">
                            <i class="bx bx-arrow-back"></i></a>
                             <a href="{{ route('resiko.edit', $resiko->id) }}" class="btn btn-warning" title="Edit Resiko">
                             <i class="bx bx-edit"></i>
                            <a>
                                <a href="{{ route('resiko.matriks', $resiko->id_riskregister) }}" title="Look Matriks" class="btn btn-primary">Matriks
                                <i class="bi bi-table"></i>
                            </a>

                        </div>

        </div>
    @endforeach

    <!-- Pagination -->
    <div class="mt-3">
        {{ $resikos->links() }}
    </div>
</div>
@endsection
