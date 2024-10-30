<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PpkExport implements FromCollection, WithStyles
{
    protected $ppk;

    public function __construct($ppk)
    {
        $this->ppk = $ppk;
    }

    public function collection()
    {
        return collect([]); // Mengembalikan koleksi kosong untuk mencegah data default
    }
    
    public function styles(Worksheet $sheet)
    {
        // Pengaturan margin dan ukuran kertas
        $sheet->getPageMargins()->setTop(0.4);
        $sheet->getPageMargins()->setRight(0.4);
        $sheet->getPageMargins()->setLeft(0.4);
        $sheet->getPageMargins()->setBottom(1.3);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);
    
        // Menghilangkan header dan footer
        $sheet->getHeaderFooter()->setOddHeader('');
        $sheet->getHeaderFooter()->setOddFooter('');
    
        // Judul di B2
        $sheet->setCellValue('B2', 'PROSES PENINGKATAN KINERJA');
        $sheet->mergeCells('B2:L2');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        // Kotak bingkai di sekitar B2:L30
        $sheet->getStyle('B2:L30')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B2:L30')->getBorders()->getOutline()->getColor()->setARGB('000000');
        $sheet->getStyle('B2:L2')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B2:L2')->getBorders()->getOutline()->getColor()->setARGB('000000');
    
        // Menyembunyikan gridlines
        $sheet->setShowGridlines(false);
    
        // Informasi detail
        $sheet->setCellValue('C3', 'KEPADA')->getStyle('C3')->getFont()->setBold(true);
        $sheet->setCellValue('E3', $this->ppk->penerimaUser->nama_user);
        $sheet->setCellValue('H3', 'PPK NO.');
        $sheet->setCellValue('I3', $this->ppk->nomor_surat);
    
        $sheet->setCellValue('C5', 'Departemen:')->getStyle('C5')->getFont()->setBold(true);
        $sheet->setCellValue('E5', $this->ppk->divisipenerima);
        $sheet->setCellValue('H5', 'Pembuat / Inisiator:')->getStyle('H5')->getFont()->setBold(true);
        $sheet->setCellValue('J5', $this->ppk->pembuatUser->nama_user);
    
        $sheet->setCellValue('H7', 'Tanggal Terbit:')->getStyle('H7')->getFont()->setBold(true);
        $sheet->setCellValue('J7', $this->ppk->created_at->format('d/m/Y'));
    
        // Jenis Ketidaksesuaian
        $sheet->setCellValue('C9', '1. Jelaskan ketidaksesuaian yang terjadi atau peningkatan yang akan dibuat');
        
        // Menandai jenis ketidaksesuaian yang dipilih dengan 'Y'
        $sheet->setCellValue('D11', $this->ppk->jenisketidaksesuaian === 'SISTEM' ? '( Y ) SISTEM' : '(   ) SISTEM');
        $sheet->setCellValue('F11', $this->ppk->jenisketidaksesuaian === 'PROSES' ? '( Y ) PROSES' : '(   ) PROSES');
        $sheet->setCellValue('H11', $this->ppk->jenisketidaksesuaian === 'PRODUK' ? '( Y ) PRODUK' : '(   ) PRODUK');
        $sheet->setCellValue('J11', $this->ppk->jenisketidaksesuaian === 'AUDIT' ? '( Y ) AUDIT' : '(   ) AUDIT');
    
        // Deskripsi Masalah
        $sheet->setCellValue('C12', $this->ppk->judul);
    
        // Evidence Section
        $sheet->setCellValue('C25', 'Evidence:');
        if ($this->ppk->signature && file_exists(public_path('admin/img/' . $this->ppk->signature))) {
            $drawing = new Drawing();
            $drawing->setName('Tanda Tangan Inisiator');
            $drawing->setDescription('Tanda Tangan Inisiator');
            $drawing->setPath(public_path('admin/img/' . $this->ppk->signature));
            $drawing->setHeight(50);
            $drawing->setCoordinates('J25');
            $drawing->setWorksheet($sheet);
        } else {
            $sheet->setCellValue('J25', 'No Available');
        }
    
        // Tanda Tangan Section
        $sheet->setCellValue('I25', 'Tanda Tangan:');
        $sheet->setCellValue('I26', 'Inisiator/Auditor:');
        $sheet->setCellValue('I29', 'Tanda Tangan: __________');
        $sheet->setCellValue('I30', 'Proses Owner/Auditee:');
        $sheet->setCellValue('J29', $this->ppk->penerimaUser->nama_user);

        if ($this->ppk->signaturepenerima && file_exists(public_path('admin/img/' . $this->ppk->signaturepenerima))) {
            $drawing = new Drawing();
            $drawing->setName('Tanda Tangan Penerima');
            $drawing->setDescription('Tanda Tangan Penerima');
            $drawing->setPath(public_path('admin/img/' . $this->ppk->signaturepenerima));
            $drawing->setHeight(50);
            $drawing->setCoordinates('J29');
            $drawing->setWorksheet($sheet);
        }

        // Menyesuaikan lebar kolom agar sesuai
        foreach (range('B', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(20);
        }
    }    
}
