@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Proses Peningkatan Kinerja</h1>

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
                        <button type="button" title="Detail" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $ppk->id }}">
                            <i class="bi bi-eye-fill">

                            </i>
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
                        <h5 class="card-title" id="detailModalLabel{{ $ppk->id }}">Detail Data Proses Peningkatan Kinerja</h5>
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
                                <th>Tanda Tangan Inisiator/Auditor</th>
                                <td>
                                    @if ($ppk->signature)
                                        @php
                                            // Mendapatkan ekstensi file untuk mengecek apakah file adalah gambar
                                            $signatureExtension = pathinfo($ppk->signature, PATHINFO_EXTENSION);
                                        @endphp

                                        @if (in_array(strtolower($signatureExtension), ['jpg', 'jpeg', 'png']))
                                            <!-- Preview tanda tangan -->
                                            <img src="{{ asset('admin/img/' . $ppk->signature) }}" alt="Signature" style="max-width: 200px; display: block; margin-bottom: 10px;">
                                        @endif
                                    @else
                                        Tidak ada tanda tangan
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Evidence</th>
                                <td>
                                    @if ($ppk->evidence)
                                        @php
                                            // Mendapatkan ekstensi file untuk mengecek apakah file adalah gambar
                                            $extension = pathinfo($ppk->evidence, PATHINFO_EXTENSION);
                                        @endphp

                                        @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                            <!-- Preview gambar -->
                                            <img src="{{ asset('dokumen/' . $ppk->evidence) }}" alt="Evidence Image" style="max-width: 200px; display: block; margin-bottom: 10px;">
                                        @endif

                                        <!-- Link download -->
                                            <!-- Button download -->
                                        <a href="{{ asset('dokumen/' . $ppk->evidence) }}" target="_blank" class="btn btn-primary">
                                            <i class="ri-download-line"></i>Download
                                        </a>
                                    @else
                                        Tidak ada file
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Terbit</th>
                                <td>{{ $ppk->created_at->format('d-m-Y') }}</td>
                            </tr>
                        </table>

                        <section class="section dashboard">
                            <div class="activity d-flex justify-content-center flex-wrap">
                                    <!-- Recent Activity -->
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">TRACK RECORD <span>| History</span></h5>
                                            <div class="activity">
                                                <div class="activity-item d-flex">
                                                    <div class="activite-label">1 MINUTE</div>
                                                    <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                                    <div class="activity-content">
                                                        Pengisian form pertama Judul dan Jenis Ketidaksesuaian Proses Peningkatan Kinerja
                                                    </div>
                                                </div><!-- End activity item-->

                                                <div class="activity-item d-flex">
                                                    <div class="activite-label">1 HOUR</div>
                                                    <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                                                    <div class="activity-content">
                                                        Form Judul dan Jenis Ketidaksesuaian Proses Peningkatan Kinerja Diterima
                                                    </div>
                                                </div><!-- End activity item-->

                                                <div class="activity-item d-flex">
                                                    <div class="activite-label">1 DAY</div>
                                                    <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                                    <div class="activity-content">
                                                        Pengisian form Identifikasi dan Evaluasi Proses Peningkatan Kinerja
                                                    </div>
                                                </div><!-- End activity item-->

                                                <div class="activity-item d-flex">
                                                    <div class="activite-label">1 WEEK</div>
                                                    <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                                                    <div class="activity-content">
                                                        Pengisian form Penanggulangan dan Pencegahan Proses Peningkatan Kinerja
                                                    </div>
                                                </div><!-- End activity item-->

                                                <div class="activity-item d-flex">
                                                    <div class="activite-label">1 MONTH</div>
                                                    <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                                                    <div class="activity-content">
                                                        Tahap Tinjauan dan Verifikasi Tindakan dalam kurun waktu 1 Bulan
                                                    </div>
                                                </div><!-- End activity item-->

                                                <div class="activity-item d-flex">
                                                    <div class="activite-label">1 YEAR</div>
                                                    <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                                                    <div class="activity-content">
                                                        Tinjauan Status Penanggulangan dan  Efektivitas Penyelesaian
                                                    </div>
                                                </div><!-- End activity item-->
                                            </div>
                                        </div>
                                    </div><!-- End Recent Activity -->
                                </div><!-- End Right side columns -->
                            </div>
                        </section>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->

    @endforeach
</div>

<style>
.activity {
    margin-top: 20px; /* Memberikan jarak antara tabel dan aktivitas */
}

.activity-item {
    padding: 10px; /* Memberikan padding pada setiap item aktivitas */
    flex: 0 1 auto; /* Menjaga lebar item agar tidak menyusut */
    min-width: 150px; /* Menetapkan lebar minimum untuk setiap item aktivitas */
}

.activity-content {
    margin-left: 10px; /* Memberikan jarak antara ikon dan konten aktivitas */
}

</style>
@endsection
