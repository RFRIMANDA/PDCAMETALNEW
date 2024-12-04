@extends('layouts.main')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Detail Proses Peningkatan Kinerja</h3>
        </div>
        <div class="card-body">
            <h6><strong>PPK NO: </strong>{{ $ppk->nomor_surat ?? 'Tidak ada nomor surat' }}</h6>
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
                        @if (count($ccEmails) > 0)
                            <ul>
                                @foreach ($ccEmails as $email)
                                    <li>{{ trim($email) }}</li>
                                @endforeach
                            </ul>
                        @else
                            Tidak ada CC Email.
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Evidence</th>
                    <td>
                        @if(!empty($ppk->evidence))
            @php
                $evidences = json_decode($ppk->evidence, true);  // Decode as an array
            @endphp
            @if(is_array($evidences) && count($evidences) > 0)
                <div id="evidencePreviewContainer" style="margin-top: 10px; display: flex; flex-wrap: wrap;">
                    @foreach($evidences as $evidence)
                        @php
                            $filePath = asset('storage/' . $evidence);
                            $fileExtension = pathinfo($evidence, PATHINFO_EXTENSION);
                        @endphp

                        <div class="evidence-item" style="margin-right: 15px; margin-bottom: 10px; text-align: center;">
                            <!-- Check if the file is an image (jpg, jpeg, png) and display it -->
                            @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                                <img src="{{ $filePath }}" alt="Evidence Image" style="max-width: 150px; height: auto; margin-bottom: 5px;">
                                <br>
                                <a href="{{ $filePath }}" download="{{ basename($evidence) }}" class="btn btn-sm btn-primary">Download</a>
                            @else
                                <!-- Display link for non-image files -->
                                <a href="{{ $filePath }}" target="_blank">{{ basename($evidence) }}</a>
                                <br>
                                <a href="{{ $filePath }}" download="{{ basename($evidence) }}" class="btn btn-sm btn-primary">Download</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p>No evidence uploaded.</p>
            @endif
        @else
            <p>No evidence uploaded.</p>
        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Terbit</th>
                    <td>{{ $ppk->created_at->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <th>Identifikasi</th>
                    <td>{{ $ppk->formppk2->identifikasi ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Penanggulangan</th>
                    <td>{{ $ppk->formppk2->penanggulangan ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Pencegahan</th>
                    <td>{{ $ppk->formppk2->pencegahan ?? '-' }}</td>
                </tr>
                <tr>
                    <th>PIC Penanggulangan</th>
                    <td>{{ $ppk->formppk2->pic1 ?? '-' }}</td>
                </tr>
                <tr>
                    <th>PIC Pencegahan</th>
                    <td>{{ $ppk->formppk2->pic2User->nama_user ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Verifikasi</th>
                    <td>{{ $ppk->formppk3->verifikasi ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tinjauan</th>
                    <td>{{ $ppk->formppk3->tinjauan ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $ppk->formppk3->status ?? '-' }}</td>
                </tr>
            </table>
            <a href="{{ route('ppk.index') }}" class="btn btn-secondary mt-3">Kembali</a>

        </div>
    </div>
</div>
@endsection
