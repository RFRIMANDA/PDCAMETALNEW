@extends('layouts.main')

@section('content')
<div class="container">
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <h5 class="card-title text-center text-uppercase fw-bold text-primary">Edit Proses Peningkatan Kinerja</h5>
            <hr class="mb-4" style="border: 1px solid #0d6efd;">

            <form method="POST" action="{{ route('ppk.update', $ppk->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Judul PPK -->
                <div class="mb-3">
                    <label for="inputJudul" class="form-label fw-bold">Judul PPK</label>
                    <textarea name="judul" class="form-control" placeholder="Masukkan Judul PPK" rows="3">{{ old('judul', $ppk->judul) }}</textarea>
                </div>

                <!-- Jenis Ketidaksesuaian -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Jenis Ketidaksesuaian</label>
                    @php
                        $selectedJenis = explode(',', $ppk->jenisketidaksesuaian ?? '');
                    @endphp
                    @foreach(['SISTEM', 'PROSES', 'PRODUK', 'AUDIT'] as $jenis)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jenisketidaksesuaian[]" value="{{ $jenis }}"
                                {{ in_array($jenis, $selectedJenis) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $jenis }}</label>
                        </div>
                    @endforeach
                </div>

                <!-- Nama Inisiator (Pembuat) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="pembuat" class="form-label fw-bold">Nama Inisiator</label>
                        <select id="pembuat" name="pembuat" class="form-select">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    data-email="{{ $user->email }}"
                                    data-divisi="{{ $user->divisi }}"
                                    {{ old('pembuat', $ppk->pembuat) == $user->id ? 'selected' : '' }}>
                                    {{ $user->nama_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="emailpembuat" class="form-label fw-bold">Email Inisiator</label>
                        <input type="email" id="emailpembuat" name="emailpembuat" class="form-control"
                            value="{{ old('emailpembuat', $ppk->emailpembuat) }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="divisipembuat" class="form-label fw-bold">Divisi Inisiator</label>
                    <input type="text" id="divisipembuat" name="divisipembuat" class="form-control"
                        value="{{ old('divisipembuat', $ppk->divisipembuat) }}" readonly>
                </div>

                <!-- Penerima -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="penerima" class="form-label fw-bold">Penerima</label>
                        <select id="penerima" name="penerima" class="form-select">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    data-email="{{ $user->email }}"
                                    data-divisi="{{ $user->divisi }}"
                                    {{ old('penerima', $ppk->penerima) == $user->id ? 'selected' : '' }}>
                                    {{ $user->nama_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="emailpenerima" class="form-label fw-bold">Email Penerima</label>
                        <input type="email" id="emailpenerima" name="emailpenerima" class="form-control"
                            value="{{ old('emailpenerima', $ppk->emailpenerima) }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="divisipenerima" class="form-label fw-bold">Divisi Penerima</label>
                    <input type="text" id="divisipenerima" name="divisipenerima" class="form-control"
                        value="{{ old('divisipenerima', $ppk->divisipenerima) }}" readonly>
                </div>

                <script>
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
                </script>

                <!-- Evidence -->
<div class="mb-3">
    <div class="mb-3 row">
        <label for="evidence" class="form-label fw-bold">Evidence</label>
        <div class="col-sm-10">
            <!-- Input file multiple -->

            <!-- Display the uploaded evidence images (if any) -->
            @if(!empty($ppk->evidence))
                @php
                    $evidences = json_decode($ppk->evidence, true);  // Decode as an array
                @endphp
                @if(is_array($evidences) && count($evidences) > 0)
                    <div id="evidencePreviewContainer" class="d-flex flex-wrap mt-3">
                        @foreach($evidences as $index => $evidence)
                            @php
                                $filePath = asset('storage/' . $evidence);
                                $fileExtension = pathinfo($evidence, PATHINFO_EXTENSION);
                            @endphp

                            <div class="evidence-item text-center me-3 mb-2">
                                <!-- Check if the file is an image (jpg, jpeg, png) and display it -->
                                @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $filePath }}" alt="Evidence Image" class="img-thumbnail" style="max-width: 150px; height: auto;">
                                    <br>
                                    <a href="{{ $filePath }}" download="{{ basename($evidence) }}" class="btn btn-sm btn-primary mt-2">Download</a>
                                @else
                                    <!-- Display link for non-image files -->
                                    <a href="{{ $filePath }}" target="_blank">{{ basename($evidence) }}</a>
                                    <br>
                                    <a href="{{ $filePath }}" download="{{ basename($evidence) }}" class="btn btn-sm btn-primary mt-2">Download</a>
                                @endif
                                <br>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No evidence uploaded.</p>
                @endif
            @else
                <p>No evidence uploaded.</p>
            @endif
        </div>
    </div>
                <!-- CC Email -->
                <div class="mb-3">
                    <label class="form-label fw-bold">CC Email</label>
                    <div id="cc-email-container">
                        @php
                            $ccEmails = explode(',', $ppk->cc_email ?? '');
                        @endphp
                        @foreach($ccEmails as $cc)
                            @if($cc)
                                <div class="input-group mb-2">
                                    <input type="email" name="cc_email[]" class="form-control" value="{{ $cc }}">
                                    <button type="button" class="btn btn-outline-danger remove-cc-email">-</button>
                                </div>
                            @endif
                        @endforeach

                    </div>
                    <button type="button" class="btn btn-outline-primary add-cc-email">+ CC Email</button>

                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update <i class="ri-save-3-fill"></i></button>
                </div>

            <script>
                // Add CC Email functionality
                document.querySelector('.add-cc-email').addEventListener('click', function() {
                    const container = document.getElementById('cc-email-container');

                    // Prevent adding more than 5 CC emails
                    if (container.querySelectorAll('.input-group').length < 10) {
                        const inputGroup = document.createElement('div');
                        inputGroup.className = 'input-group mb-2';
                        inputGroup.innerHTML = `
                            <input type="email" name="cc_email[]" class="form-control" placeholder="Masukkan CC Email">
                            <button type="button" class="btn btn-outline-danger remove-cc-email">-</button>
                        `;
                        container.appendChild(inputGroup);

                        // Add event listener to the remove button
                        inputGroup.querySelector('.remove-cc-email').addEventListener('click', function() {
                            container.removeChild(inputGroup);
                        });
                    } else {
                        alert('You can add a maximum of 10 CC emails.');
                    }
                });

                // Attach event listener to existing remove buttons (for CC emails pre-loaded into the form)
                document.querySelectorAll('.remove-cc-email').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const container = document.getElementById('cc-email-container');
                        const inputGroup = button.closest('.input-group');
                        container.removeChild(inputGroup);
                    });
                });
            </script>
@endsection
