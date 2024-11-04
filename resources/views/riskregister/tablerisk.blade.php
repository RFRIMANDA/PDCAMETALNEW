@extends('layouts.main')

@section('content')

<div class="card">
    <div class="card-body">
        <h5 class="card-title">TABLE RISK & OPPORTUNITY REGISTER  {{ $forms->first()->divisi->nama_divisi ?? '' }}</h5>
        <!-- Search form -->
        <form method="GET" action="{{ route('riskregister.tablerisk', $id) }}">
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label" for="keyword"><strong>Cari Issue:</strong></label>
                <div class="col-sm-7">
                    <input type="text" name="search" class="form-control" placeholder="Masukkan Issue" value="{{ request('search') }}">
                </div>
            </div>

        </form>
        <!-- Small tables -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Issue (Int:Ex)</th>
                    <th scope="col">Resiko</th>
                    <th scope="col">Tindakan Lanjut</th>
                    <th scope="col">Peluang</th>
                    <th scope="col">Target Penyelesaian</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1; // Inisialisasi variabel untuk nomor urut
                @endphp
                @foreach($forms as $form)
                    @php
                    // Ambil resiko yang terkait dengan form ini
                    $resikos = \App\Models\Resiko::where('id_riskregister', $form->id)->get();

                    // Cek apakah ada resiko dengan status CLOSE dan after tidak null
                    $isClosed = $resikos->contains(function ($resiko) {
                        return !is_null($resiko->after) && $resiko->status === 'CLOSE';
                    });
                    @endphp

                    @if (!$isClosed)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $form->issue }}</td>

                            <!-- Kolom Resiko -->
                            <td>
                                @if($resikos->isNotEmpty())
                                    @foreach($resikos as $resiko)
                                        {{ $resiko->nama_resiko }}
                                    @endforeach
                                @else
                                    None
                                @endif
                            </td>

                            <!-- Kolom pihak berkepentingan dan tindakan lanjut -->
                            <td>
                                @if(isset($data[$form->id]))
                                    <ul>
                                        @foreach($data[$form->id] as $tindakan)
                                            <li>
                                                <strong class="d-none">Pihak: {{ $tindakan->pihak }} </strong><!-- Menampilkan pihak sebagai string biasa -->
                                                <ul>
                                                    {{-- <li> --}}
                                                        <a href="{{ route('realisasi.index', $tindakan->id) }}">
                                                            {{ $tindakan->nama_tindakan }}
                                                        </a>
                                                        <div>
                                                            <span class="badge bg-success">{{ $tindakan->tgl_penyelesaian ?? '-' }}</span>
                                                        </div>

                                                        @if($tindakan->isClosed)
                                                            <span class="badge bg-danger">CLOSE</span>
                                                        @endif
                                                    {{-- </li> --}}
                                                </ul>
                                            </li>
                                            <hr>
                                        @endforeach
                                    </ul>
                                @else
                                    Tidak ada tindakan lanjut
                                @endif
                            </td>

                            <td>{{ $form->peluang ?? '-' }}</td>

                            <td>{{ $form->target_penyelesaian}}</td>

                            <td>
                                @if($resikos->isNotEmpty())
                                    @foreach($resikos as $resiko)
                                        <span class="badge
                                            @if($resiko->status == 'OPEN')
                                                bg-success
                                            @elseif($resiko->status == 'ON PROGRES')
                                                bg-warning
                                            @elseif($resiko->status == 'CLOSE')
                                                bg-danger
                                            @endif">
                                            {{ $resiko->status }}<br>
                                            {{ $form->nilai_actual }}%
                                        </span>

                                    @endforeach
                                @else
                                    None
                                @endif
                            </td>

                            <td class="action-col">
                                <a href="{{ route('riskregister.edit', $form->id) }}" title="Edit Issue" class="btn btn-danger">
                                    <i class="bx bx-edit"></i>
                                </a>

                                @if($resikos->isNotEmpty())
                                    <a href="{{ route('resiko.matriks', $form->id) }}" title="Matriks" class="btn btn-info">
                                        <i class="ri-grid-line"></i>
                                    </a>
                                @endif

                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal untuk Detail -->
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
    function loadDetail(id) {
        $.ajax({
            url: `/realisasi/${id}/detail`,
            method: 'GET',
            success: function(response) {
                if (response.length > 0) {
                    let modalContent = '';
                    response.forEach((detail, index) => {
                        modalContent += `
                            <div class="mb-3">
                                <label for="nama_realisasi_${index}" class="form-label"><strong>Nama Activity:</strong></label>
                                <textarea class="form-control" id="nama_realisasi_${index}" name="nama_realisasi[]" readonly>${detail.nama_realisasi}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tgl_penyelesaian_${index}" class="form-label"><strong>Tanggal Penyelesaian:</strong></label>
                                <input type="date" class="form-control" id="tgl_penyelesaian_${index}" name="tgl_penyelesaian[]" value="${detail.tgl_penyelesaian}" readonly>
                            </div>
                            <input type="hidden" name="id[]" value="${detail.id}">
                            <hr>
                        `;
                    });

                    $('#modalContent').html(modalContent);
                } else {
                    $('#modalContent').html('<p>Detail tidak tersedia.</p>');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                $('#modalContent').html(`<p>Terjadi kesalahan: ${xhr.responseText}</p>`);
            }
        });
    }

    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: '/realisasi/update',
            method: 'POST',
            data: formData,
            success: function(response) {
                alert('Data berhasil diperbarui!');
                $('#detailModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    });
</script>

@endsection
