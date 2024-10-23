@extends('layouts.main')

@section('content')

<div class="card">
    <div class="card-body">
        <h5 class="card-title">TABLE RISK & OPPORTUNITY REGISTER  {{ $divisi->nama_divisi ?? '' }}</h5>
        <!-- Small tables -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Issue</th>
                    <th scope="col">Resiko</th> <!-- Pindahkan kolom Resiko ke sini -->
                    <th scope="col">Pihak Berkepentingan & Tindakan Lanjut</th> <!-- Pindahkan kolom Pihak Berkepentingan ke sini -->
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
                        return !is_null($resiko->after) && $resiko->status === 'CLOSE'; // Memastikan status juga CLOSE
                    });
                    @endphp

                    @if (!$isClosed)
                        <tr>
                            <td>{{ $no++ }}</td> <!-- Menggunakan variabel $no yang akan meningkat setiap kali data ditampilkan -->
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
                                @if(isset($divisi[$form->id]) && isset($data[$form->id]))
                                    <ul>
                                        @foreach($divisi[$form->id] as $index => $name)
                                            <li>
                                                <strong>{{ $name->nama_divisi }}</strong>
                                                <ul>
                                                    @foreach($data[$form->id] as $tindakan)
                                                        @if($tindakan->pihak == $name->id) <!-- Sesuaikan ID divisi dan tindakan -->
                                                            <li>
                                                                <a href="{{ route('realisasi.index', $tindakan->id) }}">
                                                                    {{ $tindakan->nama_tindakan }}
                                                                </a>
                                                                <div>
                                                                    <span class="badge bg-success">{{ $tindakan->tgl_penyelesaian ?? '-' }}</span>
                                                                </div>

                                                                @if($tindakan->isClosed)
                                                                    <span class="badge bg-danger">CLOSE</span>
                                                                @endif

                                                                <hr>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </li>
                                            <hr>
                                        @endforeach
                                    </ul>
                                @else
                                    Tidak ada pihak berkepentingan
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
                                            {{ $resiko->status }}
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
            url: `/realisasi/${id}/detail`, // Sesuaikan dengan route
            method: 'GET',
            success: function(response) {
                if (response.length > 0) {
                    let modalContent = ''; // Buat form isian edit dari data yang diterima
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
                            <input type="hidden" name="id[]" value="${detail.id}"> <!-- Tambahkan ID untuk update -->
                            <hr>
                        `;
                    });

                    $('#modalContent').html(modalContent); // Masukkan data ke dalam modal
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

    // Tangani submit form dan kirim data menggunakan AJAX
    $('#editForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah submit form secara default

        let formData = $(this).serialize(); // Ambil semua data dari form

        $.ajax({
            url: '/realisasi/update', // Sesuaikan dengan route update di Laravel
            method: 'POST',
            data: formData,
            success: function(response) {
                // Tampilkan pesan sukses atau lakukan sesuatu setelah data berhasil disimpan
                alert('Data berhasil diperbarui!');
                $('#detailModal').modal('hide'); // Tutup modal
                location.reload(); // Refresh halaman (opsional)
            },
            error: function(xhr) {
                // Tampilkan pesan error jika ada kesalahan
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    });
</script>

@endsection
