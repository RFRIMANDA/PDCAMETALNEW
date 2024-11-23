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
        $sheet->setCellValue('C5', 'Penerima')->getStyle('C5')->getFont()->setBold(true);
        $sheet->setCellValue('E5', $this->ppk->penerimaUser->nama_user);
        $sheet->setCellValue('H3', 'PPK NO.')->getStyle('H3')->getFont()->setBold(true);
        $sheet->setCellValue('I3', $this->ppk->nomor_surat);

        $sheet->setCellValue('C7', 'Departemen:')->getStyle('C7')->getFont()->setBold(true);
        $sheet->setCellValue('E7', $this->ppk->divisipenerima);
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

        // Tanda Tangan Section
        $sheet->setCellValue('I27', 'Tanda Tangan:');
        $sheet->setCellValue('I29', 'Inisiator/Auditor:');
        $sheet->setCellValue('I31', 'Tanda Tangan: __________');
        $sheet->setCellValue('I32', 'Proses Owner/Auditee:');
        $sheet->setCellValue('I29', $this->ppk->pembuatUser->nama_user)->getStyle('I29')->getFont()->setBold(true);

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
         $sheet->setCellValue('I28', 'Inisiator/Auditor:');
         $sheet->setCellValue('I31', 'Tanda Tangan:');
         $sheet->setCellValue('I32', 'Proses Owner/Auditee:');
         $sheet->setCellValue('I33', $this->ppk->penerimaUser->nama_user)->getStyle('I33')->getFont()->setBold(true);

         if ($this->ppkdua && $this->ppkdua->signaturepenerima && file_exists(public_path('admin/img/' . $this->ppkdua->signaturepenerima))) {
            $drawingPenerima = new Drawing();
            $drawingPenerima->setName('Tanda Tangan Auditor');
            $drawingPenerima->setDescription('Tanda Tangan Auditor');
            $drawingPenerima->setPath(public_path('admin/img/' . $this->ppkdua->signaturepenerima));
            $drawingPenerima->setHeight(50);
            $drawingPenerima->setCoordinates('J32'); // Posisi tanda tangan penerima
            $drawingPenerima->setOffsetX(20);
            $drawingPenerima->setWorksheet($sheet); // Attach drawing to worksheet
        } else {
            // Jika file tidak ditemukan, tampilkan "Tidak Ada Tanda Tangan"
            $sheet->setCellValue('J32', 'Tidak Ada Tanda Tangan');
        }

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
        $sheet->setCellValue('B62', 'PT. TATA METAL LESTARI');

        // Judul di B2
        $sheet->setCellValue('B64', 'PROSES PENINGKATAN KINERJA');
        $sheet->getRowDimension(64)->setRowHeight(40);
        $sheet->mergeCells('B64:L64'); // Menggabungkan sel B2 sampai L2
        $sheet->getStyle('B64')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B64')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Mengatur perataan horizontal ke tengah
        $sheet->getStyle('B64')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER); // Mengatur perataan vertikal ke tengah
        // Mengatur tinggi baris untuk baris 2
        $sheet->getRowDimension(2)->setRowHeight(100); // Mengatur tinggi baris ke 30 unit

        $sheet->getStyle('B64:L120')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B64:L120')->getBorders()->getOutline()->getColor()->setARGB('000000');
        $sheet->getStyle('B64:L120')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B64:L120')->getBorders()->getOutline()->getColor()->setARGB('000000');
        $sheet->getStyle('B64:L64')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('B64:L64')->getBorders()->getOutline()->getColor()->setARGB('000000');


        $sheet->setCellValue('H66', 'PPK NO.')->getStyle('H64')->getFont()->setBold(true);
        $sheet->setCellValue('I66', $this->ppk->nomor_surat);

        $sheet->setCellValue('C68', '3. Usulan tindakan: Jelaskan apa, siapa dan kapan akan dilaksanakan dan siapa yang akan')->getStyle('C68')->getFont()->setBold(true);
        $sheet->setCellValue('C69', 'melakukan tindakan Penanggulangan/Pencegahan tersebut dan kapan akan diselesaikan.')->getStyle('C69')->getFont()->setBold(true);

        // Set the content for the headers from C71 to K71
// Set the width for columns C to K (or adjust accordingly)
$sheet->getColumnDimension('C')->setWidth(20);  // Set custom width for column C
$sheet->getColumnDimension('D')->setWidth(20);  // Set custom width for column D
$sheet->getColumnDimension('E')->setWidth(20);  // Set custom width for column E
$sheet->getColumnDimension('F')->setWidth(20);  // Set custom width for column F
$sheet->getColumnDimension('G')->setWidth(20);  // Set custom width for column G
$sheet->getColumnDimension('H')->setWidth(20);  // Set custom width for column H
$sheet->getColumnDimension('I')->setWidth(20);  // Set custom width for column I
$sheet->getColumnDimension('J')->setWidth(20);  // Set custom width for column J
$sheet->getColumnDimension('K')->setWidth(20);  // Set custom width for column K

