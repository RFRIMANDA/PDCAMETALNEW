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
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid black;
        }

        h1, h3 , h2 {
            text-align: center;
        }

        .header {
            margin-bottom: 20px;
        }

        button {
            margin: 20px auto;
            display: block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Report Risk & Register Opportunity</h1>
        <h2>Divisi {{ $divisi->nama_divisi }}</h2>
        <h3>PT. Tata Metal Lestari</h3>
    </div>

    <table>
        <tr>
            <th>Issue</th>
            <td>{{ $form->issue }}</td>
        </tr>
        <tr>
            <th>Peluang</th>
            <td>{{ $form->peluang }}</td>
        </tr>
        <tr>
            <th>Tingkatan</th>
            <td>{{ $form->tingkatan }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $form->status }}</td>
        </tr>
        <tr>
            <th>Risk</th>
            <td>{{ $form->risk }}</td>
        </tr>
        <tr>
            <th>Before</th>
            <td>{{ $form->before }}</td>
        </tr>
        <tr>
            <th>After</th>
            <td>{{ $form->after }}</td>
        </tr>
    </table>

    <h3>Daftar Tindakan</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Tindakan</th>
                <th>Pihak Berkepentingan</th>
                <th>PIC</th>
                <th>Resiko</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tindakanList as $index => $tindakan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tindakan->nama_tindakan }}</td>
                    <td>{{ optional($tindakan->divisi)->nama_divisi }}</td> <!-- Mengambil nama_divisi -->
                    <td>{{ $tindakan->pic }}</td>
                    <td>{{ $tindakan->resiko }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tombol Cetak -->
    
    <script type="text/javascript">
        function printReport() {
            window.print();  // Fungsi untuk mencetak halaman saat tombol diklik
        }
    </script>

</body>
</html>
