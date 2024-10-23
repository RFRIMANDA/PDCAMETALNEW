<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>TRANSFORMATION || PDCA MANAGEMENT SYSTEM</title>
    <meta content="" name="description">
    <meta content="" name="keywords">


  <!-- Favicons -->
  <link href="{{ asset('admin/img/TML Logo.jpg') }}" rel="icon">
  <link href="{{ asset('admin/img/TML Logo.jpg') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Vendor CSS Files -->
  <link href="{{ asset('admin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

{{-- HEADER --}}
  <header id="header" class="header fixed-top d-flex align-items-center" style="background: linear-gradient(90deg, #87ceeb, #98FB98);">
    <div class="d-flex align-items-center justify-content-between">
        <a href="/" class="logo d-flex align-items-center">
            <span class="d-none d-lg-block" style="color: white; font-size: 1.5rem; font-weight: 700; margin-left: 10px; text-transform: uppercase; letter-spacing: 1px; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">Tata Metal Lestari</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn text-light fs-3"></i>
    </div>

<nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="" data-bs-toggle="dropdown">
                <img src="{{ asset('admin/img/TML3LOGO.png') }}" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; border: 2px solid #fff;">
                <span class="d-none d-md-block dropdown-toggle ps-2 text-dark">{{ Auth::user()->nama_user }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                <li class="dropdown-header">
                    <h6>Email: {{ Auth::user()->email }}</h6>
                    <span>Role: {{ Auth::user()->role }}</span>
                </li>

                <li><hr class="dropdown-divider">

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="/password">
                          <i class="ri-lock-password-fill"></i>
                          <span>Change Password</span>
                        </a>
                    </li>
                </li>

                <li><hr class="dropdown-divider">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="/logout">
                          <i class="bi bi-box-arrow-right"></i>
                          <span>Sign Out</span>
                        </a>
                      </li>
                </li>

          <li>
                <!-- Tambahkan item lainnya di sini jika diperlukan -->
            </ul>
        </li>
    </ul>
</nav>

</header>


<div class="container">
    <h1 class="card-title">Matriks Risiko: <br>{{ $resiko_nama }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><strong>Tingkatan = {{ $tingkatan }}</strong></h5>
            <p class="card-text"><strong>Probability = {{ $severity }}</strong></p>
            <p class="card-text"><strong>Severity = {{ $probability }}</strong></p>
            <p class="card-text"><strong>Probability x Severity = {{ $riskscore }}</strong></p>
            {{-- <p class="card-text"><strong>Nilai Actual Risk: {{$actual}}%</strong></p> --}}
        </div>
    </div>

    <h4><strong>MATRIKS BEFORE</strong></h4>
    <table class="table table-bordered text-center">
        <!-- Tabel matriks pertama -->
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Severity / Keparahan
                    <p>{{$kategori}}
                </th>
                <th colspan="5">Probability / Dampak (Likelihood)</th>
            </tr>
            <tr>
                <th>1 (Sangat Jarang Terjadi)</th>
                <th>2 (Jarang Terjadi)</th>
                <th>3 (Dapat Terjadi)</th>
                <th>4 (Sering Terjadi)</th>
                <th>5 (Selalu Terjadi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matriks_used as $i => $row) <!-- Menggunakan matriks_used -->
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    @if(isset($deskripsiSeverity[$i]))
                        {{ $deskripsiSeverity[$i] }}
                    @else
                        None
                    @endif
                </td>
                @foreach($row as $j => $value)
                <td style="background-color: {{ $colors_used[$i][$j] }}; color: black;"> <!-- Menggunakan colors_used -->
                    @if(($i + 1) == $severity && ($j + 1) == $probability)
                        <div class="d-flex align-items-center">
                            <div class="spinner-grow text-info" role="status" style="width: 1.5rem; height: 1.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">{{ $value }}</span>
                        </div>
                    @else
                        {{ $value }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <br>
    <hr>
    <br>
    <br>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><strong>ACTUAL RISK</strong></h5>
            <p class="card-text"><strong>Probability = {{ $severityrisk }}</strong></p>
            <p class="card-text"><strong>Severity = {{ $probabilityrisk }}</strong></p>
            <p class="card-text"><strong>Probability x Severity = {{ $riskscorerisk }}</strong></p>
            <p class="card-text"><strong>Nilai Actual Risk: {{$actual}}%</strong></p>
        </div>
    </div>

    <h4><strong>MATRIKS AFTER</strong></h4>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Severity / Keparahan
                    <p>{{$kategori}}
                </th>
                <th colspan="5">Probability / Dampak (Likelihood)</th>
            </tr>
            <tr>
                <th>1 (Sangat Jarang Terjadi)</th>
                <th>2 (Jarang Terjadi)</th>
                <th>3 (Dapat Terjadi)</th>
                <th>4 (Sering Terjadi)</th>
                <th>5 (Selalu Terjadi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matriks_used as $i => $row) <!-- Menggunakan matriksnew untuk matriks kedua -->
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    @if(isset($deskripsiSeverity[$i]))
                        {{ $deskripsiSeverity[$i] }}
                    @else
                        None
                    @endif
                </td>
                @foreach($row as $j => $value)
                <td style="background-color: {{ $colors_used[$i][$j] }}; color: black;">
                    @if(($i + 1) == $severityrisk && ($j + 1) == $probabilityrisk)
                        <div class="d-flex align-items-center">
                            <div class="spinner-grow text-warning" role="status" style="width: 1.5rem; height: 1.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">{{ $value }}</span>
                        </div>
                    @else
                        {{ $value }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

<a class="btn btn-danger" href="{{ route('riskregister.tablerisk', $samee) }}" title="Back">
    <i class="ri-arrow-go-back-line"></i>
</a>

    <a class="btn btn-warning" href="{{ route('resiko.edit', ['id' => $same]) }}" title="Back">
        <i class="bx bx-edit"></i>
    </a>
</div>

<footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>PT. TATA METAL LESTARI</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="">TATA METAL LESTARI PRODUCTION</a>
    </div>
  </footer>
</html>
