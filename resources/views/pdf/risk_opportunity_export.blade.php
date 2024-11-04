<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Opportunity & Register Report</title>
    <style>
        body {
            font-size: 10px; /* Reduced font size */
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            background-color: #56dbc5;
            padding: 10px;
            page-break-after: avoid; /* Prevent page break after the title */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px; /* Ensure table font size is consistent */
            page-break-inside: auto; /* Allow page break within the table */
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #a6f119;
            text-align: center;
            position: sticky; /* Make header sticky */
            top: 0; /* Stick to the top of the viewport */
            z-index: 1; /* Keep the header above other content */
        }
        /* Align specific columns to the top */
        .align-top {
            vertical-align: top;
        }
        .align-bottom {
            vertical-align: bottom; /* Align to the bottom */
        }
        .align-left {
            text-align: left; /* Align text to the left */
        }
        .align-center {
            text-align: center; /* Center alignment for Tindakan Lanjut */
        }
        .separator {
            border: none;
            border-top: 1px solid black;
            margin: 0;
            padding: 0;
        }
        /* Adjust column widths */
        .col-int-ext {
            width: 30px; /* Width for Int/Ext column */
            text-align: center;
        }
        .col-tindakan {
            width: 150px; /* Width for Tindakan Lanjut column */
            text-align: left; /* Align text to the left */
        }
        .col-risiko {
            width: 80px; /* Width for Risiko column */
        }
        .col-target-pic {
            width: 100px; /* Width for Target PIC column */
        }
    </style>
</head>
<body>
    <h2>Report Risk & Opportunity Register<br>PT. Tata Metal Lestari</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Issue</th>
                <th class="col-int-ext">Int/Ext</th>
                <th>Pihak Yang Berkepentingan</th>
                <th class="col-risiko">Risiko</th>
                <th class="align-bottom">Peluang</th> <!-- Align Peluang to bottom -->
                <th>Tingkatan</th>
                <th class="col-tindakan">Tindakan Lanjut</th>
                <th class="col-target-pic">Target<br>PIC</th>
                <th>Tanggal Penyelesaian</th>
                <th>Status</th>
                <th>Actual Risk</th>
                <th>Before</th>
                <th>After</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($formattedData as $index => $data)
            <tr>
                <td class="align-top">{{ $index + 1 }}</td>
                <td class="align-top">{{ $data['issue'] }}</td>
                <td class="col-int-ext align-top">{{ $data['inex'] }}</td>
                <td class="align-top">{{ $data['pihak'] }}</td>
                <td class="col-risiko align-top">{{ $data['risiko'] }}</td>
                <td class="align-bottom">{{ $data['peluang'] }}</td> <!-- Align Peluang to bottom -->
                <td class="align-top">{{ $data['tingkatan'] }}</td>
                <td class="col-tindakan align-top">
                    @foreach ($data['tindak_lanjut'] as $index => $tindak_lanjut)
                        {{ $index + 1 }}. {{ $tindak_lanjut }}<br>
                        @if (!$loop->last)
                            <hr class="separator">
                        @endif
                    @endforeach
                </td>
                <td class="col-target-pic align-top">
                    @foreach ($data['targetpic'] as $index => $targetpic)
                        {{ $index + 1 }}. {{ $targetpic }}<br>
                        @if (!$loop->last)
                            <hr class="separator">
                        @endif
                    @endforeach
                </td>
                <td class="align-top">
                    @foreach ($data['tgl_realisasi'] as $index => $tgl_realisasi)
                        {{ $index + 1 }}. {{ $tgl_realisasi }}<br>
                        @if (!$loop->last)
                            <hr class="separator">
                        @endif
                    @endforeach
                </td>
                <td class="align-top">{{ $data['status'] }}</td>
                <td class="align-top">{{ $data['risk'] }}</td>
                <td class="align-top">{{ $data['before'] }}</td>
                <td class="align-top">{{ $data['after'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
