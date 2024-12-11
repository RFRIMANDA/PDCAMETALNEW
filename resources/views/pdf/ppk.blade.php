<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report PPK {{$nomor_surat}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            padding: 10px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 80px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .no-border {
            border: none !important;
        }
        .signature {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen PPK</title>
</head>
<body>
    <div style="border: 1px solid #000; padding: 10px; height: 100%; box-sizing: border-box;">
        <div style="border: 1px solid #000; padding: 10px; display: flex; align-items: center;">
            <img src="{{ asset('admin/img/TML3LOGO.png') }}" alt="Profile" class="rounded-circle"
                 style="width: 100px; height: 100px; border: 2px solid #fff; transition: transform 0.3s; margin-right: 10px;">
            <strong style="font-size: 20px; text-align: center; flex-grow: 1; margin-left: -100px;">PROSES PENINGKATAN KINERJA</strong>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <td colspan="2" style="font-weight: bold; text-align: left; border:none">KEPADA</td>
                <td colspan="2" style="font-weight: bold; text-align: left; border:none">PPK NO. {{ $nomor_surat }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; border:none">Penerima</td>
                <td style="border:none">{{ $ppk->penerimaUser->nama_user }}</td>
                <td style="font-weight: bold; border:none">Pembuat / Inisiator</td>
                <td style="border:none">{{$ppk->pembuatUser->nama_user}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; border:none">Departemen Pembuat</td>
                <td style="border:none">{{ $divisipembuat }}</td>
                <td style="font-weight: bold; border:none">Departemen Penerima</td>
                <td style="border:none">{{ $divisipenerima }}</td>
            </tr>
            <tr>
                <td style="border: none;"></td>
                <td style="border: none;"></td>
                <td style="font-weight: bold; border:none">Tanggal Terbit</td>
                <td style="border:none">{{ $ppk->created_at->format('d/m/Y') }}</td>
            </tr>
        </table>

        <p><strong>1. Jelaskan ketidaksesuaian yang terjadi atau peningkatan yang akan dibuat</strong></p>
        <p>Jenis</p>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border:none">( {{ str_contains($jenisketidaksesuaian, 'SISTEM') ? 'Y' : ' ' }} ) SISTEM</td>
                <td style="border:none">( {{ str_contains($jenisketidaksesuaian, 'PROSES') ? 'Y' : ' ' }} ) PROSES</td>
                <td style="border:none">( {{ str_contains($jenisketidaksesuaian, 'PRODUK') ? 'Y' : ' ' }} ) PRODUK</td>
                <td style="border:none">( {{ str_contains($jenisketidaksesuaian, 'AUDIT') ? 'Y' : ' ' }} ) AUDIT</td>
            </tr>
        </table>
        <p>{{ $judul }}</p>
        <p><strong>Evidence:</strong></p>
        <ul>
            @if (!empty($evidence))
                @foreach ($evidence as $file)
                    <img src="{{ asset('storage/' . $file) }}" alt="Evidence" style="max-width: 200px; max-height: 200px;">
                @endforeach
            @else
                Tidak ada evidence
            @endif
        </ul>
        <div class="signature" style="margin-top: 20px;">
            <div style="display: flex; flex-direction: column; align-items: flex-end; text-align: right; margin-right: 20px;">
                <p style="margin: 0;">Tanda Tangan: <img src="{{ $signature }}" width="100"></p>
                <p style="margin: 0;">Inisiator/Auditor: <strong>{{ $ppk->pembuatUser->nama_user }}</strong></p>
            </div>
            <br>
            <div style="display: flex; flex-direction: column; align-items: flex-end; text-align: right; margin-right: 20px;">
                <p style="margin: 0;">Tanda Tangan: <img src="{{ $signaturepenerima }}" width="100"></p>
                <p style="margin: 0;">Proses Owner/Auditee: <strong>{{ $ppk->penerimaUser->nama_user }}</strong></p>
            </div>
        </div>
        <br>
        <hr style="height: 3px; background-color: #000; border: none;">
        <p><strong>2. Identifikasi, evaluasi & pastikan akar penyebab masalah/Root Cause *:</strong></p>
        <p>{{ $identifikasi ?? 'Tidak ada data identifikasi' }}</p>
        <br>
        <p>* Gunakan metode 5WHYS untuk menentukan Root Cause; Fish Bone; Diagram alir; Penilaian situasi;</p>
        <p>Kendali proses dan peningkatan.</p>

    </div>
    <p>PT. TATA METAL LESTARI</p>
    <br>
    <br>
    <div style="page-break-before: always; border: 1px solid #000; padding: 10px; height: 100%; box-sizing: border-box;">
        <div style="border: 1px solid #000; padding: 10px; display: flex; align-items: center;">
            <img src="{{ asset('admin/img/TML3LOGO.png') }}" alt="Profile" class="rounded-circle"
                 style="width: 100px; height: 100px; border: 2px solid #fff; transition: transform 0.3s; margin-right: 10px;">
            <strong style="font-size: 20px; text-align: center; flex-grow: 1; margin-left: -100px;">PROSES PENINGKATAN KINERJA</strong>
        </div>
        <p colspan="2" style="text-align: right;" text-align: left; border:none">PPK NO. {{ $nomor_surat }}</p>
        <p><strong>3. Usulan tindakan: Jelaskan apa, siapa dan kapan akan dilaksanakan dan siapa yang akan melakukan tindakan Penanggulangan/Pencegahan tersebut dan kapan akan diselesaikan.</strong></p>

        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; text-align: center;">
            <tr>
                <!-- Sel kiri dengan rowspan 3 -->
                <td rowspan="1" style="border: 1px solid #000; padding: 10px; vertical-align: middle;">
                </td>
                <!-- Sel tengah di baris pertama -->
                <td colspan="4" style="border: 1px solid #000; padding: 10px; text-align: center;">
                    <strong>Tindakan</strong>
                </td>
                <!-- Sel kanan dengan rowspan 3 -->
                <td rowspan="1" style="border: 1px solid #000; padding: 10px; vertical-align: middle;">
                    <strong>Target Tgl</strong>
                </td>
                <td rowspan="1" style="border: 1px solid #000; padding: 10px; vertical-align: middle;">
                    <strong>PIC</strong>
                </td>
            </tr>
            <tr>
                <!-- Sel kiri dengan rowspan 3 -->
                <td rowspan="1" style="border: 1px solid #000; padding: 10px; vertical-align: middle;">
                    <strong>Penanggulangan</strong>
                </td>
                <!-- Sel tengah di baris pertama -->
                <td colspan="4" style="border: 1px solid #000; padding: 10px;">
                    {{ $penanggulangan ?? 'Tidak ada data penanggulangan' }}
                </td>
                <!-- Sel kanan dengan rowspan 3 -->
                <td rowspan="1" style="border: 1px solid #000; padding: 10px; vertical-align: middle;">
                        {{ $tgl_penanggulangan ? \Carbon\Carbon::parse($tgl_penanggulangan)->format('d M Y') : 'Tidak ada Tanggal Penanggulangan' }}
                </td>
                <td>
                    @if (!empty($pic1))
                        @php
                            $pic1Array = explode(',', $pic1); // Pisahkan berdasarkan koma
                        @endphp

                        @foreach ($pic1Array as $index => $pic)
                            <p>{{ $index + 1 }}. {{ trim($pic) }}</p> <!-- Menampilkan nomor urut dan nama PIC -->
                        @endforeach
                    @else
                        <!-- Jika pic1 kosong, gunakan pic1_other -->
                        @if (!empty($pic1_other))
                            <p>{{ $pic1_other }}</p> <!-- Menampilkan pic1_other -->
                        @else
                            <p>-</p> <!-- Jika pic1 dan pic1_other kosong -->
                        @endif
                    @endif
                </td>



            </tr>
            <tr>
                <!-- Sel kiri dengan rowspan 3 -->
                <td rowspan="1" style="border: 1px solid #3f3d3d; padding: 10px; vertical-align: middle;">
                    <strong>Pencegahan</strong>
                </td>
                <!-- Sel tengah di baris pertama -->
                <td colspan="4" style="border: 1px solid #000; padding: 10px;">
                    {{ $pencegahan ?? 'Tidak ada data pencegahan' }}
                </td>
                <!-- Sel kanan dengan rowspan 3 -->
                <td rowspan="1" style="border: 1px solid #000; padding: 10px; vertical-align: middle;">
                        {{ $tgl_pencegahan ? \Carbon\Carbon::parse($tgl_pencegahan)->format('d M Y') : 'Tidak ada Tanggal Pencegahan' }}
                </td>
                <td>
                    @if (!empty($pic2))
                        @php
                            $pic2Array = explode(',', $pic2); // Pisahkan berdasarkan koma
                        @endphp

                        @foreach ($pic2Array as $index => $pic)
                            <p>{{ $index + 1 }}. {{ trim($pic) }}</p> <!-- Menampilkan nomor urut dan nama PIC -->
                        @endforeach
                    @else
                        <!-- Jika pic1 kosong, gunakan pic1_other -->
                        @if (!empty($pic2_other))
                            <p>{{ $pic2_other }}</p> <!-- Menampilkan pic1_other -->
                        @else
                            <p>-</p> <!-- Jika pic1 dan pic1_other kosong -->
                        @endif
                    @endif
                </td>

            </tr>
        </table>
        <p>* Bila tidak cukup, dapat  menggunakan lampiran sesuai dengan format diatas </p>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="margin: 0;">Tanggal: {{ $created_at->format('d/m/Y') }}</p>
            <div style="text-align: center;">
                <p style="margin: 0;">Tanda Tangan:</p>
                <img src="{{ $signaturepenerima }}" alt="" width="100">
                <p><strong>{{ $ppk->penerimaUser->nama_user }}</strong></p>
            </div>
        </div>

        <hr style="height: 3px; background-color: #000; border: none;">

        <p><strong>4. Verifikasi Tindakan , sesuai kolom"Target Tanggal" </strong></p>

        @if(!empty($verifikasi) && !empty($verifikasi_img))
            <p><strong>Catatan Verifikasi:</strong></p>
            <p>{{ $verifikasi }}</p>
                @foreach(json_decode($verifikasi_img) as $file)
                    <img src="{{ asset('storage/' . $file) }}" alt="Verifikasi" style="max-width: 200px; max-height: 200px; margin-top: 10px;">
                @endforeach
                @elseif(!empty($verifikasi))
                    <p><strong>Catatan Verifikasi:</strong></p>
                    <p>{{ $verifikasi }}</p>
                @elseif(!empty($verifikasi_img))
                    <p><strong>Gambar Verifikasi:</strong></p>
                    @foreach(json_decode($verifikasi_img) as $file)
                        <img src="{{ asset('storage/' . $file) }}" alt="Verifikasi" style="max-width: 200px; max-height: 200px; margin-top: 10px;">
                    @endforeach
                @else
            <p>Catatan: Tidak ada Catatan atau Gambar Verifikasi.</p>
        @endif

        <div style="display: flex; flex-direction: column; align-items: flex-end;">
            <!-- Teks Tanggal Verifikasi -->
            <p style="text-align: right;">
                Tanggal Verifikasi: {{ $created_at_ppkketiga ? \Carbon\Carbon::parse($created_at_ppkketiga)->format('d M Y') : 'Tidak ada Tanggal' }}
            </p>
            <!-- Teks Auditor dan Signature -->
            <p style="margin-top: 20px; text-align: right; display: flex; align-items: center;">
                Auditor,
                <img src="{{ $signaturepenerima }}" alt="" width="100" style="margin-left: 10px;">
            </p>
        </div>

        <!-- Nama Pembuat di bawah Auditor -->
        <p style="text-align: right; margin-right: 20px;">
            <strong>{{$ppk->pembuatUser->nama_user}}.</strong>
        </p>

        <p>Efektifitas Tindakan Penanggulangan/Pencegahan dapat diverifkasi 1(satu) bulan dari Tanggal Verifikasi oleh Auditor </p>

        <hr style="height: 3px; background-color: #000; border: none;">
        <p><strong>5.
            <span style="display: block;">a) Tinjauan Status penyelesaian Tindakan Penanggulangan</span>
            <span style="display: block;">b) Tinjauan efektivitas atas hasil Tindakan Pencegahan</span>
        </strong></p>
        <p>Catatan: {{$tinjauan ?? 'Tidak ada Catatan'}}</p>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <!-- TRUE Section -->
            <div style="display: grid; grid-template-columns: 30% 1fr; gap: 10px;">
                <div style="text-align: center;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; text-align: center;">
                        <tr>
                            <td style="border: 1px solid #000; padding: 10px; vertical-align: middle;
                                background-color: {{ $status == 'TRUE' ? 'green' : 'transparent' }};
                                color: {{ $status == 'TRUE' ? '#fff' : '#000' }};">
                                TRUE
                            </td>
                        </tr>
                    </table>
                </div>
                <p style="margin: 0; padding-top: 20px;">
                    Efektif, dalam 1 bulan masalah yang sama tidak muncul lagi & tindakan penanggulangan sudah selesai
                </p>

                <div style="text-align: center;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; text-align: center;">
                        <tr>
                            <td style="border: 1px solid #000; padding: 10px; vertical-align: middle;
                                background-color: {{ $status == 'FALSE' ? 'red' : 'transparent' }};
                                color: {{ $status == 'FALSE' ? '#fff' : '#000' }};">
                                FALSE
                            </td>
                        </tr>
                    </table>
                </div>
                <p style="margin: 0; padding-top: 20px;">
                    Tidak efektif, dilanjutkan dengan PPK No. {{$newppk ?? '...'}}
                </p>
            </div>
        </div>

        <hr style="height: 3px; background-color: #000; border: none;">
        <p><strong>6. Close Out (apabila efektif)</strong></p>
        <div class="signature" style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-right: 20px;">
                <!-- Tanda Tangan di tengah dan sedikit ke kanan -->
                <div style="flex-grow: 1; text-align: center; margin-left: 250px;">
                    <p style="margin: 0;">Tanda Tangan: <img src="{{ $signature }}" alt="Signature" width="100"></p>
                    <p style="margin: 0;">Inisiator/Auditor: <strong>{{ $ppk->pembuatUser->nama_user }}</strong></p>
                </div>
                <!-- Inisiator/Auditor dan Date di kanan -->
                <div style="display: flex; flex-direction: column; align-items: flex-end;">
                    <p style="margin: 0;">
                        Date: {{ $created_at_ppkketiga ? \Carbon\Carbon::parse($created_at_ppkketiga)->format('d M Y') : 'Tidak ada Tanggal' }}
                    </p>
                </div>
            </div>
        </div>

    </div>
    <p>PT. TATA METAL LESTARI</p>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

