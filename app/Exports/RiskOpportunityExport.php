<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;

class RiskOpportunityExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $formattedData;
    protected $counter = 1; // Variable for numbering

    public function __construct($formattedData)
    {
        $this->formattedData = $formattedData;
    }

    public function collection()
    {
        return collect($this->formattedData);
    }

    public function headings(): array
    {
        return [
            ['RISK & REGISTER OPPORTUNITY'],
            [],
            [
                'No',
                'Issue',
                'Pihak Berkepentingan',
                'Risiko',
                'Peluang',
                'Tingkatan',
                'Tindakan Lanjut',
                'Target PIC',
                'Tanggal Penyelesaian',
                'Status',
                'Actual Risk',
                'Before',
                'After',
            ],
        ];
    }

    public function map($row): array
    {
        // Ensure pihak is always an array
        $pihak = is_array($row['pihak']) ? array_unique($row['pihak']) : [$row['pihak']];
        $tindak_lanjut = is_array($row['tindak_lanjut']) ? $row['tindak_lanjut'] : [$row['tindak_lanjut']];
        $targetpic = is_array($row['targetpic']) ? $row['targetpic'] : [$row['targetpic']];
        $tgl_realisasi = is_array($row['tgl_realisasi']) ? $row['tgl_realisasi'] : [$row['tgl_realisasi']];

        // Prepare the mapped rows
        $mappedRows = [];

        // Calculate the max number of rows for the details
        $maxRows = max(count($pihak), count($tindak_lanjut), count($targetpic), count($tgl_realisasi));

        for ($i = 0; $i < $maxRows; $i++) {
            $mappedRows[] = [
                $i === 0 ? $this->counter++ : '',
                $i === 0 ? $row['issue'] : '',
                $i === 0 ? implode(', ', $pihak) : '', // Join unique 'pihak'
                $i === 0 ? $row['risiko'] : '',
                $i === 0 ? $row['peluang'] : '',
                $i === 0 ? $row['tingkatan'] : '',
                $tindak_lanjut[$i] ?? '',
                $targetpic[$i] ?? '',
                $tgl_realisasi[$i] ?? '',
                $i === 0 ? $row['status'] : '',
                $i === 0 ? $row['scores'] : '',
                $i === 0 ? $row['before'] : '',
                $i === 0 ? $row['after'] : '',
            ];
        }

        $mappedRows[] = ['', '', '', '', '', '', '', '', '', '', '', ''];

        return $mappedRows;
    }


    public function styles(Worksheet $sheet)
    {
        // Bold untuk heading
        $sheet->getStyle('A3:M3')->getFont()->setBold(true);

        // Merge cells untuk title
        $sheet->mergeCells('A1:C1');

        // Set font size dan alignment untuk title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Buat heading menjadi bold
        $sheet->getStyle('A3:M3')->getFont()->setBold(true);

        // Set alignment untuk heading agar lebih rapi
        $sheet->getStyle('A3:M3')->getAlignment()->setHorizontal('center');

        // Format as table (auto filter and borders)
        $sheet->setAutoFilter('A3:M3');
        $sheet->getStyle('A3:M100')->getBorders()->getAllBorders()->setBorderStyle('thin');

        // Set lebar kolom agar sesuai
        $sheet->getColumnDimension('A')->setWidth(5);  // Nomor
        $sheet->getColumnDimension('B')->setWidth(30); // Issue
        $sheet->getColumnDimension('C')->setWidth(25); // Pihak Berkepentingan
        $sheet->getColumnDimension('D')->setWidth(15); // Risiko
        $sheet->getColumnDimension('E')->setWidth(15); // Peluang
        $sheet->getColumnDimension('F')->setWidth(15); // Tingkatan
        $sheet->getColumnDimension('G')->setWidth(20); // Tindakan Lanjut
        $sheet->getColumnDimension('H')->setWidth(15); // Target PIC
        $sheet->getColumnDimension('I')->setWidth(15); // Tanggal Penyelesaian
        $sheet->getColumnDimension('J')->setWidth(15); // Status
        $sheet->getColumnDimension('K')->setWidth(30); // Actual Risk
        $sheet->getColumnDimension('L')->setWidth(15); // Before
        $sheet->getColumnDimension('M')->setWidth(15); // After
    }
}
