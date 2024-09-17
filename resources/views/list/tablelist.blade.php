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
                    <th style="width: 150px;">Action</th>
                    <th style="width: 200px;">Last Update</th>
                    <th style="width: 200px;">Before</th>
                    <th style="width: 200px;">After</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forms as $form)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $form->issue }}</td>

                        <td>
                            @foreach($divisi[$form->id] as $name)
                                {{ $name->nama_divisi }}
                                <hr>
                            @endforeach
                        </td>

                        <td>
                            @foreach($data[$form->id] as $tindakan)
                                {{ $tindakan->resiko }}
                                <hr>
                            @endforeach
                        </td>
                        <td>{{ $form->peluang }}</td>
                        <td>{{ $form->tingkatan }}</td>
                        <td>
                            @foreach($data[$form->id] as $tindakan)
                                {{ $tindakan->nama_tindakan }}
                                <a href="{{ route('listkecil.show', $tindakan->id) }}"><br>
                                    <i class="bi bi-caret-down-square-fill"></i> DETAIL
                                </a>
                                <a href="javascript:void(0)" onclick="loadDetail('{{ $form->id }}', {{ $loop->index }})" data-bs-toggle="modal" data-bs-target="#detailModal"><br>
                                    <i class="fa fa-eye"></i> Track Record
                                </a>
                                <hr>
                            @endforeach
                        </td>

                        <td>
                            @foreach($data[$form->id] as $tindakan)
                                {{ $tindakan->pic }}
                                <hr>
                            @endforeach
                        </td>
                        <td>{{ $form->status }}</td>
                        <td>{{ $form->risk }}</td>
                        
                        <td class="action-col">
                            <a href="{{ route('list.edit', $form->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <a href="#" class="btn btn-success btn-sm">Print</a>
                        </td>
                        <td>{{ $form->updated_at }}</td>
                        <td>{{ $form->before }}</td>
                        <td>{{ $form->after }}</td>
                    </tr>
                @endforeach
            </tbody>



        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Track Record Tindak Lanjut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    function loadDetail(id, index) {
    $.ajax({
        url: `/listkecil/${id}/detail/${index}`, // Pastikan ini sesuai dengan route Anda
        method: 'GET',
        success: function(response) {
            if (response.realisasi && response.date) {
                $('#modalContent').html(`
                    <form id="detailForm">
                        <div class="mb-3">
                            <label for="realisasi" class="form-label">Realisasi:</label>
                            <textarea class="form-control" id="realisasi" name="realisasi">${response.realisasi}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Tanggal Penyelesaian:</label>
                            <input type="date" class="form-control" id="date" name="date" value="${response.date}" readonly>
                        </div>
                    </form>
                `);
            } else {
                $('#modalContent').html('<p>Detail tidak tersedia.</p>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.error('Status:', status);
            console.error('XHR:', xhr.responseText);
            $('#modalContent').html(`<p>Terjadi kesalahan: ${xhr.responseText}</p>`);
        }
    });
}


</script>



<style>
    .table {
        table-layout: fixed;
    }
    .table th, .table td {
        max-width: 200px;
        overflow: hidden;
        word-wrap: break-word;
        white-space: normal;
    }
    .sticky-header {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }
    .sticky-cell {
        position: -webkit-sticky;
        position: sticky;
        background: #fff;
        z-index: 1;
    }
    .wrap-text {
        word-wrap: break-word;
        white-space: normal;
    }
</style>
@endsection