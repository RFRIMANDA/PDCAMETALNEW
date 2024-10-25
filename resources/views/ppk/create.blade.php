@extends('layouts.main')

@section('content')

<body>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">PROSES PENINGKATAN KINERJA</h5>

            <!-- General Form Elements -->
            <form method="POST" action="{{ route('ppk.store') }}">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Judul PPK -->
                <div class="row mb-3">
                    <label for="inputJudul" class="col-sm-2 col-form-label">Judul PPK</label>
                    <div class="col-sm-10">
                        <textarea name="judul" class="form-control" placeholder="Masukkan Judul PPK">{{ old('judul') }}</textarea>
                    </div>
                </div>
                
                <!-- Jenis Ketidaksesuaian -->
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
                
                <!-- Pembuat dan Divisi Pembuat -->
                <div class="row mb-3">
                    <label for="pembuat" class="col-sm-2 col-form-label">Pembuat</label>
                    <div class="col-sm-10">
                        <select id="pembuat" name="pembuat" class="form-control">
                            <option value="">Pilih Pembuat</option>
                            @foreach($data as $user)
                                <option value="{{ $user->nama }}" data-email="{{ $user->email }}" data-divisi="{{ $user->divisi }}">
                                    {{ $user->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="emailpembuat" class="col-sm-2 col-form-label">Email Pembuat</label>
                    <div class="col-sm-10">
                        <input type="email" id="emailpembuat" name="emailpembuat" class="form-control" value="{{ old('emailpembuat') }}" placeholder="Email Pembuat" readonly>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="divisipembuat" class="col-sm-2 col-form-label">Divisi Pembuat</label>
                    <div class="col-sm-10">
                        <input type="text" name="divisipembuat" id="divisipembuat" class="form-control" value="{{ old('divisipembuat') }}" placeholder="Divisi Pembuat" readonly>
                    </div>
                </div>
                
                <!-- Lakukan hal yang sama untuk Penerima -->
                <div class="row mb-3">
                    <label for="penerima" class="col-sm-2 col-form-label">Penerima</label>
                    <div class="col-sm-10">
                        <select id="penerima" name="penerima" class="form-control">
                            <option value="">Pilih Penerima</option>
                            @foreach($data as $user)
                                <option value="{{ $user->nama }}" data-email="{{ $user->email }}" data-divisi="{{ $user->divisi }}">
                                    {{ $user->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="emailpenerima" class="col-sm-2 col-form-label">Email Penerima</label>
                    <div class="col-sm-10">
                        <input type="email" name="emailpenerima" id="emailpenerima" class="form-control" value="{{ old('emailpenerima') }}" placeholder="Email Penerima" readonly>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="divisipenerima" class="col-sm-2 col-form-label">Divisi Penerima</label>
                    <div class="col-sm-10">
                        <input type="text" name="divisipenerima" id="divisipenerima" class="form-control" value="{{ old('divisipenerima') }}" placeholder="Divisi Penerima" readonly>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>            
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Mengisi data pembuat
        document.getElementById("pembuat").addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById("emailpembuat").value = selectedOption.getAttribute("data-email");
            document.getElementById("divisipembuat").value = selectedOption.getAttribute("data-divisi");
        });

        // Mengisi data penerima
        document.getElementById("penerima").addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById("emailpenerima").value = selectedOption.getAttribute("data-email");
            document.getElementById("divisipenerima").value = selectedOption.getAttribute("data-divisi");
        });
    });
</script>

@endsection
