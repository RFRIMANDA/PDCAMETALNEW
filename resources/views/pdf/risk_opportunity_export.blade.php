<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Opportunity & Register Report</title>
    <style>
        body {
            font-size: 12px; /* Menyesuaikan ukuran font keseluruhan */
        }
        h2 {
            text-align: center; /* Header h2 di tengah */
            background-color: #d9f99d; /* Warna latar belakang hijau muda */
            padding: 10px; /* Menambahkan padding untuk tampilan yang lebih baik */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px; /* Ukuran font khusus untuk tabel */
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 4px; /* Mengurangi padding untuk membuat tabel lebih ringkas */
            text-align: left;
        }
        th {
            background-color: #d9f99d; /* Menambahkan warna latar belakang hijau muda */
            text-align: center; /* Header di tengah */
        }
        .separator {
            border: none; /* Menghilangkan border di bawah */
            border-top: 1px solid black; /* Garis atas untuk pemisah */
            margin: 0; /* Mengatur margin untuk pemisah */
            padding: 0; /* Mengatur padding untuk pemisah */
        }
    </style>
</head>
<body>
    <h2>Report Risk & Opportunity Register</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Issue</th>
                <th>Pihak Yang Berkepentingan</th>
                <th>Risiko</th>
                <th>Peluang</th>
                <th>Tingkatan</th>
                <th>Tindakan Lanjut</th>
                <th>Target PIC</th>
                <th>Tanggal Penyelesaian</th>
                <th>Status</th>
                <th>Before</th>
                <th>After</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($formattedData as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td> <!-- Menambahkan nomor urut -->
                <td>{{ $data['issue'] }}</td>
                <td>
                    @foreach ($data['pihak'] as $pihak)
                        {{ $pihak }}<br>
                        @if (!$loop->last) <!-- Menambahkan pemisah hanya jika ini bukan elemen terakhir -->
                            <hr class="separator">
                        @endif
                    @endforeach
                </td>
                <td>{{ $data['risiko'] }}</td>
                <td>{{ $data['peluang'] }}</td>
                <td>{{ $data['tingkatan'] }}</td>
                <td>
                    @foreach ($data['tindak_lanjut'] as $tindak_lanjut)
                        {{ $tindak_lanjut }}<br>
                        @if (!$loop->last) <!-- Menambahkan pemisah hanya jika ini bukan elemen terakhir -->
                            <hr class="separator">
                        @endif
                    @endforeach
                </td>
                <td>
                    @foreach ($data['targetpic'] as $targetpic)
                        {{ $targetpic }}<br>
                        @if (!$loop->last) <!-- Menambahkan pemisah hanya jika ini bukan elemen terakhir -->
                            <hr class="separator">
                        @endif
                    @endforeach
                </td>
                <td>
                    @foreach ($data['tgl_realisasi'] as $tgl_realisasi)
                        {{ $tgl_realisasi }}<br>
                        @if (!$loop->last) <!-- Menambahkan pemisah hanya jika ini bukan elemen terakhir -->
                            <hr class="separator">
                        @endif
                    @endforeach
                </td>
                <td>{{ $data['status'] }}</td>
                <td>{{ $data['before'] }}</td>
                <td>{{ $data['after'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
