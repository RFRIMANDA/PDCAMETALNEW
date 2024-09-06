@extends('layouts.main')

@section('content')

<section class="section dashboard">
    <div class="container-fluid">
        <div class="row justify-content-center">

            <!-- Card Container -->
            <div class="d-flex flex-wrap justify-content-center align-items-start">

                <!-- Sales Card 1 -->
                <div class="col-xxl-4 col-md-6 mb-4">
                    <div class="card info-card sales-card">
                        <button class="card-body btn btn-light animate-card" style="border: none; padding: 0; text-align: left;" onclick="window.location.href='{{ route('list.listregister') }}'">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-file-text-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>List Register</h6>
                                </div>
                            </div>
                        </button>
                    </div>
                </div><!-- End Sales Card 1 -->

                <!-- Sales Card 2 -->
                <div class="col-xxl-4 col-md-6 mb-4">
                    <div class="card info-card sales-card">
                        <button class="card-body btn btn-light animate-card" style="border: none; padding: 0; text-align: left;" onclick="window.location.href='/your-target-url';">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>PPK</h6>
                                </div>
                            </div>
                        </button>
                    </div>
                </div><!-- End Sales Card 2 -->

                <!-- Customers Card 3 (Positioned below center) -->
                <div class="col-xxl-4 col-md-6 mb-4 d-flex justify-content-center">
                    <div class="card info-card sales-card">
                        <button class="card-body btn btn-light animate-card" style="border: none; padding: 0; text-align: left;" onclick="window.location.href='/your-target-url';">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-bookmark-check-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>Pengajuan Nomor Berita Acara</h6>
                                </div>
                            </div>
                        </button>
                    </div>
                </div><!-- End Customers Card 3 -->

            </div><!-- End Card Container -->

        </div><!-- End row -->
    </div><!-- End container-fluid -->
</section>

@endsection
