<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PenjualanPeritemController extends Controller
{
    function index(Request $r)
    {
        if (empty($r->tahun)) {
            $tahun = date('Y');
        } else {
            $tahun = $r->tahun;
        }
        if (empty($r->id_lokasi)) {
            $lokasi = '1';
        } else {
            $lokasi = $r->id_lokasi;
        }

        $penjualan = DB::select("SELECT 
        d.nm_station AS Station,
        b.harga AS Harga,
        if(b.id_distribusi = '2', concat(b.nm_menu,' ','gojek'), b.nm_menu) AS Nama_Menu,
        SUM(CASE WHEN MONTH(a.tgl) = 1 THEN a.qty ELSE 0 END) AS bulan1,
        SUM(CASE WHEN MONTH(a.tgl) = 2 THEN a.qty ELSE 0 END) AS bulan2,
        SUM(CASE WHEN MONTH(a.tgl) = 3 THEN a.qty ELSE 0 END) AS bulan3,
        SUM(CASE WHEN MONTH(a.tgl) = 4 THEN a.qty ELSE 0 END) AS bulan4,
        SUM(CASE WHEN MONTH(a.tgl) = 5 THEN a.qty ELSE 0 END) AS bulan5,
        SUM(CASE WHEN MONTH(a.tgl) = 6 THEN a.qty ELSE 0 END) AS bulan6,
        SUM(CASE WHEN MONTH(a.tgl) = 7 THEN a.qty ELSE 0 END) AS bulan7,
        SUM(CASE WHEN MONTH(a.tgl) = 8 THEN a.qty ELSE 0 END) AS bulan8,
        SUM(CASE WHEN MONTH(a.tgl) = 9 THEN a.qty ELSE 0 END) AS bulan9,
        SUM(CASE WHEN MONTH(a.tgl) = 10 THEN a.qty ELSE 0 END) AS bulan10,
        SUM(CASE WHEN MONTH(a.tgl) = 11 THEN a.qty ELSE 0 END) AS bulan11,
        SUM(CASE WHEN MONTH(a.tgl) = 12 THEN a.qty ELSE 0 END) AS bulan12
    FROM tb_order AS a 
    LEFT JOIN view_menu AS b ON b.id_harga = a.id_harga
    LEFT JOIN tb_menu AS c ON b.id_menu = c.id_menu
    LEFT JOIN tb_station AS d ON d.id_station = c.id_station
    WHERE YEAR(a.tgl) = '$tahun' AND a.id_lokasi = '$lokasi' AND a.id_harga != 0
    GROUP BY a.id_harga;");

        $data = [
            'title' => 'Penjualan pertahun',
            'penjualan' => $penjualan,
            'tahun' => DB::select("SELECT YEAR(a.tgl) as tahun FROM tb_order as a where YEAR(a.tgl) != 0 group by YEAR(a.tgl);"),
            'logout' => $r->session()->get('logout'),
            'thn' => $tahun,
            'id_lokasi' => $lokasi

        ];

        return view('peritem.index', $data);
    }

    function export_per_item(Request $r)
    {
        $tahun = $r->tahun;
        $style_atas = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        );

        $style = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Takemori');
        $sheet1->getStyle("A1:P1")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', '#');
        $sheet1->setCellValue('B1', 'Station');
        $sheet1->setCellValue('C1', 'Harga');
        $sheet1->setCellValue('D1', 'Nama Menu');
        $sheet1->setCellValue('E1', '1');
        $sheet1->setCellValue('F1', '2');
        $sheet1->setCellValue('G1', '3');
        $sheet1->setCellValue('H1', '4');
        $sheet1->setCellValue('I1', '5');
        $sheet1->setCellValue('J1', '6');
        $sheet1->setCellValue('K1', '7');
        $sheet1->setCellValue('L1', '8');
        $sheet1->setCellValue('M1', '9');
        $sheet1->setCellValue('N1', '10');
        $sheet1->setCellValue('O1', '11');
        $sheet1->setCellValue('P1', '12');

        $penjualan = DB::select("SELECT 
        d.nm_station,b.harga,
        if(b.id_distribusi = '2', concat(b.nm_menu,' ','gojek'), b.nm_menu) AS Nama_Menu,
        SUM(CASE WHEN MONTH(a.tgl) = 1 THEN a.qty ELSE 0 END) AS bulan1,
        SUM(CASE WHEN MONTH(a.tgl) = 2 THEN a.qty ELSE 0 END) AS bulan2,
        SUM(CASE WHEN MONTH(a.tgl) = 3 THEN a.qty ELSE 0 END) AS bulan3,
        SUM(CASE WHEN MONTH(a.tgl) = 4 THEN a.qty ELSE 0 END) AS bulan4,
        SUM(CASE WHEN MONTH(a.tgl) = 5 THEN a.qty ELSE 0 END) AS bulan5,
        SUM(CASE WHEN MONTH(a.tgl) = 6 THEN a.qty ELSE 0 END) AS bulan6,
        SUM(CASE WHEN MONTH(a.tgl) = 7 THEN a.qty ELSE 0 END) AS bulan7,
        SUM(CASE WHEN MONTH(a.tgl) = 8 THEN a.qty ELSE 0 END) AS bulan8,
        SUM(CASE WHEN MONTH(a.tgl) = 9 THEN a.qty ELSE 0 END) AS bulan9,
        SUM(CASE WHEN MONTH(a.tgl) = 10 THEN a.qty ELSE 0 END) AS bulan10,
        SUM(CASE WHEN MONTH(a.tgl) = 11 THEN a.qty ELSE 0 END) AS bulan11,
        SUM(CASE WHEN MONTH(a.tgl) = 12 THEN a.qty ELSE 0 END) AS bulan12
        FROM tb_order AS a 
        LEFT JOIN view_menu AS b ON b.id_harga = a.id_harga
        LEFT JOIN tb_menu AS c ON b.id_menu = c.id_menu
        LEFT JOIN tb_station AS d ON d.id_station = c.id_station
        WHERE YEAR(a.tgl) = '$tahun' AND a.id_lokasi = '1' AND a.id_harga != 0
        GROUP BY a.id_harga;");

        $kolom = 2;
        foreach ($penjualan as $no => $p) {
            $sheet1->setCellValue('A' . $kolom, $no + 1);
            $sheet1->setCellValue('B' . $kolom, $p->nm_station);
            $sheet1->setCellValue('C' . $kolom, $p->harga);
            $sheet1->setCellValue('D' . $kolom, $p->Nama_Menu);
            $sheet1->setCellValue('E' . $kolom, $p->bulan1);
            $sheet1->setCellValue('F' . $kolom, $p->bulan2);
            $sheet1->setCellValue('G' . $kolom, $p->bulan3);
            $sheet1->setCellValue('H' . $kolom, $p->bulan4);
            $sheet1->setCellValue('I' . $kolom, $p->bulan5);
            $sheet1->setCellValue('J' . $kolom, $p->bulan6);
            $sheet1->setCellValue('K' . $kolom, $p->bulan7);
            $sheet1->setCellValue('L' . $kolom, $p->bulan8);
            $sheet1->setCellValue('M' . $kolom, $p->bulan9);
            $sheet1->setCellValue('N' . $kolom, $p->bulan10);
            $sheet1->setCellValue('O' . $kolom, $p->bulan11);
            $sheet1->setCellValue('P' . $kolom, $p->bulan12);
            $kolom++;
        }
        $sheet1->getStyle('A2:P' . $kolom - 1)->applyFromArray($style);



        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet(1);
        $sheet2->setTitle('Soondobu');
        $sheet2->getStyle('A1:P1')->applyFromArray($style_atas);

        $sheet2->setCellValue('A1', '#');
        $sheet2->setCellValue('B1', 'Station');
        $sheet2->setCellValue('C1', 'Harga');
        $sheet2->setCellValue('D1', 'Nama Menu');
        $sheet2->setCellValue('E1', '1');
        $sheet2->setCellValue('F1', '2');
        $sheet2->setCellValue('G1', '3');
        $sheet2->setCellValue('H1', '4');
        $sheet2->setCellValue('I1', '5');
        $sheet2->setCellValue('J1', '6');
        $sheet2->setCellValue('K1', '7');
        $sheet2->setCellValue('L1', '8');
        $sheet2->setCellValue('M1', '9');
        $sheet2->setCellValue('N1', '10');
        $sheet2->setCellValue('O1', '11');
        $sheet2->setCellValue('P1', '12');

        $penjualan = DB::select("SELECT 
        d.nm_station,b.harga,
        if(b.id_distribusi = '2', concat(b.nm_menu,' ','gojek'), b.nm_menu) AS Nama_Menu,
        SUM(CASE WHEN MONTH(a.tgl) = 1 THEN a.qty ELSE 0 END) AS bulan1,
        SUM(CASE WHEN MONTH(a.tgl) = 2 THEN a.qty ELSE 0 END) AS bulan2,
        SUM(CASE WHEN MONTH(a.tgl) = 3 THEN a.qty ELSE 0 END) AS bulan3,
        SUM(CASE WHEN MONTH(a.tgl) = 4 THEN a.qty ELSE 0 END) AS bulan4,
        SUM(CASE WHEN MONTH(a.tgl) = 5 THEN a.qty ELSE 0 END) AS bulan5,
        SUM(CASE WHEN MONTH(a.tgl) = 6 THEN a.qty ELSE 0 END) AS bulan6,
        SUM(CASE WHEN MONTH(a.tgl) = 7 THEN a.qty ELSE 0 END) AS bulan7,
        SUM(CASE WHEN MONTH(a.tgl) = 8 THEN a.qty ELSE 0 END) AS bulan8,
        SUM(CASE WHEN MONTH(a.tgl) = 9 THEN a.qty ELSE 0 END) AS bulan9,
        SUM(CASE WHEN MONTH(a.tgl) = 10 THEN a.qty ELSE 0 END) AS bulan10,
        SUM(CASE WHEN MONTH(a.tgl) = 11 THEN a.qty ELSE 0 END) AS bulan11,
        SUM(CASE WHEN MONTH(a.tgl) = 12 THEN a.qty ELSE 0 END) AS bulan12
        FROM tb_order AS a 
        LEFT JOIN view_menu AS b ON b.id_harga = a.id_harga
        LEFT JOIN tb_menu AS c ON b.id_menu = c.id_menu
        LEFT JOIN tb_station AS d ON d.id_station = c.id_station
        WHERE YEAR(a.tgl) = '$tahun' AND a.id_lokasi = '2' AND a.id_harga != 0
        GROUP BY a.id_harga;");

        $kolom = 2;
        foreach ($penjualan as $no => $p) {
            $sheet2->setCellValue('A' . $kolom, $no + 1);
            $sheet2->setCellValue('B' . $kolom, $p->nm_station);
            $sheet2->setCellValue('C' . $kolom, $p->harga);
            $sheet2->setCellValue('D' . $kolom, $p->Nama_Menu);
            $sheet2->setCellValue('E' . $kolom, $p->bulan1);
            $sheet2->setCellValue('F' . $kolom, $p->bulan2);
            $sheet2->setCellValue('G' . $kolom, $p->bulan3);
            $sheet2->setCellValue('H' . $kolom, $p->bulan4);
            $sheet2->setCellValue('I' . $kolom, $p->bulan5);
            $sheet2->setCellValue('J' . $kolom, $p->bulan6);
            $sheet2->setCellValue('K' . $kolom, $p->bulan7);
            $sheet2->setCellValue('L' . $kolom, $p->bulan8);
            $sheet2->setCellValue('M' . $kolom, $p->bulan9);
            $sheet2->setCellValue('N' . $kolom, $p->bulan10);
            $sheet2->setCellValue('O' . $kolom, $p->bulan11);
            $sheet2->setCellValue('P' . $kolom, $p->bulan12);
            $kolom++;
        }
        $sheet2->getStyle('A2:P' . $kolom - 1)->applyFromArray($style);




        $namafile = "Laporan Per Item.xlsx";
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
