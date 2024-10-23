<?php

namespace App\Exports;

use App\Models\Riskregister;
use App\Models\Tindakan;
use App\Models\Realisasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiskRegisterExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $divisiId;
    protected $counter = 1; // Variable untuk nomor urut

    public function __construct($divisiId)
    {
        $this->divisiId = $divisiId;
    }

    public function collection()
    {
        // Ambil semua data riskregister untuk divisi tertentu
        return Riskregister::with('resikos')->where('id_divisi', $this->divisiId)->get()->map(function ($riskregister, $index) {
            // Menyimpan index + 1 sebagai nomor berurutan
            $riskregister->No = $index + 1;
            return $riskregister;
        });
    }

    public function headings(): array
    {
        return [
            ['RISK & REGISTER OPPORTUNITY'], // Judul di atas heading
            [], // Baris kosong untuk pemisah
            [
                'No',
                'Issue',
                'Pihak Berkepentingan',
                'Risiko',
                'Peluang',
                'Tingkatan',
                'Target PIC',
                'Status',
                'Actual Risk',
                'Tindakan',
                'Before',  // Tambahkan heading Before
                'After'    // Tambahkan heading After
            ],
        ];
    }

    public function map($riskregister): array
    {
        $tindakanList = Tindakan::where('id_riskregister', $riskregister->id)->get();
        $realisasiList = Realisasi::whereIn('id_tindakan', $tindakanList->pluck('id'))->get();

        $tindakanData = [];
        foreach ($tindakanList as $tindakan) {
            $tindakanData[] = [
                'nama_tindakan' => $tindakan->nama_tindakan,
                'targetpic' => $tindakan->targetpic
            ];
        }

        return [
            $riskregister->No, // Gunakan nomor berurutan
            $riskregister->issue,
            $tindakanList->pluck('divisi.nama_divisi')->implode(', '), // Pihak Berkepentingan
            $riskregister->resikos->pluck('nama_resiko')->implode(', '), // Risiko
            $riskregister->peluang,
            $riskregister->resikos->isNotEmpty() ? $riskregister->resikos->first()->tingkatan : 'N/A', // Tingkatan
            implode(', ', array_column($tindakanData, 'targetpic')), // Target PIC
            $riskregister->resikos->isNotEmpty() ? $riskregister->resikos->first()->status : 'N/A', // Status
            $riskregister->resikos->isNotEmpty() ? $riskregister->resikos->first()->risk : 'N/A', // Actual Risk
            implode(', ', array_column($tindakanData, 'nama_tindakan')), // Daftar Tindakan
            $riskregister->resikos->isNotEmpty() ? $riskregister->resikos->first()->before : 'N/A', // Before
            $riskregister->resikos->isNotEmpty() ? $riskregister->resikos->first()->after : 'N/A', // After
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Bold untuk header
        $sheet->getStyle('A3:L3')->getFont()->setBold(true);

        // Merge cells untuk judul
        $sheet->mergeCells('A1:L1');

        // Set font size dan alignment untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Buat heading menjadi bold
        $sheet->getStyle('A3:L3')->getFont()->setBold(true);

        // Set alignment untuk heading agar lebih rapi
        $sheet->getStyle('A3:L3')->getAlignment()->setHorizontal('center');

        // Format as table (auto filter and borders)
        $sheet->setAutoFilter('A3:L3');
        $sheet->getStyle('A3:L100')->getBorders()->getAllBorders()->setBorderStyle('thin');

        // Set lebar kolom agar sesuai
        $sheet->getColumnDimension('A')->setWidth(5);  // Nomor
        $sheet->getColumnDimension('B')->setWidth(30); // Issue
        $sheet->getColumnDimension('C')->setWidth(25); // Pihak Berkepentingan
        $sheet->getColumnDimension('D')->setWidth(15); // Risiko
        $sheet->getColumnDimension('E')->setWidth(15); // Peluang
        $sheet->getColumnDimension('F')->setWidth(15); // Tingkatan
        $sheet->getColumnDimension('G')->setWidth(25); // Daftar Tindakan
        $sheet->getColumnDimension('H')->setWidth(20); // Target PIC
        $sheet->getColumnDimension('I')->setWidth(15); // Status
        $sheet->getColumnDimension('J')->setWidth(15); // Actual Risk
        $sheet->getColumnDimension('K')->setWidth(15); // Before
        $sheet->getColumnDimension('L')->setWidth(15); // After
    }
}
