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
    protected $ppkdua;
    protected $ppktiga;

    public function __construct($ppk, $ppkdua,$ppktiga)
    {
        $this->ppk = $ppk;
        $this->ppkdua = $ppkdua;
        $this->ppktiga = $ppktiga;
    }

    public function collection()
    {
        return collect([]); // Mengembalikan koleksi kosong untuk mencegah data default
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(1.86); // Mengatur lebar kolom A menjadi 15
        $sheet->getColumnDimension('B')->setWidth(0.80); // Mengatur lebar kolom B menjadi 25
        $sheet->getColumnDimension('C')->setWidth(4.00); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('D')->setWidth(12.43); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('E')->setWidth(14.43); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('F')->setWidth(9.00); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('G')->setWidth(9.00); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('H')->setWidth(9.00); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('I')->setWidth(8.57); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('J')->setWidth(13.57); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('K')->setWidth(16.14); // Mengatur lebar kolom C menjadi 30
        $sheet->getColumnDimension('L')->setWidth(5.14); // Mengatur lebar kolom C menjadi 30

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

    // Mengatur font untuk seluruh worksheet
    $sheet->getStyle('B2:L60')->getFont()->setName('Arial'); // Set font untuk rentang sel tertentu

    // Menambahkan gambar
$drawinglogo = new Drawing();
$drawinglogo->setName('Logo');
$drawinglogo->setDescription('Logo Perusahaan');
$drawinglogo->setPath('admin/img/TMLPNG.png'); // Ganti dengan path ke gambar kamu
$drawinglogo->setCoordinates('B2'); // Koordinat sel tempat gambar akan ditambahkan
$drawinglogo->setHeight(100); // Atur tinggi gambar (dalam unit Excel)

// Mengatur offset untuk memindahkan gambar sedikit ke kanan dan ke bawah
$drawinglogo->setOffsetX(10); // Geser gambar ke kanan (dalam piksel)
$drawinglogo->setOffsetY(5); // Geser gambar ke bawah (dalam piksel)

$drawinglogo->setWorksheet($sheet); // Menambahkan gambar ke worksheet

// Judul di B2
$sheet->setCellValue('B2', 'PROSES PENINGKATAN KINERJA');
$sheet->mergeCells('B2:L2'); // Menggabungkan sel B2 sampai L2
$sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Mengatur perataan horizontal ke tengah
$sheet->getStyle('B2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER); // Mengatur perataan vertikal ke tengah
// Mengatur tinggi baris untuk baris 2
$sheet->getRowDimension(2)->setRowHeight(100); // Mengatur tinggi baris ke 30 unit


        // Kotak bingkai di sekitar B2:L30
        $sheet->getStyle('B2:L40')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B2:L40')->getBorders()->getOutline()->getColor()->setARGB('000000');
        $sheet->getStyle('B2:L60')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B2:L60')->getBorders()->getOutline()->getColor()->setARGB('000000');
        $sheet->getStyle('B2:L2')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B2:L2')->getBorders()->getOutline()->getColor()->setARGB('000000');

        // Menyembunyikan gridlines
        $sheet->setShowGridlines(false);

        // Informasi detail
        $sheet->setCellValue('C3', 'KEPADA')->getStyle('C3')->getFont()->setBold(true);
        $sheet->getRowDimension(3)->setRowHeight(26); // Mengatur tinggi baris ke 30 unit
        $sheet->setCellValue('E3', $this->ppk->penerimaUser->nama_user);
        $sheet->setCellValue('H3', 'PPK NO.')->getStyle('H3')->getFont()->setBold(true);
        $sheet->setCellValue('I3', $this->ppk->nomor_surat);

        $sheet->setCellValue('C5', 'Departemen:')->getStyle('C5')->getFont()->setBold(true);
        $sheet->setCellValue('E5', $this->ppk->divisipenerima);
        $sheet->setCellValue('H5', 'Pembuat / Inisiator:')->getStyle('H5')->getFont()->setBold(true);
        $sheet->setCellValue('J5', $this->ppk->pembuatUser->nama_user);
        $sheet->setCellValue('H7', 'Departemen:')->getStyle('H7')->getFont()->setBold(true);
        $sheet->setCellValue('J7', $this->ppk->divisipembuat);

        $sheet->setCellValue('H9', 'Tanggal Terbit:')->getStyle('H9')->getFont()->setBold(true);
        $sheet->setCellValue('J9', $this->ppk->created_at->format('d/m/Y'));

        // Jenis Ketidaksesuaian
        $sheet->setCellValue('C11', '1. Jelaskan ketidaksesuaian yang terjadi atau peningkatan yang akan dibuat')->getStyle('C11')->getFont()->setBold(true);
        $sheet->setCellValue('C12', 'Jenis');

        $jk = is_string($this->ppk->jenisketidaksesuaian)
        ? explode(',', $this->ppk->jenisketidaksesuaian)
        : (array) $this->ppk->jenisketidaksesuaian;


        // Tandai jenis ketidaksesuaian yang dipilih
        $sheet->setCellValue('D13', in_array('SISTEM', $jk) ? '( Y ) Sistem' : '(   ) Sistem');
        $sheet->setCellValue('F13', in_array('PROSES', $jk) ? '( Y ) Proses' : '(   ) Proses');
        $sheet->setCellValue('H13', in_array('PRODUK', $jk) ? '( Y ) Produk' : '(   ) Produk');
        $sheet->setCellValue('J13', in_array('AUDIT', $jk) ? '( Y ) Audit' : '(   ) Audit');

        // Deskripsi Masalah
        $sheet->setCellValue('C15', $this->ppk->judul);
        $sheet->mergeCells('C15:K15');
        $sheet->getStyle('C15:K15')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C15:K15')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(15)->setRowHeight(200);
        $sheet->getStyle('C15:K15')->getAlignment()->setWrapText(true);


       // Evidence Section
        $sheet->setCellValue('C27', 'Evidence:')->getStyle('C27')->getFont()->setBold(true);

        if ($this->ppk->evidence && file_exists(public_path('dokumen/' . $this->ppk->evidence))) {
            $drawingEvidence = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawingEvidence->setName('Evidence');
            $drawingEvidence->setDescription('Evidence');
            $drawingEvidence->setPath(public_path('dokumen/' . $this->ppk->evidence)); // Path gambar evidence yang benar
            $drawingEvidence->setHeight(100); // Atur tinggi gambar
            $drawingEvidence->setWidth(150); // Atur lebar gambar (opsional)
            $drawingEvidence->setCoordinates('D25'); // Posisi gambar di sheet
            $drawingEvidence->setWorksheet($sheet);
        } else {
            $sheet->setCellValue('D25', 'No Evidence Available'); // Pesan jika evidence tidak ditemukan
        }

        if ($this->ppk->signature && file_exists(public_path('admin/img/' . $this->ppk->signature))) {
            $drawing = new Drawing();
            $drawing->setName('Tanda Tangan Inisiator');
            $drawing->setDescription('Tanda Tangan Inisiator');
            $drawing->setPath(public_path('admin/img/' . $this->ppk->signature));
            $drawing->setHeight(50);
            $drawing->setCoordinates('J27');
            $drawing->setOffsetX(20); // Angka ini bisa disesuaikan, semakin besar semakin ke kanan
            $drawing->setWorksheet($sheet);
        } else {
            $sheet->setCellValue('J25', 'No Available');
        }

        // Tanda Tangan Section
        $sheet->setCellValue('I27', 'Tanda Tangan:');
        $sheet->setCellValue('I29', 'Inisiator/Auditor:');
        $sheet->setCellValue('I31', 'Tanda Tangan: __________');
        $sheet->setCellValue('I32', 'Proses Owner/Auditee:');
        $sheet->setCellValue('I29', $this->ppk->pembuatUser->nama_user)->getStyle('I27')->getFont()->setBold(true);

    // Signature Penerima dari Ppkkedua
    if ($this->ppkdua && $this->ppkdua->signaturepenerima && file_exists(public_path('admin/img/' . $this->ppkdua->signaturepenerima))) {
        $drawingPenerima = new Drawing();
        $drawingPenerima->setName('Tanda Tangan Penerima');
        $drawingPenerima->setDescription('Tanda Tangan Penerima');
        $drawingPenerima->setPath(public_path('admin/img/' . $this->ppkdua->signaturepenerima));
        $drawingPenerima->setHeight(50);
        $drawingPenerima->setCoordinates('J31'); // Posisi tanda tangan penerima
        $drawingPenerima->setOffsetX(20); // Angka ini bisa disesuaikan, semakin besar semakin ke kanan
        $drawingPenerima->setWorksheet($sheet);
    } else {
        $sheet->setCellValue('J31', 'No Available');
    }

        // Tanda Tangan Section
        $sheet->setCellValue('I27', 'Tanda Tangan:');
        $sheet->setCellValue('I28', 'Inisiator/Auditor:');
        $sheet->setCellValue('I31', 'Tanda Tangan:');
        $sheet->setCellValue('I32', 'Proses Owner/Auditee:');
        $sheet->setCellValue('I33', $this->ppk->penerimaUser->nama_user)->getStyle('I33')->getFont()->setBold(true);

        // Menyesuaikan lebar kolom agar sesuai
        foreach (range('B', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(20);
        }
        $sheet->setCellValue('C42', '2.  Identifikasi, evaluasi & pastikan akar penyebab masalah/Root Cause *:')->getStyle('C42')->getFont()->setBold(true);
       // Mengatur nilai untuk D44
       $sheet->setCellValue('D44', $this->ppkdua->identifikasi);
       $sheet->mergeCells('D44:J44');// Menggabungkan sel D44 sampai J44
       $sheet->getStyle('D44:J44')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);// Mengatur perataan ke kiri
       $sheet->getStyle('D44:J44')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);// Mengatur perataan ke tengah secara vertikal
       $sheet->getRowDimension(44)->setRowHeight(200);// Mengatur tinggi baris untuk baris 44
       $sheet->getStyle('D44:J44')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('C57', '* Gunakan metodE 5WHYS untuk menentukan Root Cause; Fish Bone; Diagram alir,Penilaian situasi:');
        $sheet->setCellValue('C58', 'Kendali proses dan peningkatan.');


        //PENCEGAHAN & PENGENDALIAN
        $sheet->setCellValue('B62', 'PT. TATA METAL LESTARI:');

        // Judul di B2
        $sheet->setCellValue('B64', 'PROSES PENINGKATAN KINERJA');
        $sheet->mergeCells('B64:L64'); // Menggabungkan sel B2 sampai L2
        $sheet->getStyle('B64')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B64')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Mengatur perataan horizontal ke tengah
        $sheet->getStyle('B64')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER); // Mengatur perataan vertikal ke tengah
        // Mengatur tinggi baris untuk baris 2
        $sheet->getRowDimension(2)->setRowHeight(100); // Mengatur tinggi baris ke 30 unit

        $sheet->getStyle('B64:L110')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B64:L110')->getBorders()->getOutline()->getColor()->setARGB('000000');
        $sheet->getStyle('B64:L110')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B64:L110')->getBorders()->getOutline()->getColor()->setARGB('000000');
        $sheet->getStyle('B64:L64')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B64:L64')->getBorders()->getOutline()->getColor()->setARGB('000000');


        $sheet->setCellValue('H66', 'PPK NO.')->getStyle('H64')->getFont()->setBold(true);
        $sheet->setCellValue('I66', $this->ppk->nomor_surat);

        $sheet->setCellValue('C68', '3. Usulan tindakan: Jelaskan apa, siapa dan kapan akan dilaksanakan dan siapa yang akan')->getStyle('C68')->getFont()->setBold(true);
        $sheet->setCellValue('C69', 'melakukan tindakan Penanggulangan/Pencegahan tersebut dan kapan akan diselesaikan.')->getStyle('C69')->getFont()->setBold(true);

        // Set the content for the headers from C71 to K71
