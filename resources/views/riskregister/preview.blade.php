<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk & Register Opportunity</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background-color: #89cc1c;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        h1, h2, h3 {
            text-align: center;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e1e1e1;
        }

        button {
            margin: 20px auto;
            display: block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }
        .custom-background {
            background-color: #9fa583; /* Warna latar belakang */
            color: white; /* Warna teks, sesuaikan jika diperlukan */
            padding: 10px; /* Opsional: Memberikan sedikit padding agar lebih rapi */
        }


        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Report Risk & Register Opportunity</h1>
        <h2>Divisi {{ $divisi->nama_divisi }}</h2>
        <h3>PT. Tata Metal Lestari</h3>
    </div>

    <h2 class="custom-background">DETAIL ISSUE</h2>
    <table>
        <thead>
            <tr>
                <th>Issue</th>
                <th>Pihak Berkepentingan</th>
                <th>Risiko</th>
                <th>Peluang</th>
                <th>Tingkatan</th>
                <th>Target PIC</th>
                <th>Status</th>
                <th>Actual Risk</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $riskregister->issue }}</td>
                <td>
                    @foreach ($tindakanList as $tindakan)
                        {{ optional($tindakan->divisi)->nama_divisi }}<br>
                    @endforeach
                </td>

                <td>
                    @foreach ($resikos as $resiko)
                        {{ $resiko->nama_resiko }}@if(!$loop->last), @endif
                    @endforeach
                </td>

                @if($riskregister->resikos->isNotEmpty())
                    <td>{{ $riskregister->peluang }}</td>
                    <td>{{ $riskregister->resikos->first()->tingkatan }}</td>
                    <td>
                        @foreach ($tindakanList as $index => $tindakan)
                            {{ $tindakan->targetpic }}<br>
                        @endforeach
                    </td>

                    <td>{{ $riskregister->resikos->first()->status }}</td>
                    <td>{{ $riskregister->resikos->first()->risk }}</td>
                @else
                    <td colspan="4">No data available</td>
                @endif
            </tr>
        </tbody>
    </table>

    <h2 class="">Daftar Tindakan</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tindakanList as $index => $tindakan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tindakan->nama_tindakan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Detail Before After</h2>
    <table>
        <thead>
            <tr>
                <th>Before</th>
                <th>After</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resikos as $resiko)
                <tr>
                    <td>{{ $resiko->before }}</td>
                    <td>{{ $resiko->after }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tombol Cetak -->
    <button onclick="printReport()">Export to PDF</button>

    <script type="text/javascript">
        function printReport() {
            window.print();
        }
    </script>

</body>
</html>
