@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Data PPK</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Jenis Ketidaksesuaian</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ppks as $ppk)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ppk->judul }}</td>
                    <td>{{ $ppk->jenisketidaksesuaian }}</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $ppk->id }}">
                            Detail
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal Detail Data PPK -->
    @foreach ($ppks as $ppk)
        <div class="modal fade" id="detailModal{{ $ppk->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $ppk->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel{{ $ppk->id }}">Detail Data PPK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Judul</th>
                                <td>{{ $ppk->judul }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Ketidaksesuaian</th>
                                <td>{{ $ppk->jenisketidaksesuaian }}</td>
                            </tr>
                            <tr>
                                <th>Pembuat</th>
                                <td>{{ $ppk->pembuat }}</td>
                            </tr>
                            <tr>
                                <th>Email Pembuat</th>
                                <td>{{ $ppk->emailpembuat }}</td>
                            </tr>
                            <tr>
                                <th>Divisi Pembuat</th>
                                <td>{{ $ppk->divisipembuat }}</td>
                            </tr>
                            <tr>
                                <th>Penerima</th>
                                <td>{{ $ppk->penerima }}</td>
                            </tr>
                            <tr>
                                <th>Email Penerima</th>
                                <td>{{ $ppk->emailpenerima }}</td>
                            </tr>
                            <tr>
                                <th>Divisi Penerima</th>
                                <td>{{ $ppk->divisipenerima }}</td>
                            </tr>
                            <tr>
                                <th>CC Email</th>
                                <td>{{ $ppk->ccemail }}</td>
                            </tr>
                            <tr>
                                <th>Evidence</th>
                                <td>
                                    @if ($ppk->evidence)
                                        <a href="{{ asset('storage/' . $ppk->evidence) }}" target="_blank">Download Evidence</a>
                                    @else
                                        Tidak ada file
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->
    @endforeach
</div>
@endsection
