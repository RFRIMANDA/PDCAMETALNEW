@extends('layouts.main')

@section('content')

<style>
    body {
        background: linear-gradient(135deg, #f0f4f8, #e2e2e2); /* Gradasi latar belakang */
    }

    .animate-card {
        transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden; /* Menghindari elemen keluar dari batas kartu */
    }

    .animate-card:hover {
        transform: scale(1.05);
        background-color: #ffffff; /* Warna latar belakang saat hover */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Bayangan lebih dalam saat hover */
    }

    .animate-card:active {
        transform: scale(0.95);
    }

    .clicked {
        animation: clickEffect 0.3s forwards;
    }

    @keyframes clickEffect {
        transform: scale(0.95);
        opacity: 0.9;
    }

    .card-icon {
        background-color: #007bff; /* Warna latar belakang ikon */
        padding: 12px;
        border-radius: 50%;
        font-size: 32px; /* Ukuran ikon yang lebih besar */
        color: white; /* Warna ikon putih */
    }

    h6 {
        margin: 0;
        font-weight: bold;
        color: #333; /* Warna teks judul */
        line-height: 1.5; /* Spasi antara baris judul */
    }

    .alert {
        margin-bottom: 20px; /* Ruang di bawah alert */
        font-weight: bold; /* Teks tebal untuk alert */
    }
</style>

<section class="section dashboard">
    <br>
    <div class="container-fluid">
        <div class="row justify-content-center">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }} {{ Auth::user()->nama_user }} 👋
                </div>
            @endif

            <!-- Card Container -->
            <div class="d-flex flex-wrap justify-content-center align-items-start">

                <!-- Sales Card 1 -->
                <div class="col-xxl-4 col-md-6 mb-4">
                    <div class="card info-card sales-card">
                        <button class="card-body btn btn-light animate-card" style="border: none; padding: 0; text-align: left;" onclick="window.location.href='{{ route('riskregister.biglist') }}'">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-file-text-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>Risk & Opportunity <br>Register</h6>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
                <!-- End Sales Card 1 -->

                <!-- Sales Card 2 -->
                <div class="col-xxl-4 col-md-6 mb-4">
                    <div class="card info-card sales-card">
                        <button class="card-body btn btn-light animate-card" style="border: none; padding: 0; text-align: left;" onclick="window.location.href='{{ route('ppk.index')}}'">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>Proses Peningkatan <br>Kinerja (PPK)</h6>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
                <!-- End Sales Card 2 -->
            </div><!-- End Card Container -->
        </div><!-- End row -->
    </div><!-- End container-fluid -->
</section>

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

<script>
    document.querySelectorAll('.animate-card').forEach(button => {
        button.addEventListener('click', function () {
            this.classList.add('clicked');
            setTimeout(() => {
                this.classList.remove('clicked');
            }, 300); // Durasi animasi
        });
    });
</script>

@endsection