// Set the height for the rows (C71 to F73)
$sheet->getRowDimension(71)->setRowHeight(25);  // Set custom height for row 71
$sheet->getRowDimension(72)->setRowHeight(65);  // Set custom height for row 72
$sheet->getRowDimension(73)->setRowHeight(65);  // Set custom height for row 73

// Apply borders to the header row from C71 to K71
$sheet->getStyle('C71:K71')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Merge cells C72 and D72
$sheet->mergeCells('C71:D71');
$sheet->mergeCells('C72:D72');
$sheet->mergeCells('C73:D73');
// Merge cells E72 to I72
$sheet->mergeCells('E71:I71');
$sheet->mergeCells('E72:I72');
$sheet->mergeCells('E73:I73');

$sheet->setCellValue('E71', 'Tindakan')->getStyle('E71')->getFont()->setBold(true);
$sheet->getStyle('E71')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('E71')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('J71', 'Target Tanggal')->getStyle('J71')->getFont()->setBold(true);
$sheet->getStyle('J71')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('J71')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('K71', 'PIC')->getStyle('K71')->getFont()->setBold(true);
$sheet->getStyle('K71')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->getStyle('K71')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);



// Set the content for the merged cells
$sheet->setCellValue('C72', 'Penanggulangan')->getStyle('C72')->getFont()->setBold(true);
$sheet->getStyle('C72')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet->setCellValue('E72', $this->ppktiga->penanggulangan);
$sheet->getStyle('C72:K72')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);// Mengatur perataan ke kiri
$sheet->getStyle('C72:K72')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);// Mengatur perataan ke tengah secara vertikal
$sheet->getStyle('C72:K72')->getAlignment()->setWrapText(true);
$sheet->setCellValue('J72', \Carbon\Carbon::parse($this->ppktiga->target_tgl)->format('d/m/Y'));
// Fetch and display nama_user for pic1, or display 'Tidak ada PIC' if not available
$sheet->setCellValue('K72', $this->ppktiga->pic1User ? $this->ppktiga->pic1User->nama_user : 'Tidak ada PIC');
$sheet->getStyle('K72')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);// Mengatur perataan ke kiri
$sheet->getStyle('K72')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);// Mengatur perataan ke tengah secara vertikal
$sheet->getStyle('K72')->getAlignment()->setWrapText(true);

// Apply borders to the second row from C72 to K72
$sheet->getStyle('C72:K72')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Set the content for the next row from C73 to K73
$sheet->setCellValue('C73', 'Pencegahan')->getStyle('C73')->getFont()->setBold(true);
$sheet->setCellValue('E73', $this->ppktiga->pencegahan);
$sheet->getStyle('C73:K73')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);// Mengatur perataan ke kiri
$sheet->getStyle('C73:K73')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);// Mengatur perataan ke tengah secara vertikal
$sheet->getStyle('C73:K73')->getAlignment()->setWrapText(true);
$sheet->setCellValue('J73', \Carbon\Carbon::parse($this->ppktiga->target_tgl)->format('d/m/Y'));
// Fetch and display nama_user for pic2, or display 'Tidak ada PIC' if not available
$sheet->setCellValue('K73', $this->ppktiga->pic2User ? $this->ppktiga->pic2User->nama_user : 'Tidak ada PIC');

// Apply borders to the third row from C73 to K73
$sheet->getStyle('C73:K73')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


$sheet->setCellValue('C75', '* Bila tidak cukup, dapat  menggunakan lampiran sesuai dengan format diatas');
$sheet->setCellValue('C79', 'Tanggal');
$sheet->setCellValue('D79',  $this->ppk->created_at->format('d/m/Y'));

if ($this->ppkdua && $this->ppkdua->signaturepenerima && file_exists(public_path('admin/img/' . $this->ppkdua->signaturepenerima))) {
    $drawingPenerimaJ79 = new Drawing();
    $drawingPenerimaJ79->setName('Tanda Tangan Auditor J79');
    $drawingPenerimaJ79->setDescription('Tanda Tangan Auditor J79');
    $drawingPenerimaJ79->setPath(public_path('admin/img/' . $this->ppkdua->signaturepenerima));
    $drawingPenerimaJ79->setHeight(50);
    $drawingPenerimaJ79->setCoordinates('J79'); // Posisi tanda tangan penerima kedua di J79
    $drawingPenerimaJ79->setWorksheet($sheet); // Attach drawing to worksheet
} else {
    // Jika file tidak ditemukan, tampilkan "Tidak Ada Tanda Tangan" di J79
    $sheet->setCellValue('J79', 'Tidak Ada Tanda Tangan');
}
// Posisi tanda tangan penerima
$sheet->setCellValue('I81', 'Tandatangan, ');
$sheet->setCellValue('J82', $this->ppk->penerimaUser->nama_user);