$sheet->setCellValue('C71', 'Header 1')->getStyle('C71')->getFont()->setBold(true);
$sheet->setCellValue('D71', 'Header 2')->getStyle('D71')->getFont()->setBold(true);
$sheet->setCellValue('E71', 'Header 3')->getStyle('E71')->getFont()->setBold(true);
$sheet->setCellValue('F71', 'Header 4')->getStyle('F71')->getFont()->setBold(true);
$sheet->setCellValue('G71', 'Header 5')->getStyle('G71')->getFont()->setBold(true);
$sheet->setCellValue('H71', 'Header 6')->getStyle('H71')->getFont()->setBold(true);
$sheet->setCellValue('I71', 'Header 7')->getStyle('I71')->getFont()->setBold(true);
$sheet->setCellValue('J71', 'Header 8')->getStyle('J71')->getFont()->setBold(true);
$sheet->setCellValue('K71', 'Header 9')->getStyle('K71')->getFont()->setBold(true);

// Apply borders to the header row
$sheet->getStyle('C71:K71')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Set the content for the next row from C72 to K72
$sheet->setCellValue('C72', 'Value 1')->getStyle('C72')->getFont()->setBold(true);
$sheet->setCellValue('D72', 'Value 2')->getStyle('D72')->getFont()->setBold(true);
$sheet->setCellValue('E72', 'Value 3')->getStyle('E72')->getFont()->setBold(true);
$sheet->setCellValue('F72', 'Value 4')->getStyle('F72')->getFont()->setBold(true);
$sheet->setCellValue('G72', 'Value 5')->getStyle('G72')->getFont()->setBold(true);
$sheet->setCellValue('H72', 'Value 6')->getStyle('H72')->getFont()->setBold(true);
$sheet->setCellValue('I72', 'Value 7')->getStyle('I72')->getFont()->setBold(true);
$sheet->setCellValue('J72', 'Value 8')->getStyle('J72')->getFont()->setBold(true);
$sheet->setCellValue('K72', 'Value 9')->getStyle('K72')->getFont()->setBold(true);

// Apply borders to the second row
$sheet->getStyle('C72:K72')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Set the content for the next row from C73 to K73
$sheet->setCellValue('C73', 'Detail 1')->getStyle('C73')->getFont()->setBold(true);
$sheet->setCellValue('D73', 'Detail 2')->getStyle('D73')->getFont()->setBold(true);
$sheet->setCellValue('E73', 'Detail 3')->getStyle('E73')->getFont()->setBold(true);
$sheet->setCellValue('F73', 'Detail 4')->getStyle('F73')->getFont()->setBold(true);
$sheet->setCellValue('G73', 'Detail 5')->getStyle('G73')->getFont()->setBold(true);
$sheet->setCellValue('H73', 'Detail 6')->getStyle('H73')->getFont()->setBold(true);
$sheet->setCellValue('I73', 'Detail 7')->getStyle('I73')->getFont()->setBold(true);
$sheet->setCellValue('J73', 'Detail 8')->getStyle('J73')->getFont()->setBold(true);
$sheet->setCellValue('K73', 'Detail 9')->getStyle('K73')->getFont()->setBold(true);

// Apply borders to the third row
$sheet->getStyle('C73:K73')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    }


}
