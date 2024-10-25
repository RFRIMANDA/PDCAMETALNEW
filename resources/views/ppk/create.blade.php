<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>FORM || PROSES PENINGKATAN KINERJA</title>
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
</head>

<body>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">PROSES PENINGKATAN KINERJA</h5>

            <!-- General Form Elements -->
            <form method="POST" action="{{ route('ppk.store') }}">
                @csrf
                <div class="row mb-3">
                    <label for="inputJudul" class="col-sm-2 col-form-label">Judul PPK</label>
                    <div class="col-sm-10">
                        <textarea name="judul" class="form-control">{{ old('judul') }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="inputJenis" class="col-sm-2 col-form-label">Jenis Ketidaksesuaian</label>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="Sistem">
                            <label class="form-check-label" for="Sistem">Sistem</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="Proses">
                            <label class="form-check-label" for="Proses">Proses</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="Produk">
                            <label class="form-check-label" for="Produk">Produk</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="Audit">
                            <label class="form-check-label" for="Audit">Audit</label>
                        </div>
                    </div>
                </div>


                <div class="row mb-3">
                    <label for="inputPembuat" class="col-sm-2 col-form-label">Pembuat</label>
                    <div class="col-sm-10">
                        <input type="text" name="pembuat" class="form-control" value="{{ old('pembuat') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="inputEmailPembuat" class="col-sm-2 col-form-label">Email Pembuat</label>
                    <div class="col-sm-10">
                        <input type="email" name="emailpembuat" class="form-control" value="{{ old('emailpembuat') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="inputDivisiPembuat" class="col-sm-2 col-form-label">Divisi Pembuat</label>
                    <div class="col-sm-10">
                        <input type="text" name="divisipembuat" class="form-control" value="{{ old('divisipembuat') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="inputPenerima" class="col-sm-2 col-form-label">Penerima</label>
                    <div class="col-sm-10">
                        <input type="text" name="penerima" class="form-control" value="{{ old('penerima') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="inputEmailPenerima" class="col-sm-2 col-form-label">Email Penerima</label>
                    <div class="col-sm-10">
                        <input type="email" name="emailpenerima" class="form-control" value="{{ old('emailpenerima') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="inputDivisiPenerima" class="col-sm-2 col-form-label">Divisi Penerima</label>
                    <div class="col-sm-10">
                        <input type="text" name="divisipenerima" class="form-control" value="{{ old('divisipenerima') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </form><!-- End General Form Elements -->
        </div>
    </div>
</body>

<!-- Tambahkan jQuery dan jQuery UI untuk autocomplete -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script type="text/javascript">
    $(document).ready(function() {
        // Autocomplete untuk pembuat
        $("#pembuat").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('autocomplete.userppk') }}",
                    data: {
                        term: request.term
                    },
                    dataType: "json",
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.nama,
                                value: item.nama,
                                email: item.email,
                                divisi: item.divisi
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('#pembuat').val(ui.item.value); // Nama
                $('#emailpembuat').val(ui.item.email); // Isi email
                $('#divisipembuat').val(ui.item.divisi); // Isi divisi
                return false;
            }
        });

        // Autocomplete untuk penerima
        $("#penerima").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('autocomplete.userppk') }}",
                    data: {
                        term: request.term
                    },
                    dataType: "json",
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.nama,
                                value: item.nama,
                                email: item.email,
                                divisi: item.divisi
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('#penerima').val(ui.item.value); // Nama
                $('#emailpenerima').val(ui.item.email); // Isi email
                $('#divisipenerima').val(ui.item.divisi); // Isi divisi
                return false;
            }
        });
    });
</script>


