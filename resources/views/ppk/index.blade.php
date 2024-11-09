@extends('layouts.main')

@section('content')
<div class="container">
    <h1 class="card-title">Proses Peningkatan Kinerja</h1>

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($ppks->isEmpty())
        <div class="alert alert-warning">Tidak ada data yang tersedia.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">No</th> <!-- Mengatur lebar kolom No dan meratakan teks ke tengah -->
                    {{-- <th style="width: 250px;">Judul</th> <!-- Mengatur lebar kolom Judul --> --}}
                    <th style="width: 60px; text-align: center;">Action</th> <!-- Mengatur lebar kolom Action dan meratakan teks ke tengah -->
                </tr>
            </thead>
            <tbody>
                @foreach ($ppks as $ppk)
                    <tr>
                        <td style="text-align: center;">{{ $ppk->nomor_surat ?? 'Tidak ada nomor surat' }}</td> <!-- Meratakan teks ke tengah -->
                        {{-- <td>{{ $ppk->judul }}</td> --}}
                        <td style="text-align: center;"> <!-- Meratakan teks ke tengah -->
                            <button type="button" title="Detail" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $ppk->id }}">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            <a href="{{ route('ppk.formppkkedua', $ppk->id) }}" class="btn btn-secondary btn-sm" title="Form PPK Kedua">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="{{ route('ppk.export', $ppk->id) }}" class="btn btn-success btn-sm" title="Export to Excel">
                                <i class="bi bi-file-earmark-excel-fill"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

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
                    <!-- Tampilkan Nomor Surat di atas Judul -->
                    <div class="mb-3">
                        <h6><strong>PPK NO. </strong> {{ $ppk->nomor_surat ?? 'Tidak ada nomor surat' }}</h6>
                    </div>

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
                            <th>Inisiator</th>
                            <td>{{ $ppk->pembuatUser->nama_user ?? 'Tidak ada nama inisiator' }}</td>
                        </tr>
                        <tr>
                            <th>Email Inisiator</th>
                            <td>{{ $ppk->emailpembuat }}</td>
                        </tr>
                        <tr>
                            <th>Divisi Inisiator</th>
                            <td>{{ $ppk->divisipembuat }}</td>
                        </tr>
                        <tr>
                            <th>Penerima</th>
                            <td>{{ $ppk->penerimaUser->nama_user ?? 'Tidak ada nama penerima' }}</td>
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
                            <td>
                                @php
                                    $ccEmails = explode(',', $ppk->cc_email);
                                @endphp
                                @if(count($ccEmails) > 0)
                                    <ul>
                                        @foreach($ccEmails as $email)
                                            <li>{{ trim($email) }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    Tidak ada CC Email.
                                @endif
                            </td>
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
                        <tr>
                            <th>Identifikasi</th>
                            <td>{{ $ppk->formppkkedua->identifikasi ?? 'Tidak ada identifikasi oleh Penerima' }}</td>
                        </tr>
                        <tr>
                            <th>Tanda Tangan Penerima</th>
                            <td>
                                @if ($ppk->formppkkedua && $ppk->formppkkedua->signaturepenerima)
                                    @php
                                        // Mendapatkan ekstensi file untuk mengecek apakah file adalah gambar
                                        $signatureExtension = pathinfo($ppk->formppkkedua->signaturepenerima, PATHINFO_EXTENSION);
                                    @endphp

                                    @if (in_array(strtolower($signatureExtension), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ asset('admin/img/' . $ppk->formppkkedua->signaturepenerima) }}" alt="Signature" style="max-width: 200px; display: block; margin-bottom: 10px;">
                                    @else
                                        Tidak ada tanda tangan
                                    @endif
                                @else
                                    Tidak ada tanda tangan
                                @endif
                            </td>
                        </tr>
                    </table>

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

<section class="section dashboard">
    <div class="activity d-flex justify-content-center flex-wrap">
            <!-- Recent Activity -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">TRACK RECORD <span>| History</span></h5>
                    <div class="activity">

                        <div class="activity-item d-flex">
                            <div class="activite-label">
                                <a href="{{ route('ppk.create') }}" class="btn btn-primary">Tahap 1</a>
                            </div>
                            <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                            <div class="activity-content">
                                Pengisian form pertama Judul, Jenis Ketidaksesuaian dan Identifikasi, Evaluasi Proses Peningkatan Kinerja
                            </div>
                        </div><!-- End activity item-->
                        <br>
                        <div class="activity-item d-flex">
                            <div class="activite-label">
                                <a class="btn btn-primary">Tahap 2</a>
                            </div>
                            <i class="bi bi-circle-fill activity-badge text-primary align-self-start"></i>
                            <div class="activity-content">
                                Pengisian form kedua Penanggulangan dan Pencegahan oleh Inisiator dan Penerima Proses Peningkatan Kinerja Diterima
                            </div>
                        </div>
                        <!-- End activity item -->
                        <br>

                        <div class="activity-item d-flex">
                            <div class="activite-label">
                                <a class="btn btn-warning">Prosses</a>
                            </div>
                            <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                            <div class="activity-content">
                                Proses Verifikasi Tindakan Penanggulangan dan Pencegahan 1 (satu) bulan dari Tanggal Verifikasi oleh Auditor
                            </div>
                        </div><!-- End activity item-->
                        <br>
                        <div class="activity-item d-flex">
                            <div class="activite-label">
                                <div class="activite-label">
                                    <a class="btn btn-warning">Tahap 3</a>
                                </div>
                            </div>
                            <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                            <div class="activity-content">
                                Verifikasi Tindakan Penanggulangan dan Pencegahan 1 (satu) bulan dari Tanggal Verifikasi oleh Auditor
                            </div>
                          </div><!-- End activity item-->
                    </div>
                </div>
            </div><!-- End Recent Activity -->
        </div><!-- End Right side columns -->
    </div>
</section>
@endsection