// FORM VERIFIKASI
$sheet->getStyle('B84:L120')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
$sheet->getStyle('B84:L120')->getBorders()->getOutline()->getColor()->setARGB('000000');
$sheet->getStyle('B84:L120')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
$sheet->getStyle('B84:L120')->getBorders()->getOutline()->getColor()->setARGB('000000');

$sheet->setCellValue('C85', '4. Verifikasi Tindakan, sesuai kolom "Target Tanggal"')->getStyle('C85')->getFont()->setBold(true);
$sheet->setCellValue('C87', 'Catatan: ');
$sheet->setCellValue('D88', $this->ppktiga->verifikasi);

$sheet->mergeCells('D88:J88');// Menggabungkan sel D44 sampai J44
$sheet->getStyle('D88:J88')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);// Mengatur perataan ke kiri
$sheet->getStyle('D88:J88')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);// Mengatur perataan ke tengah secara vertikal
$sheet->getRowDimension(88)->setRowHeight(200);// Mengatur tinggi baris untuk baris 44
$sheet->getStyle('D88:J88')->getAlignment()->setWrapText(true);

$sheet->setCellValue('J92', 'Tgl. Verifikasi: ');
$sheet->setCellValue('K92', $this->ppktiga->tinjauan);

$sheet->setCellValue('J93', 'Auditor');
$sheet->setCellValue('J94', $this->ppk->pembuatUser->nama_user)->getStyle('J94')->getFont()->setBold(true);

$sheet->setCellValue('C96', 'Efektifitas Tindakan Penanggulangan/Pencegahan dapat diverifkasi 1(satu) bulan dari Tanggal Verifikasi oleh Auditor');
$sheet->getStyle('B98:L120')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
$sheet->getStyle('B98:L120')->getBorders()->getOutline()->getColor()->setARGB('000000');
$sheet->getStyle('B98:L120')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
$sheet->getStyle('B98:L120')->getBorders()->getOutline()->getColor()->setARGB('000000');

$sheet->mergeCells('C99:G99')
      ->setCellValue('C99', '5. a) Tinjauan Status Penyelesaian Tindakan Penanggulangan' . "\n" . 'b) Tinjauan Efektivitas atas hasil Tindakan Pencegahan')
      ->getStyle('C99')->getFont()->setBold(true);
$sheet->getStyle('C99')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$sheet->getRowDimension(99)->setRowHeight(40);

$sheet->setCellValue('C101', 'Catatan: ');

$sheet->mergeCells('D102:J102');// Menggabungkan sel D44 sampai J44
$sheet->getStyle('D102:J102')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);// Mengatur perataan ke kiri
$sheet->getStyle('D102:J102')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);// Mengatur perataan ke tengah secara vertikal
$sheet->getRowDimension(102)->setRowHeight(150);// Mengatur tinggi baris untuk baris 44
$sheet->getStyle('D102:J102')->getAlignment()->setWrapText(true);

// Apply a single border around C104:G105 and add "TRUE"
$sheet->getStyle('C104:F105')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
$sheet->mergeCells('C104:F105');
$sheet->setCellValue('C104', 'TRUE');
$sheet->getStyle('C104')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
// Apply a single border around C107:G107 and add "FALSE"
$sheet->getStyle('C107:F108')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
$sheet->mergeCells('C107:F108');
$sheet->setCellValue('C107', 'FALSE');
$sheet->getStyle('C107')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

$sheet->mergeCells('G104:K105');
$sheet->setCellValue('G104', ' Efektif, dalam 1 bulan masalah yang sama tidak muncul lagi & tindakan penanggulangan sudah  selesai');
$sheet->getStyle('G104')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

$sheet->mergeCells('G107:K108');
$sheet->setCellValue('G107', 'Tidak efektif, dilanjutkan dengan PPK No. ...');
$sheet->getStyle('G107')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

$sheet->getStyle('B110:L120')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
$sheet->getStyle('B110:L120')->getBorders()->getOutline()->getColor()->setARGB('000000');
$sheet->getStyle('B110:L120')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);
$sheet->getStyle('B110:L120')->getBorders()->getOutline()->getColor()->setARGB('000000');

$sheet->setCellValue('C111', '6. Close Out (apabila efektif)')->getStyle('C111')->getFont()->setBold(true);
$sheet->setCellValue('G115', 'Tanda tangan: ');
$sheet->setCellValue('G117', '(Pembuat/Inisiator): ');
$sheet->setCellValue('G118', $this->ppk->pembuatUser->nama_user);

$sheet->setCellValue('J115', 'Date: ');

$sheet->setCellValue('B122', 'PT. TATA METAL LESTARI ');

}
}
