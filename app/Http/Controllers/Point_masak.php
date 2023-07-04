<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Str;
use DateTime;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use PointQuery;

class Point_masak extends Controller
{

    public function index(Request $r)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)->where('id_menu', 28)->first();
        if (empty($id_menu)) {
            return back();
        } else {
            $id_lokasi = $r->session()->get('id_lokasi');
            $tgl1 = $r->tgl1 ?? date('Y-m-01');
            $tgl2 = $r->tgl2 ?? date('Y-m-d');

            $total_not_gojek = PointQuery::getTotalNotGojek($id_lokasi, $tgl1, $tgl2);

            $masak = PointQuery::getMasak($id_lokasi, $tgl1, $tgl2);

            $server = DB::select("SELECT a.nama, b.rp_m, sum(l.qty_m) AS qty_m, sum(l.qty_e) AS qty_e, sum(l.qty_sp) AS qty_sp, b.rp_e, b.rp_sp, b.komisi
        FROM tb_karyawan AS a
        left join tb_gaji AS b ON b.id_karyawan = a.id_karyawan
        LEFT JOIN (
               SELECT c.id_karyawan,  c.status, c.id_lokasi,
                if(c.status = 'M', COUNT(c.status), 0) AS qty_m,
                if(c.status = 'E', COUNT(c.status), 0) AS qty_e,
                if(c.status = 'SP', COUNT(c.status), 0) AS qty_sp,
                if(c.status = 'OFF', COUNT(c.status), 0) AS qty_off
                FROM tb_absen AS c 
                WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2' and c.id_lokasi = '$id_lokasi'
                GROUP BY c.id_karyawan, c.status
                ) AS l ON l.id_karyawan = a.id_karyawan

        LEFT JOIN (
        SELECT a.admin, SUM(if(a.voucher != '0' ,0, a.hrg )) AS komisi
        FROM view_summary_server AS a
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY a.admin
        ) AS b ON b.admin = a.nama

                WHERE  a.tgl_masuk <= '$tgl2' and l.id_lokasi ='$id_lokasi' and a.id_status ='2'
                group by a.id_karyawan
    ");
            $data = [
                'title' => 'Point Masak',
                'masak' => $masak,
                'server' => $server,
                'tgl1' => $tgl1,
                'tgl2' => $tgl2,
                'service' => $total_not_gojek,
                'jumlah_orang' => DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Kitchen')->where('id_lokasi', $id_lokasi)->first(),
                'persen' => DB::table('persentse_komisi')->where('nama_persentase', 'Kitchen')->where('id_lokasi', $id_lokasi)->first(),
                'jumlah_orang2' => DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', $id_lokasi)->first(),
                'persen2' => DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', $id_lokasi)->first(),
                'logout' => $r->session()->get('logout'),
            ];

            return view('point_masak.point_masak', $data);
        }
    }
    public function point_kitchen(Request $r)
    {
        $id_user = Auth::user()->id;
        $arr = [97, 1, 2, 3, 4];
        if (!in_array($id_user, $arr)) {
            return back();
        } else {

            $id_lokasi = $r->id_lokasi ?? 1;

            $tgl1 = $r->tgl1 ?? date('Y-m-01');
            $tgl2 = $r->tgl2 ?? date('Y-m-d');

            $lamaMenit = DB::table('tb_menit')->where('id_lokasi', $id_lokasi)->first();


            $total_not_gojek = PointQuery::getTotalNotGojek($id_lokasi, $tgl1, $tgl2);

            $masak = PointQuery::getMasak($id_lokasi, $tgl1, $tgl2);

            $absen = PointQuery::getAbsen($id_lokasi, $tgl1, $tgl2);

            $data = [
                'title' => 'Point Masak',
                'masak' => $masak,
                'absen' => $absen,
                'tgl1' => $tgl1,
                'tgl2' => $tgl2,
                'id_lokasi' => $id_lokasi,
                'service' => $total_not_gojek,
                'jumlah_orang' => DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Kitchen')->where('id_lokasi', $id_lokasi)->first(),
                'persen' => DB::table('persentse_komisi')->where('nama_persentase', 'Kitchen')->where('id_lokasi', $id_lokasi)->first(),
                'logout' => $r->session()->get('logout'),
            ];

            return view('point_masak.point_kitchen', $data);
        }
    }

    public function detailPoint(Request $r)
    {
        $id_lokasi = $r->id_lokasi ?? 1;

        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-d');

        $id_karyawan = $r->id_karyawan;
        $nm_karyawan = DB::table('tb_karyawan')->where('id_karyawan', $id_karyawan)->first();
        $detail = DB::select("SELECT b.tipe,c.nama,a.tgl,a.no_order, b.nm_menu, b.point_menu, a.lama_masak, a.nilai_koki FROM `view_point2` as a
        LEFT JOIN view_menu2 as b on a.id_harga = b.id_harga
        LEFT JOIN tb_karyawan as c on a.koki = c.id_karyawan
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' AND a.id_lokasi = '$id_lokasi' AND c.id_karyawan = '$id_karyawan' AND a.lama_masak <= 25");

        $data = [
            'point' => $detail,
            'nm_karyawan' => $nm_karyawan->nama,
            'id_karyawan' => $id_karyawan,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'id_lokasi' => $id_lokasi,
        ];
        return view('point_masak.detail_point', $data);
    }

    public function exportDetailPoint(Request $r)
    {
        $id_karyawan = $r->id_karyawan;
        $id_lokasi = $r->id_lokasi;
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;

        $nm_karyawan = DB::table('tb_karyawan')->where('id_karyawan', $id_karyawan)->first();

        $detail = DB::select("SELECT c.nama,a.tgl,a.no_order, b.nm_menu, b.point_menu, a.lama_masak, a.nilai_koki FROM `view_point2` as a
        LEFT JOIN view_menu2 as b on a.id_harga = b.id_harga
        LEFT JOIN tb_karyawan as c on a.koki = c.id_karyawan
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' AND a.id_lokasi = '$id_lokasi' AND c.id_karyawan = '$id_karyawan' AND a.lama_masak <= 25");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1:D4')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        // lebar kolom
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(13);
        // header text
        $sheet->getStyle("A1")->getFont()->setBold(true);
        $tpoint = 0;
        foreach ($detail as $k) {
            if ($k->point_menu != '') {
                $tpoint += $k->point_menu;
            } else {
                continue;
            }
        }
        $sheet
            ->setCellValue('A1', $nm_karyawan->nama)
            ->setCellValue('B1', 'NO')
            ->setCellValue('C1', 'NO ORDER')
            ->setCellValue('D1', 'NAMA MENU')
            ->setCellValue('E1', 'POINT (' . $tpoint . ')')
            ->setCellValue('F1', 'LAMA MASAK');

        $kolom = 2;
        $no = 1;

        foreach ($detail as $k) {
            if ($k->point_menu != '') {
                $sheet
                    ->setCellValue('B' . $kolom, $no++)
                    ->setCellValue('C' . $kolom, $k->no_order)
                    ->setCellValue('D' . $kolom, $k->nm_menu)
                    ->setCellValue('E' . $kolom, $k->point_menu)
                    ->setCellValue('F' . $kolom, $k->lama_masak . ' Menit');
                $kolom++;
            } else {
                continue;
            }
        }
        $writer = new Xlsx($spreadsheet);
        $style = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];
        $batas = $kolom - 1;
        $sheet->getStyle('B1:F' . $batas)->applyFromArray($style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="DETAIL POINT ' . $nm_karyawan->nama . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function point_export(Request $r)
    {
        $id_lokasi = $r->session()->get('id_lokasi');
        if (empty($r->tgl1)) {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-d');
        } else {
            $tgl1 = $r->tgl1;
            $tgl2 = $r->tgl2;
        }
        $lamaMenit = DB::table('tb_menit')->where('id_lokasi', $id_lokasi)->first();
        $service = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE tb_transaksi.id_lokasi = '$id_lokasi' and  dt_order.id_distribusi != '2' AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $jumlah_orang = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Kitchen')->where('id_lokasi', $id_lokasi)->first();
        $persen = DB::table('persentse_komisi')->where('nama_persentase', 'Kitchen')->where('id_lokasi', $id_lokasi)->first();

        $masak = DB::select("SELECT a.nama,b.rp_m, sum(l.qty_m) AS qty_m, sum(l.qty_e) AS qty_e, sum(l.qty_sp) AS qty_sp,e.point_gagal,f.point_berhasil, b.rp_e, b.rp_sp
            FROM tb_karyawan AS a
            left join tb_gaji AS b ON b.id_karyawan = a.id_karyawan
            LEFT JOIN (
                    SELECT c.id_karyawan,  c.status, c.id_lokasi,
                    if(c.status = 'M', COUNT(c.status), 0) AS qty_m,
                    if(c.status = 'E', COUNT(c.status), 0) AS qty_e,
                    if(c.status = 'SP', COUNT(c.status), 0) AS qty_sp,
                    if(c.status = 'OFF', COUNT(c.status), 0) AS qty_off
                    FROM tb_absen AS c 
                    WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2' and c.id_lokasi = '$id_lokasi'
                    GROUP BY c.id_karyawan, c.status
                    ) AS l ON l.id_karyawan = a.id_karyawan
                    
                    LEFT JOIN (
                    SELECT koki, SUM(nilai_koki) as point_gagal FROM view_point2 
                    WHERE tgl BETWEEN '$tgl1' AND '$tgl2' AND lama_masak > $lamaMenit->menit and id_lokasi = '$id_lokasi'
                    GROUP BY koki , id_lokasi
                    )e ON a.id_karyawan = e.koki
                    
                    LEFT JOIN (
                        SELECT koki, SUM(nilai_koki) as point_berhasil FROM view_point2 
                        WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND lama_masak <= $lamaMenit->menit and id_lokasi = '$id_lokasi'
                        GROUP BY koki , id_lokasi
                    )f ON a.id_karyawan = f.koki


                        WHERE a.id_status = '1' and a.tgl_masuk <= '$tgl2' and l.id_lokasi ='$id_lokasi' and a.id_posisi not in ('3','2') and a.point =  'Y'
                        group by a.id_karyawan
        ");

        $l = 1;
        $point = 0;
        $point2 = 0;
        foreach ($masak as $m) {
            $orang = $l++;
            $point += $m->point_berhasil + $m->point_gagal;
        }

        $service_charge = $service->total * 0.07;
        $kom =  round((((($service_charge  / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah)  * $orang));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Point Masak');

        $sheet->getStyle('A1:F1')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        // lebar kolom
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(17);
        $sheet->getColumnDimension('E')->setWidth(17);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->getColumnDimension('H')->setWidth(21);
        $sheet->getColumnDimension('J')->setWidth(17);
        // header text
        $sheet
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'NAMA KARYAWAN')
            ->setCellValue('C1', 'POINT MASAK')
            ->setCellValue('D1', 'KOM POINT MASAK')
            ->setCellValue('E1', 'NON POINT MASAK')
            ->setCellValue('F1', 'KOM NON POINT MASAK')
            ->setCellValue('H1', 'Org P ')
            ->setCellValue('I1', $jumlah_orang->jumlah)
            ->setCellValue('H2', 'Org R ')
            ->setCellValue('I2', $orang)
            ->setCellValue('H4', 'Service charge P ')
            ->setCellValue('I4', ($service_charge / 7) * $persen->jumlah_persen)
            ->setCellValue('H5', 'Service charge R')
            ->setCellValue('I5', $kom);
        $kolom = 2;



        $i = 1;
        foreach ($masak as $k) {
            $sheet->setCellValue('A' . $kolom, $i++);
            $sheet->setCellValue('B' . $kolom, $k->nama);
            $kom1 =  round(($k->point_berhasil / $point) * $kom, 0);
            $sheet->setCellValue('C' . $kolom, $k->point_berhasil);
            $sheet->setCellValue('D' . $kolom, $kom1);
            $sheet->setCellValue('E' . $kolom, $k->point_gagal);
            $kom3 =  round(($k->point_gagal / $point) * $kom);
            $sheet->setCellValue('F' . $kolom, $kom3);
            $kolom++;
        }

        $writer = new Xlsx($spreadsheet);
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
        $batas = $masak;
        $batas = count($batas) + 1;
        $sheet->getStyle('A1:F' . $batas)->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);

        $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2->setTitle('Gaji Kitchen');

        // lebar kolom
        $sheet2->getColumnDimension('A')->setWidth(3);
        $sheet2->getColumnDimension('B')->setWidth(20);
        $sheet2->getColumnDimension('C')->setWidth(15);
        $sheet2->getColumnDimension('D')->setWidth(14.36);
        $sheet2->getColumnDimension('E')->setWidth(13);
        $sheet2->getColumnDimension('F')->setWidth(16.9);
        $sheet2->getColumnDimension('G')->setWidth(16);
        $sheet2->getColumnDimension('J')->setWidth(13);
        $sheet2->getColumnDimension('K')->setWidth(14);
        $sheet2->getColumnDimension('L')->setWidth(14);
        // header text
        $sheet2
            ->setCellValue('A1', 'NO')
            ->setCellValue('B1', 'Nama')
            ->setCellValue('C1', 'M')
            ->setCellValue('D1', 'E')
            ->setCellValue('E1', 'SP')
            ->setCellValue('F1', 'Rp M')
            ->setCellValue('G1', 'Gaji');

        $kolom = 2;
        $i = 1;
        foreach ($masak as $k) {
            $spreadsheet->setActiveSheetIndex(1);
            $sheet2->setCellValue('A' . $kolom, $i++);
            $sheet2->setCellValue('B' . $kolom, $k->nama);
            $sheet2->setCellValue('C' . $kolom, $k->qty_m);
            $sheet2->setCellValue('D' . $kolom, $k->qty_e);
            $sheet2->setCellValue('E' . $kolom, $k->qty_sp);
            $sheet2->setCellValue('F' . $kolom, $k->rp_m);
            $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp);
            $sheet2->setCellValue('G' . $kolom, $gaji);
            $kolom++;
        }

        $writer = new Xlsx($spreadsheet);
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
        $batas = $masak;
        $batas = count($batas) + 1;
        $sheet2->getStyle('A1:G' . $batas)->applyFromArray($style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="POINT-TS.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function point_export_server(Request $r)
    {
        $id_lokasi = $r->id_lokasi ?? 1;

        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-d');
        $lokasi = $id_lokasi == 1 ? 'takemori' : 'soondobu';


        $serviceTkm = PointQuery::getService(1, $tgl1, $tgl2);

        $jumlah_orangTkm = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Kitchen')->where('id_lokasi', 1)->first();
        $persenTkm = DB::table('persentse_komisi')->where('nama_persentase', 'Kitchen')->where('id_lokasi', 1)->first();

        $masakTkm = PointQuery::getMasak(1, $tgl1, $tgl2);

        $absenTkm = PointQuery::getAbsen(1, $tgl1, $tgl2);

        // soondobu
        $serviceSdb = PointQuery::getService(2, $tgl1, $tgl2);

        $jumlah_orangSdb = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Kitchen')->where('id_lokasi', 2)->first();
        $persenSdb = DB::table('persentse_komisi')->where('nama_persentase', 'Kitchen')->where('id_lokasi', 2)->first();

        $masakSdb = PointQuery::getMasak(2, $tgl1, $tgl2);

        $absenSdb = PointQuery::getAbsen(2, $tgl1, $tgl2);

        // takemori
        $l = 1;
        $point = 0;
        $point2 = 0;
        foreach ($masakTkm as $m) {
            $orang = $l++;
            $point += $m->point_berhasil + $m->point_gagal;
        }

        // soondobu
        $lSdb = 1;
        $pointSdb = 0;
        $point2Sdb = 0;
        foreach ($masakSdb as $m) {
            $orangSdb = $lSdb++;
            $pointSdb += $m->point_berhasil + $m->point_gagal;
        }

        // takemori
        $service_charge = $serviceTkm->total * 0.07;
        $kom =  round((((($service_charge  / 7) * $persenTkm->jumlah_persen) / $jumlah_orangTkm->jumlah)  * $orang));

        // soondobu
        $service_chargeSdb = $serviceSdb->total * 0.07;
        $komSdb =  round((((($service_chargeSdb  / 7) * $persenSdb->jumlah_persen) / $jumlah_orangSdb->jumlah)  * $orangSdb));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Point Masak');

        $sheet->getStyle('A1:L1')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        // lebar kolom
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(9);
        $sheet->getColumnDimension('D')->setWidth(9);
        $sheet->getColumnDimension('E')->setWidth(9);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->getColumnDimension('H')->setWidth(21);
        $sheet->getColumnDimension('J')->setWidth(17);
        // header text
        // takemori
        $sheet
            ->setCellValue('B1', 'TAKEMORI')
            ->setCellValue('A2', 'NO')
            ->setCellValue('B2', 'NAMA')
            ->setCellValue('C2', 'POINT MASAK')
            ->setCellValue('D2', 'KOM POINT MASAK')
            ->setCellValue('E2', 'NON POINT MASAK')
            ->setCellValue('F2', 'KOM NON POINT MASAK')
            ->setCellValue('H2', 'Org P ')
            ->setCellValue('I2', $jumlah_orangTkm->jumlah)
            ->setCellValue('H3', 'Org R ')
            ->setCellValue('I3', $orang)
            ->setCellValue('H5', 'Service charge P ')
            ->setCellValue('I5', ($service_charge / 7) * $persenTkm->jumlah_persen)
            ->setCellValue('H6', 'Service charge R')
            ->setCellValue('I6', $kom);

        $kolom = 3;

        $i = 1;
        $ttlAbsen = 0;
        foreach ($masakTkm as $k) {
            $ttlAbsen = $k->qty_m + $k->qty_e + $k->qty_sp;
            $sheet->setCellValue('A' . $kolom, $i++);
            $sheet->setCellValue('B' . $kolom, $k->nama);
            $kom1 =  round(($k->point_berhasil / $point) * $kom, 0);
            $sheet->setCellValue('C' . $kolom, $k->point_berhasil);
            $sheet->setCellValue('D' . $kolom, $kom1);
            $sheet->setCellValue('E' . $kolom, $k->point_gagal);
            $kom3 =  round(($k->point_gagal / $point) * $kom);
            $sheet->setCellValue('F' . $kolom, $kom3);
            $kolom++;
        }

        $writer = new Xlsx($spreadsheet);
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
        $batas = $masakTkm;
        $batas = count($batas) + 2;
        $sheet->getStyle('A2:F' . $batas)->applyFromArray($style);


        $rowSdb = $batas + 2;
        $rSdb = $rowSdb + 1;
        $rsSdb = $rowSdb + 2;
        $rspSdb = $rsSdb + 2;
        $rsrSdb = $rspSdb + 1;
        $sheet
            ->setCellValue('B' . $rowSdb, 'SOONDOBU')
            ->setCellValue('A' . $rSdb, 'NO')
            ->setCellValue('B' . $rSdb, 'NAMA')
            ->setCellValue('C' . $rSdb, 'POINT MASAK')
            ->setCellValue('D' . $rSdb, 'KOM POINT MASAK')
            ->setCellValue('E' . $rSdb, 'NON POINT MASAK')
            ->setCellValue('F' . $rSdb, 'KOM NON POINT MASAK')
            ->setCellValue('H' . $rSdb, 'Org P ')
            ->setCellValue('I' . $rSdb, $jumlah_orangSdb->jumlah)
            ->setCellValue('H' . $rsSdb, 'Org R ')
            ->setCellValue('I' . $rsSdb, $orangSdb)
            ->setCellValue('H' . $rspSdb, 'Service charge P ')
            ->setCellValue('I' . $rspSdb, ($service_chargeSdb / 7) * $persenSdb->jumlah_persen)
            ->setCellValue('H' . $rsrSdb, 'Service charge R')
            ->setCellValue('I' . $rsrSdb, $komSdb);

        $kolomSdb = $rsSdb;



        $i = 1;
        $ttlAbsenSdb = 0;
        foreach ($masakSdb as $k) {
            $ttlAbsenSdb = $k->qty_m + $k->qty_e + $k->qty_sp;
            $sheet->setCellValue('A' . $kolomSdb, $i++);
            $sheet->setCellValue('B' . $kolomSdb, $k->nama);
            $kom1 =  round(($k->point_berhasil / $pointSdb) * $komSdb, 0);
            $sheet->setCellValue('C' . $kolomSdb, $k->point_berhasil);
            $sheet->setCellValue('D' . $kolomSdb, $kom1);
            $sheet->setCellValue('E' . $kolomSdb, $k->point_gagal);
            $kom3 =  round(($k->point_gagal / $pointSdb) * $komSdb);
            $sheet->setCellValue('F' . $kolomSdb, $kom3);
            $kolomSdb++;
        }


        $styleSdb = [
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
        $batasSdb = $kolomSdb - 1;
        $batasAwal = $rsSdb - 1;
        // dd($batasAwal);
        $sheet->getStyle('A' . $batasAwal . ':F' . $batasSdb)->applyFromArray($styleSdb);

        // absensi ----------------
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);

        $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2->setTitle('Gaji Kitchen');

        // lebar kolom
        $sheet2->getColumnDimension('A')->setWidth(3);
        $sheet2->getColumnDimension('B')->setWidth(20);
        $sheet2->getColumnDimension('C')->setWidth(15);
        $sheet2->getColumnDimension('D')->setWidth(14.36);
        $sheet2->getColumnDimension('E')->setWidth(13);
        $sheet2->getColumnDimension('F')->setWidth(16.9);
        $sheet2->getColumnDimension('G')->setWidth(16);
        $sheet2->getColumnDimension('J')->setWidth(13);
        $sheet2->getColumnDimension('K')->setWidth(14);
        $sheet2->getColumnDimension('L')->setWidth(14);
        // header text
        $sheet2
            ->setCellValue('B1', 'TAKEMORI')
            ->setCellValue('A2', 'NO')
            ->setCellValue('B2', 'Nama')
            ->setCellValue('C2', 'M')
            ->setCellValue('D2', 'E')
            ->setCellValue('E2', 'SP')
            ->setCellValue('F2', 'Total')
            ->setCellValue('G2', 'Rp M')
            ->setCellValue('H2', 'Rp E')
            ->setCellValue('I2', 'Rp SP')
            ->setCellValue('J2', 'Gaji')
            ->setCellValue('K2', 'Kom Point Masak')
            ->setCellValue('L2', 'Total Kom & Gaji')
            ->setCellValue('M2', 'Denda')
            ->setCellValue('N2', 'Kasbon')
            ->setCellValue('O2', 'Sisa Gaji')
            ->setCellValue('P2', 'Terima Point')
            ->setCellValue('Q2', 'Lama Kerja');

        $kolomTkm = 3;
        $i = 1;
        $ttlAbsenTkm = 0;
        foreach ($absenTkm as $k) {
            $ttlAbsenTkm = $k->qty_m + $k->qty_e + $k->qty_sp;
            $totalKerja = new DateTime($k->tgl_masuk);
            $today = new DateTime();
            $tKerja = $today->diff($totalKerja);
            $spreadsheet->setActiveSheetIndex(1);
            $sheet2->setCellValue('A' . $kolomTkm, $i++);
            $sheet2->setCellValue('B' . $kolomTkm, $k->nama);
            $sheet2->setCellValue('C' . $kolomTkm, $k->qty_m);
            $sheet2->setCellValue('D' . $kolomTkm, $k->qty_e);
            $sheet2->setCellValue('E' . $kolomTkm, $k->qty_sp);
            $sheet2->setCellValue('F' . $kolomTkm, $ttlAbsenTkm);
            $sheet2->setCellValue('G' . $kolomTkm, $k->rp_m);
            $sheet2->setCellValue('H' . $kolomTkm, $k->rp_e);
            $sheet2->setCellValue('I' . $kolomTkm, $k->rp_sp);
            $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp);
            $kom1 =  round(($k->point_berhasil / $point) * $kom, 0);
            $sheet2->setCellValue('J' . $kolomTkm, $gaji);
            $sheet2->setCellValue('K' . $kolomTkm, $k->point == 'T' ? '0' : $kom1);
            $sheet2->setCellValue('L' . $kolomTkm, $gaji + $kom1);
            $sheet2->setCellValue('M' . $kolomTkm, $k->denda);
            $sheet2->setCellValue('N' . $kolomTkm, $k->kasbon);
            $sheet2->setCellValue('O' . $kolomTkm, ($gaji + $kom1) - $k->denda - $k->kasbon);
            $sheet2->setCellValue('P' . $kolomTkm, $k->point == 'Y' ? 'Ya' : 'Tidak');
            $sheet2->setCellValue('Q' . $kolomTkm, $tKerja->y . ' Tahun ' . $tKerja->m . ' Bulan');
            $kolomTkm++;
        }


        $batas = $absenTkm;
        $batasA = count($batas) + 2;
        $sheet2->getStyle('A2:Q' . $batasA)->applyFromArray($style);

        $rowSdba = $batasA + 2;
        $rSdba = $rowSdba + 1;
        $rsSdba = $rowSdb + 2;
        $rspSdba = $rsSdb + 2;
        $rsrSdba = $rspSdb + 1;
        $sheet2
        ->setCellValue('B' . $rowSdba, 'Soondobu')
        ->setCellValue('A' . $rSdba, 'NO')
        ->setCellValue('B' . $rSdba, 'Nama')
        ->setCellValue('C' . $rSdba, 'M')
        ->setCellValue('D' . $rSdba, 'E')
        ->setCellValue('E' . $rSdba, 'SP')
        ->setCellValue('F' . $rSdba, 'Total')
        ->setCellValue('G' . $rSdba, 'Rp M')
        ->setCellValue('H' . $rSdba, 'Rp E')
        ->setCellValue('I' . $rSdba, 'Rp SP')
        ->setCellValue('J' . $rSdba, 'Gaji')
        ->setCellValue('K' . $rSdba, 'Kom Point Masak')
        ->setCellValue('L' . $rSdba, 'Total Kom & Gaji')
        ->setCellValue('M' . $rSdba, 'Denda')
        ->setCellValue('N' . $rSdba, 'Kasbon')
        ->setCellValue('O' . $rSdba, 'Sisa Gaji')
        ->setCellValue('P' . $rSdba, 'Terima Point')
        ->setCellValue('Q' . $rSdba, 'Lama Kerja');

        $kolomSdba = $rSdba;
        $i = 1;
        $ttlAbsenSdb = 0;
        foreach ($absenSdb as $k) {
            $ttlAbsenSdb = $k->qty_m + $k->qty_e + $k->qty_sp;
            $totalKerja = new DateTime($k->tgl_masuk);
            $today = new DateTime();
            $tKerja = $today->diff($totalKerja);
            $sheet2->setCellValue('A' . $kolomSdba, $i++);
            $sheet2->setCellValue('B' . $kolomSdba, $k->nama);
            $sheet2->setCellValue('C' . $kolomSdba, $k->qty_m);
            $sheet2->setCellValue('D' . $kolomSdba, $k->qty_e);
            $sheet2->setCellValue('E' . $kolomSdba, $k->qty_sp);
            $sheet2->setCellValue('F' . $kolomSdba, $ttlAbsenSdb);
            $sheet2->setCellValue('G' . $kolomSdba, $k->rp_m);
            $sheet2->setCellValue('H' . $kolomSdba, $k->rp_e);
            $sheet2->setCellValue('I' . $kolomSdba, $k->rp_sp);
            $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp);
            $kom1 =  round(($k->point_berhasil / $pointSdb) * $komSdb, 0);
            $sheet2->setCellValue('J' . $kolomSdba, $gaji);
            $sheet2->setCellValue('K' . $kolomSdba, $k->point == 'T' ? '0' : $kom1);
            $sheet2->setCellValue('L' . $kolomSdba, $gaji + $kom1);
            $sheet2->setCellValue('M' . $kolomSdba, $k->denda);
            $sheet2->setCellValue('N' . $kolomSdba, $k->kasbon);
            $sheet2->setCellValue('O' . $kolomSdba, ($gaji + $kom1) - $k->denda - $k->kasbon);
            $sheet2->setCellValue('P' . $kolomSdba, $k->point == 'Y' ? 'Ya' : 'Tidak');
            $sheet2->setCellValue('Q' . $kolomSdba, $tKerja->y . ' Tahun ' . $tKerja->m . ' Bulan');
            $kolomSdba++;
        }

        $batasAwala = $rowSdba + 1;
        $batasSdba = $kolomSdba - 1;

        $sheet2->getStyle('A' . $batasAwala . ':Q' . $batasSdba)->applyFromArray($styleSdb);

        // SUMMARY VARIABEL
        $loc = 1;
        $locSdb = 2;

        $total_gojekTkm = PointQuery::getTotalGojek($loc, $tgl1, $tgl2);

        $total_not_gojekTkm = PointQuery::getTotalNotGojek($loc, $tgl1, $tgl2);

        $jml_telatTkm = PointQuery::jml_telat($loc, $tgl1, $tgl2);

        $jml_telat20Tkm = PointQuery::jml_telat20($loc, $tgl1, $tgl2);

        $jml_ontimeTkm = PointQuery::jml_ontime($loc, $tgl1, $tgl2);

        $majoTkm = PointQuery::majo(1, $loc, $tgl1, $tgl2);

        $majo_gojekTkm = PointQuery::majo(2, $loc, $tgl1, $tgl2);

        $dpTkm = PointQuery::dp($loc, $tgl1, $tgl2);

        $transaksiTkm = PointQuery::transaksi($loc, $tgl1, $tgl2);

        $kategoriTkm = PointQuery::kategori($loc, $tgl1, $tgl2);

        $gojekTkm = PointQuery::gojek($loc, $tgl1, $tgl2);

        $voidTkm = PointQuery::void($loc, $tgl1, $tgl2);

        $pb1_gojekTkm = (($total_gojekTkm->total + $majo_gojekTkm->bayar_majo) * 0.8) / 11;

        $service_chargeTkm = $total_not_gojekTkm->total * 0.07;
        $pb1_not_gojekTkm = ($total_not_gojekTkm->total  + $service_chargeTkm) * 0.1;

        $total_totalTkm = $total_gojekTkm->total + $total_not_gojekTkm->total + $service_chargeTkm + $pb1_not_gojekTkm + $transaksiTkm->rounding;

        $total_transaksiTkm = $transaksiTkm->rp + $transaksiTkm->tax + $transaksiTkm->ser + $transaksiTkm->rounding - $transaksiTkm->dp;
        $kembalianTkm = $transaksiTkm->total_bayar - $total_transaksiTkm;
        $kuranganTkm = $transaksiTkm->tax + $transaksiTkm->ser + $transaksiTkm->rounding - $transaksiTkm->dp;

        $persenTkm = $jml_telatTkm->jml_telat > 0 ? round(($jml_telatTkm->jml_telat * 100) / ($jml_telatTkm->jml_telat + $jml_ontimeTkm->jml_ontime), 0) : 0;

        $persen20Tkm = $jml_telatTkm->jml_telat > 0 ? round(($jml_telat20Tkm->jml_telat * 100) / ($jml_telat20Tkm->jml_telat + $jml_ontimeTkm->jml_ontime), 0) : 0;

        $persenOntimeTkm = $jml_ontimeTkm->jml_ontime > 0 ? round(($jml_ontimeTkm->jml_ontime * 100) / ($jml_telatTkm->jml_telat + $jml_ontimeTkm->jml_ontime), 0) : 0;

        // variabel soondobu
        $total_gojekSdb = PointQuery::getTotalGojek($locSdb, $tgl1, $tgl2);

        $total_not_gojekSdb = PointQuery::getTotalNotGojek($locSdb, $tgl1, $tgl2);

        $jml_telatSdb = PointQuery::jml_telat($locSdb, $tgl1, $tgl2);

        $jml_telat20Sdb = PointQuery::jml_telat20($locSdb, $tgl1, $tgl2);

        $jml_ontimeSdb = PointQuery::jml_ontime($locSdb, $tgl1, $tgl2);

        $majoSdb = PointQuery::majo(1, $locSdb, $tgl1, $tgl2);

        $majo_gojekSdb = PointQuery::majo(2, $locSdb, $tgl1, $tgl2);

        $dpSdb = PointQuery::dp($locSdb, $tgl1, $tgl2);

        $transaksiSdb = PointQuery::transaksi($locSdb, $tgl1, $tgl2);

        $kategoriSdb = PointQuery::kategori($locSdb, $tgl1, $tgl2);

        $gojekSdb = PointQuery::gojek($locSdb, $tgl1, $tgl2);

        $voidSdb =  PointQuery::void($locSdb, $tgl1, $tgl2);

        $pb1_gojekSdb = (($total_gojekSdb->total + $majo_gojekSdb->bayar_majo) * 0.8) / 11;

        $service_chargeSdb = $total_not_gojekSdb->total * 0.07;
        $pb1_not_gojekSdb = ($total_not_gojekSdb->total  + $service_chargeSdb) * 0.1;

        $total_totalSdb = $total_gojekSdb->total + $total_not_gojekSdb->total + $service_chargeSdb + $pb1_not_gojekSdb + $transaksiSdb->rounding;

        $total_transaksiSdb = $transaksiSdb->rp + $transaksiSdb->tax + $transaksiSdb->ser + $transaksiSdb->rounding - $transaksiSdb->dp;
        $kembalianSdb = $transaksiSdb->total_bayar - $total_transaksiSdb;
        $kuranganSdb = $transaksiSdb->tax + $transaksiSdb->ser + $transaksiSdb->rounding - $transaksiSdb->dp;

        $persenSdb = $jml_telatSdb->jml_telat > 0 ? round(($jml_telatSdb->jml_telat * 100) / ($jml_telatSdb->jml_telat + $jml_ontimeSdb->jml_ontime), 0) : 0;

        $persen20Sdb = $jml_telatSdb->jml_telat > 0 ? round(($jml_telat20Sdb->jml_telat * 100) / ($jml_telat20Sdb->jml_telat + $jml_ontimeSdb->jml_ontime), 0) : 0;

        $persenOntimeSdb = $jml_ontimeSdb->jml_ontime > 0 ? round(($jml_ontimeSdb->jml_ontime * 100) / ($jml_telatSdb->jml_telat + $jml_ontimeSdb->jml_ontime), 0) : 0;
        // ----------------------------------------------------------
        // ---------------------------------------------------------------------------

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);

        $sheet3 = $spreadsheet->getActiveSheet();
        $sheet3->setTitle('Summary');

        $sheet3->getStyle('A1:F1')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        // lebar kolom
        $sheet3->getColumnDimension('A')->setWidth(33);
        $sheet3->getColumnDimension('B')->setWidth(20);
        $sheet3->getColumnDimension('C')->setWidth(12);
        $sheet3->getColumnDimension('D')->setWidth(17);
        $sheet3->getColumnDimension('E')->setWidth(33);
        $sheet3->getColumnDimension('F')->setWidth(22);
        $sheet3->getColumnDimension('G')->setWidth(17);
        $sheet3->getColumnDimension('H')->setWidth(21);
        $sheet3->getColumnDimension('J')->setWidth(17);

        $sheet3->mergeCells('A1:B1');
        $sheet3->getStyle("A9:A18")->getFont()->setBold(true);
        $sheet3->getStyle("A24:B24")->getFont()->setBold(true);
        $sheet3->getStyle("A1")->getFont()->setBold(true);
        $sheet3->getStyle("B9:B18")->getFont()->setBold(true);



        $sheet3->mergeCells('E1:F1');
        $sheet3->getStyle("E9:E18")->getFont()->setBold(true);
        $sheet3->getStyle("E24:F24")->getFont()->setBold(true);
        $sheet3->getStyle("E1")->getFont()->setBold(true);
        $sheet3->getStyle("F9:F18")->getFont()->setBold(true);

        $sheet3
            ->setCellValue('A1', 'TAKEMORI ' . date('M d, Y', strtotime($tgl1)) . ' - ' . date('M d, Y', strtotime($tgl2)))
            ->setCellValue('A2', 'Total Invoice')
            ->setCellValue('A3', 'Rp/Invoice')
            ->setCellValue('A4', 'Unit Food')
            ->setCellValue('A5', 'Rp/Unit')
            ->setCellValue('A6', 'Jumlah Pesanan Telat Masak 25 Menit')
            ->setCellValue('A7', 'Jumlah Pesanan Telat Masak 20 Menit')
            ->setCellValue('A8', 'Jumlah Pesanan Ontime Masak')
            ->setCellValue('A9', 'REVENUE : ')
            ->setCellValue('A10', 'Subtotal Dinein, take Away & Delivery')
            ->setCellValue('A11', 'Service Charge')
            ->setCellValue('A12', 'Pb1 Dinein, Takeaway & Delivery')
            ->setCellValue('A13', 'Subtotal STK')
            ->setCellValue('A14', 'Pb1 STK (subtotal stk / 11)')
            ->setCellValue('A15', 'Subtotal gojek')
            ->setCellValue('A16', 'pb1 gojek dine in & stk (80% dari subtotal / 11)')
            ->setCellValue('A17', 'Total Pb1')
            ->setCellValue('A18', 'Total Subtotal')
            ->setCellValue('A19', 'CASH')
            ->setCellValue('A20', 'BCA Debit')
            ->setCellValue('A21', 'BCA Kredit')
            ->setCellValue('A22', 'Mandiri Debit')
            ->setCellValue('A23', 'Mandiri Kredit')
            ->setCellValue('A24', 'Total Total')
            ->setCellValue('A25', 'Discount')
            ->setCellValue('A26', 'Voucher')
            ->setCellValue('A27', 'Rounding')
            ->setCellValue('A28', 'Dp')
            ->setCellValue('A29', 'Total Total + DP');

        // soondobu
        $sheet3
            ->setCellValue('E1', 'SOONDOBU ' . date('M d, Y', strtotime($tgl1)) . ' - ' . date('M d, Y', strtotime($tgl2)))
            ->setCellValue('E2', 'Total Invoice')
            ->setCellValue('E3', 'Rp/Invoice')
            ->setCellValue('E4', 'Unit Food')
            ->setCellValue('E5', 'Rp/Unit')
            ->setCellValue('E6', 'Jumlah Pesanan Telat Masak 25 Menit')
            ->setCellValue('E7', 'Jumlah Pesanan Telat Masak 20 Menit')
            ->setCellValue('E8', 'Jumlah Pesanan Ontime Masak')
            ->setCellValue('E9', 'REVENUE : ')
            ->setCellValue('E10', 'Subtotal Dinein, take Away & Delivery')
            ->setCellValue('E11', 'Service Charge')
            ->setCellValue('E12', 'Pb1 Dinein, Takeaway & Delivery')
            ->setCellValue('E13', 'Subtotal STK')
            ->setCellValue('E14', 'Pb1 STK (subtotal stk / 11)')
            ->setCellValue('E15', 'Subtotal gojek')
            ->setCellValue('E16', 'pb1 gojek dine in & stk (80% dari subtotal / 11)')
            ->setCellValue('E17', 'Total Pb1')
            ->setCellValue('E18', 'Total Subtotal')
            ->setCellValue('E19', 'CASH')
            ->setCellValue('E20', 'BCA Debit')
            ->setCellValue('E21', 'BCA Kredit')
            ->setCellValue('E22', 'Mandiri Debit')
            ->setCellValue('E23', 'Mandiri Kredit')
            ->setCellValue('E24', 'Total Total')
            ->setCellValue('E25', 'Discount')
            ->setCellValue('E26', 'Voucher')
            ->setCellValue('E27', 'Rounding')
            ->setCellValue('E28', 'Dp')
            ->setCellValue('E29', 'Total Total + DP');

        $sheet3
            ->setCellValue('B2', $transaksiTkm->ttl_invoice)
            ->setCellValue('B3', round($total_totalTkm / $transaksiTkm->ttl_invoice, 0))
            ->setCellValue('B4', $transaksiTkm->unit)
            ->setCellValue('B5', round($total_totalTkm / $transaksiTkm->unit, 0));

        $sheet3
            ->setCellValue('B6', $jml_telatTkm->jml_telat . ' / ' . $persenTkm . ' %')
            ->setCellValue('B7', $jml_telat20Tkm->jml_telat . ' / ' . $persen20Tkm . '%')
            ->setCellValue('B8', $jml_ontimeTkm->jml_ontime . ' / ' . $persenOntimeTkm . '%')
            ->setCellValue('B10', $total_not_gojekTkm->total)
            ->setCellValue('B11', $service_chargeTkm)
            ->setCellValue('B12', round($pb1_not_gojekTkm, 0))
            ->setCellValue('B13', round($majoTkm->bayar_majo, 0))
            ->setCellValue('B14', round($majoTkm->bayar_majo / 11, 0))
            ->setCellValue('B15', round(($total_gojekTkm->total + $majo_gojekTkm->bayar_majo), 0))
            ->setCellValue('B16', round($pb1_gojekTkm, 0))
            ->setCellValue('B17', round($pb1_gojekTkm + $pb1_not_gojekTkm + ($majoTkm->bayar_majo * 0.1), 0))
            ->setCellValue('B18', $total_not_gojekTkm->total + $majoTkm->bayar_majo + round($total_gojekTkm->total + $majo_gojekTkm->bayar_majo, 0) - $pb1_gojekTkm)
            ->setCellValue('B19', $transaksiTkm->cash)
            ->setCellValue('B20', $transaksiTkm->d_bca)
            ->setCellValue('B21', $transaksiTkm->k_bca)
            ->setCellValue('B22', $transaksiTkm->d_mandiri)
            ->setCellValue('B23', $transaksiTkm->k_mandiri)
            ->setCellValue('B24', $transaksiTkm->total_bayar)
            ->setCellValue('B25', $transaksiTkm->discount)
            ->setCellValue('B26', $transaksiTkm->voucher)
            ->setCellValue('B27', $transaksiTkm->rounding)
            ->setCellValue('B28', $transaksiTkm->dp)
            ->setCellValue('B29', $transaksiTkm->dp  + $transaksiTkm->total_bayar);

        // soondobu
        $sheet3
            ->setCellValue('F2', $transaksiSdb->ttl_invoice)
            ->setCellValue('F3', round($total_totalSdb / $transaksiSdb->ttl_invoice, 0))
            ->setCellValue('F4', $transaksiSdb->unit)
            ->setCellValue('F5', round($total_totalSdb / $transaksiSdb->unit, 0));

        $sheet3
            ->setCellValue('F6', $jml_telatSdb->jml_telat . ' / ' . $persenSdb . ' %')
            ->setCellValue('F7', $jml_telat20Sdb->jml_telat . ' / ' . $persen20Sdb . '%')
            ->setCellValue('F8', $jml_ontimeSdb->jml_ontime . ' / ' . $persenOntimeSdb . '%')
            ->setCellValue('F10', $total_not_gojekSdb->total)
            ->setCellValue('F11', $service_chargeSdb)
            ->setCellValue('F12', round($pb1_not_gojekSdb, 0))
            ->setCellValue('F13', round($majoSdb->bayar_majo, 0))
            ->setCellValue('F14', round($majoSdb->bayar_majo / 11, 0))
            ->setCellValue('F15', round(($total_gojekSdb->total + $majo_gojekSdb->bayar_majo), 0))
            ->setCellValue('F16', round($pb1_gojekSdb, 0))
            ->setCellValue('F17', round($pb1_gojekSdb + $pb1_not_gojekSdb + ($majoSdb->bayar_majo * 0.1), 0))
            ->setCellValue('F18', $total_not_gojekSdb->total + $majoSdb->bayar_majo + round($total_gojekSdb->total + $majo_gojekSdb->bayar_majo, 0) - $pb1_gojekSdb)
            ->setCellValue('F19', $transaksiSdb->cash)
            ->setCellValue('F20', $transaksiSdb->d_bca)
            ->setCellValue('F21', $transaksiSdb->k_bca)
            ->setCellValue('F22', $transaksiSdb->d_mandiri)
            ->setCellValue('F23', $transaksiSdb->k_mandiri)
            ->setCellValue('F24', $transaksiSdb->total_bayar)
            ->setCellValue('F25', $transaksiSdb->discount)
            ->setCellValue('F26', $transaksiSdb->voucher)
            ->setCellValue('F27', $transaksiSdb->rounding)
            ->setCellValue('F28', $transaksiSdb->dp)
            ->setCellValue('F29', $transaksiSdb->dp  + $transaksiSdb->total_bayar);
        $sheet3->getStyle('A2:B29')->applyFromArray($style);
        $sheet3->getStyle('E2:F29')->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet4 = $spreadsheet->getActiveSheet();
        $sheet4->setTitle('Data Laporan Penjualan Majo');

        $komisiTkm = Http::get("https://majoo-laravel.putrirembulan.com/api/laporan/takemori/$tgl1/$tgl2");
        $laporanTkm = $komisiTkm['laporan'];
        $komisiSdb = Http::get("https://majoo-laravel.putrirembulan.com/api/laporan/soondobu/$tgl1/$tgl2");
        $laporanSdb = $komisiSdb['laporan'];

        $sheet4
            ->setCellValue('A1', 'Takemori')
            ->setCellValue('B1', 'No')
            ->setCellValue('C1', 'Kategori')
            ->setCellValue('D1', 'Nama Produk')
            ->setCellValue('E1', 'Harga Satuan')
            ->setCellValue('F1', 'Qty')
            ->setCellValue('G1', 'Satuan')
            ->setCellValue('H1', 'Total')

            ->setCellValue('J1', 'Soondobu')
            ->setCellValue('K1', 'No')
            ->setCellValue('L1', 'Kategori')
            ->setCellValue('M1', 'Nama Produk')
            ->setCellValue('N1', 'Harga Satuan')
            ->setCellValue('O1', 'Qty')
            ->setCellValue('P1', 'Satuan')
            ->setCellValue('Q1', 'Total');

        $sheet4->getStyle("A1:H1")->getFont()->setBold(true);
        $sheet4->getStyle("J1:Q1")->getFont()->setBold(true);
        $kolap = 2;
        $laporanTtlTkm = 0;
        $laporanTtlSdb = 0;
        foreach ($laporanTkm as $no => $d) {
            $sheet4->setCellValue("B$kolap", $no + 1)
                ->setCellValue("C$kolap", $d['nm_kategori'])
                ->setCellValue("D$kolap", $d['nm_produk'])
                ->setCellValue("E$kolap", $d['harga'])
                ->setCellValue("F$kolap", $d['jlh'])
                ->setCellValue("G$kolap", $d['satuan'])
                ->setCellValue("H$kolap", $d['total']);
            $kolap++;
            $laporanTtlTkm += $d['total'];
        }
        $sheet4->setCellValue("G$kolap", 'TOTAL');
        $sheet4->setCellValue("H$kolap", $laporanTtlTkm);
        $sheet4->getStyle("G$kolap")->getFont()->setBold(true);
        $sheet4->getStyle("H$kolap")->getFont()->setBold(true);
        $kolapSdb = 2;
        foreach ($laporanSdb as $no => $d) {
            $sheet4->setCellValue("K$kolapSdb", $no + 1)
                ->setCellValue("L$kolapSdb", $d['nm_kategori'])
                ->setCellValue("M$kolapSdb", $d['nm_produk'])
                ->setCellValue("N$kolapSdb", $d['harga'])
                ->setCellValue("O$kolapSdb", $d['jlh'])
                ->setCellValue("P$kolapSdb", $d['satuan'])
                ->setCellValue("Q$kolapSdb", $d['total']);
            $kolapSdb++;
            $laporanTtlSdb += $d['total'];
        }
        $sheet4->setCellValue("P$kolapSdb", 'TOTAL');
        $sheet4->setCellValue("Q$kolapSdb", $laporanTtlSdb);
        $sheet4->getStyle("P$kolapSdb")->getFont()->setBold(true);
        $sheet4->getStyle("Q$kolapSdb")->getFont()->setBold(true);

        $batasLap = $kolap - 1;
        $sheet4->getStyle('B1:H' . $batasLap)->applyFromArray($style);

        $batasLapSdb = $kolapSdb - 1;
        $sheet4->getStyle('K1:Q' . $batasLapSdb)->applyFromArray($style);
        

        // mulai gaji server
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(4);
        $sheet5 = $spreadsheet->getActiveSheet();
        $sheet5->setTitle('Gaji Server');

        // start gaji server
        $persenBagi = DB::table('db_denda_kpi')->where('id', 3)->first()->rupiah;
        $settingOrang = DB::table('db_denda_kpi')->where('id', 1)->first()->rupiah;
        $id_lokasi_tkm = 1;
        $id_lokasi_sdb = 2;

        // START TKM ----------------------------------------------------------------------------
        $service = PointQuery::getService($id_lokasi_tkm, $tgl1, $tgl2);

        $jumlah_orang = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', 1)->first();
        $persen = DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', 1)->first();

        $server = PointQuery::getServer(1, $tgl1, $tgl2);

        $orang_real = 1;
        $ttl_kom = 0;
        $total_m = 0;
        $total_sp = 0;
        foreach ($server as $m) {
            // if ($m->point != 'Y') {
            //     continue;
            // } else {

            // }
            $orang =  $l++;
            $total_m += $m->qty_m + $m->qty_e;
            $total_sp += $m->qty_sp * 2;
            $ttl_kom += $m->komisi;
        }

        // kpi ------------------------------
        $l1 = 1;

        foreach ($server as $k) {
            $o = $l1++;
        }
        $orang1 = $o ?? 0;


        // end kpi ---------------------------

        $bagi_kom = $service->total;
        $service_charge = $service->total * 0.07;
        $kom =  round((((($service_charge  / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah)  * $orang));
        $komKpi1 = ((($service_charge / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah) * $orang1;



        $bagi_kom = $service->total;
        $service_charge = $service->total * 0.07;
        $kom =  round((((($service_charge  / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah)  * $orang));
        // end kom pi -----------------------

        // End TAKEMORI VAR--------------------------------------------

        // START SDB -------------------------------------------------
        $service_sdb = PointQuery::getService($id_lokasi_sdb, $tgl1, $tgl2);

        $jumlah_orang_sdb = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', 2)->first();
        $persen_sdb = DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', 2)->first();

        $server_sdb = PointQuery::getServer(2, $tgl1, $tgl2);

        $l_sdb = 1;
        $ttl_kom_sdb = 0;
        $total_m_sdb = 0;
        $total_sp_sdb = 0;
        foreach ($server_sdb as $m) {
            if ($m->point != 'Y') {
                continue;
            } else {

                $orang_sdb =  $l_sdb++;
                $total_m_sdb += $m->qty_m + $m->qty_e;
                $total_sp_sdb += $m->qty_sp * 2;
            }

            $ttl_kom_sdb += $m->komisi;
        }

        $l2 = 1;
        foreach ($server_sdb as $m) {
            $orang2 =  $l2++;
        }

        $bagi_kom_sdb = $service_sdb->total;
        $service_charge_sdb = $service_sdb->total * 0.07;
        $kom_sdb =  round((((($service_charge_sdb  / 7) * $persen->jumlah_persen) / $jumlah_orang_sdb->jumlah)  * $orang_sdb));
        $komKpi2 =  round((((($service_charge_sdb  / 7) * $persen->jumlah_persen) / $jumlah_orang_sdb->jumlah)  * $orang2));
        // END SDB VAR -------------------------------------------------


        $sheet5
            ->setCellValue('B1', "TAKEMORI")
            ->setCellValue('A2', 'No')
            ->setCellValue('B2', 'Lama Bekerja')
            ->setCellValue('C2', 'Nama')
            ->setCellValue('D2', 'M')
            ->setCellValue('E2', 'E')
            ->setCellValue('F2', 'SP')
            ->setCellValue('G2', 'Ttl hari')
            ->setCellValue('H2', 'GAJI HARIAN')
            ->setCellValue('I2', 'GAJI SP')
            ->setCellValue('J2', 'TOTAL GAJI')
            ->setCellValue('K2', 'KOM PENJUALAN')
            ->setCellValue('L2', 'KOM STK')
            ->setCellValue('M2', 'KOM MAJO')
            ->setCellValue('N2', 'TOTAL KOM & GAJI')
            ->setCellValue('O2', 'TIPS')
            ->setCellValue('P2', 'KASBON')
            ->setCellValue('Q2', 'DENDA')

            ->setCellValue('R2', 'SISA GAJI')
            ->setCellValue('S2', 'POINT')

            ->setCellValue('U1', 'Org P ')
            ->setCellValue('V1', $jumlah_orang->jumlah)
            ->setCellValue('U2', 'Org R ')
            ->setCellValue('V2', $orang)
            ->setCellValue('U4', 'Service charge P ')
            ->setCellValue('V4', ($service_charge / 7) * $persen->jumlah_persen)
            ->setCellValue('U5', 'Service charge R')
            ->setCellValue('V5', $kom);

        $kolom = 3;
        $i = 1;

        $ttlKomMajoTkmr = 0;
        foreach($server as $i => $k) {
            $komisiG = Http::get("https://majoo-laravel.putrirembulan.com/api/komisiGaji/1/$k->karyawan_majo/$tgl1/$tgl2");
            $komaj = empty($komisiG['komisi']) ? 0 : $komisiG['komisi'][0]['dt_komisi'];
            $ttlKomMajoTkmr += $komaj;
        }
        
        $no = 1;
        foreach ($server as  $k) {
            $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp);
            $komisiServer = $k->point != 'Y' ? 0 : round($k->kom, 0);

            $kom1 = $ttl_kom == '' ? '0' : ($kom / $bagi_kom) * $k->komisi;
            $absen_m = $k->qty_m + $k->qty_e;
            $absen_sp = $k->qty_sp * 2;

            // $komisiG = Http::get("https://majoo-laravel.putrirembulan.com/api/komisiGaji/1/$k->karyawan_majo/$tgl1/$tgl2");
            // $komaj = empty($komisiG['komisi']) ? 0 : $komisiG['komisi'][0]['dt_komisi'];
            $kom_penjualan = $k->point != 'Y' ? '0' : round(($kom / ($total_m + $total_sp)) * ($absen_m + $absen_sp), 0);
            $komaj = $k->point != 'Y' ? '0' : round(($ttlKomMajoTkmr / ($total_m + $total_sp)) * ($absen_m + $absen_sp), 0);
            // kom kpi
            $ttlRp = $komKpi1 * $persenBagi + $komKpi2 * $persenBagi;
            $pointR = $ttlRp / $settingOrang;
            $ttlPointRp = $pointR / 10;
            $komKpi = $k->point != 'Y' ? '0' : $pointR - $ttlPointRp * $k->ttl;
            $ttlKomGaji = $gaji + $kom_penjualan + $k->kom + $komaj;
            $totalKerja = new DateTime($k->tgl_masuk);
            $today = new DateTime();
            $tKerja = $today->diff($totalKerja);

            $sheet5->setCellValue('A' . $kolom, $no++);
            $sheet5->setCellValue('B' . $kolom, $tKerja->y . ' Tahun ' . $tKerja->m . ' Bulan');
            $sheet5->setCellValue('C' . $kolom, $k->nama);
            $sheet5->setCellValue('D' . $kolom, $k->qty_m);
            $sheet5->setCellValue('E' . $kolom, $k->qty_e);
            $sheet5->setCellValue('F' . $kolom, $k->qty_sp);
            $sheet5->setCellValue('G' . $kolom, $k->qty_sp + $k->qty_m + $k->qty_e);
            $sheet5->setCellValue('H' . $kolom, $k->rp_m);
            $sheet5->setCellValue('I' . $kolom, $k->rp_sp);
            $sheet5->setCellValue('J' . $kolom, $gaji);
            $sheet5->setCellValue('K' . $kolom, $kom_penjualan);
            $sheet5->setCellValue('L' . $kolom, round($k->kom, 0));
            $sheet5->setCellValue('M' . $kolom, $komaj);
            $sheet5->setCellValue('N' . $kolom, $ttlKomGaji);
            $sheet5->setCellValue('O' . $kolom, '');
            $sheet5->setCellValue('P' . $kolom, $k->kasbon);
            $sheet5->setCellValue('Q' . $kolom, $k->denda);
            $sheet5->setCellValue('R' . $kolom, round($gaji + $kom_penjualan + $k->kom + $komaj - $k->kasbon - $k->denda, 0) );
            $sheet5->setCellValue('S' . $kolom, $k->point);

            $kolom++;
        }
        // ----------------------------------------
        $batasA = count($server) + 2;
        $sheet5->getStyle('A2:S' . $batasA)->applyFromArray($styleSdb);

        // $rowSdba = $batasA + 2;
        // $rSdbas = $rowSdba + 1;
        // $rsSdba = $rowSdb + 2;
        // $rspSdba = $rsSdb + 2;
        // $rsrSdba = $rspSdb + 1;

        // $sheet5
        //     ->setCellValue('B' . $rowSdba, 'SOONDOBU')
        //     ->setCellValue('A' . $rSdbas, 'No')
        //     ->setCellValue('B' . $rSdbas, 'Lama Bekerja')
        //     ->setCellValue('C' . $rSdbas, 'Nama')
        //     ->setCellValue('D' . $rSdbas, 'M')
        //     ->setCellValue('E' . $rSdbas, 'E')
        //     ->setCellValue('F' . $rSdbas, 'SP')
        //     ->setCellValue('G' . $rSdbas, 'Ttl hari')
        //     ->setCellValue('H' . $rSdbas, 'Gaji Harian')
        //     ->setCellValue('I' . $rSdbas, 'Gaji SP')
        //     ->setCellValue('J' . $rSdbas, 'TOTAL GAJI')
        //     ->setCellValue('K' . $rSdbas, 'KOM PENJUALAN')
        //     ->setCellValue('L' . $rSdbas, 'KOM STK')
        //     ->setCellValue('M' . $rSdbas, 'KOM MAJO')
        //     ->setCellValue('N' . $rSdbas, 'TOTAL KOM & GAJI')
        //     ->setCellValue('O' . $rSdbas, 'TIPS')
        //     ->setCellValue('P' . $rSdbas, 'KASBON')
        //     ->setCellValue('Q' . $rSdbas, 'DENDA')
        //     ->setCellValue('R' . $rSdbas, 'SISA GAJI')

        //     ->setCellValue('U' . $rowSdba, 'Org P ')
        //     ->setCellValue('V' . $rowSdba, $jumlah_orang_sdb->jumlah)
        //     ->setCellValue('U' . $rSdbas, 'Org R ')
        //     ->setCellValue('V' . $rSdbas, $orang_sdb);
        // $scpSdb = $rSdbas + 2;
        // $scrSdb = $rSdbas + 3;
        // $sheet5
        //     ->setCellValue('U' . $scpSdb, 'Service charge P ')
        //     ->setCellValue('V' . $scpSdb, ($service_charge_sdb / 7) * $persen_sdb->jumlah_persen)
        //     ->setCellValue('U' . $scrSdb, 'Service charge R')
        //     ->setCellValue('V' . $scrSdb, $kom_sdb);

        // $kolomSdba = $rSdbas + 1;
        // $i = 1;
        // $ttlAbsenSdb = 0;

        // $ttlKomMajoSdb = 0;
        // foreach($server as $i => $k) {
        //     $komisiG = Http::get("https://majoo-laravel.putrirembulan.com/api/komisiGaji/2/$k->karyawan_majo/$tgl1/$tgl2");
        //     $komaj = empty($komisiG['komisi']) ? 0 : $komisiG['komisi'][0]['dt_komisi'];
        //     $ttlKomMajoSdb += $komaj;
        // }
        // foreach ($server_sdb as $k) {
        //     $komisiServer = $k->point != 'Y' ? 0 : round($k->kom, 0);
        //     $totalKerja = new DateTime($k->tgl_masuk);
        //     $today = new DateTime();
        //     $tKerja = $today->diff($totalKerja);
        //     $kom1 = $ttl_kom_sdb == '' ? '0' : ($kom_sdb / $bagi_kom_sdb) * $k->komisi;
        //     $absen_m_sdb = $k->qty_m + $k->qty_e;
        //     $absen_sp_sdb = $k->qty_sp * 2;
        //     // $komisiG = Http::get("https://majoo-laravel.putrirembulan.com/api/komisiGaji/2/$k->karyawan_majo/$tgl1/$tgl2");
        //     // $komaj = empty($komisiG['komisi']) ? 0 : $komisiG['komisi'][0]['dt_komisi'];
        //     $kom_penjualan_sdb = $k->point != 'Y' ? '0' : round(($kom_sdb / ($total_m_sdb + $total_sp_sdb)) * ($absen_m_sdb + $absen_sp_sdb), 0);
        //     $komaj = $k->point != 'Y' ? '0' : round(($ttlKomMajoTkmr / ($total_m_sdb + $total_sp_sdb)) * ($absen_m_sdb + $absen_sp_sdb), 0);
        //     // kom kpi
        //     $komKpi = $k->point != 'Y' ? '0' : $pointR - $ttlPointRp * $k->ttl;
        //     $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp);
        //     $ttlKomGajiSdb = $gaji + $kom_penjualan_sdb + $k->kom + $komaj;

        //     $sheet5->setCellValue('A' . $kolomSdba, $i++);
        //     $sheet5->setCellValue('B' . $kolomSdba, $tKerja->y . ' Tahun ' . $tKerja->m . ' Bulan');
        //     $sheet5->setCellValue('C' . $kolomSdba, $k->nama);
        //     $sheet5->setCellValue('D' . $kolomSdba, $k->qty_m);
        //     $sheet5->setCellValue('E' . $kolomSdba, $k->qty_e);
        //     $sheet5->setCellValue('F' . $kolomSdba, $k->qty_sp);
        //     $sheet5->setCellValue('G' . $kolomSdba, $k->qty_sp + $k->qty_m + $k->qty_e);
        //     $sheet5->setCellValue('H' . $kolomSdba, $k->rp_m);
        //     $sheet5->setCellValue('I' . $kolomSdba, $k->rp_sp);
        //     $sheet5->setCellValue('J' . $kolomSdba, $gaji);
        //     $sheet5->setCellValue('K' . $kolomSdba, $kom_penjualan_sdb);
        //     $sheet5->setCellValue('L' . $kolomSdba, round($k->kom, 0));
        //     $sheet5->setCellValue('M' . $kolomSdba, $komaj);
        //     $sheet5->setCellValue('N' . $kolomSdba, $ttlKomGajiSdb);
        //     $sheet5->setCellValue('O' . $kolomSdba, '');
        //     $sheet5->setCellValue('P' . $kolomSdba, $k->kasbon);
        //     $sheet5->setCellValue('Q' . $kolomSdba, $k->denda);
        //     $sheet5->setCellValue('R' . $kolomSdba, round($gaji + $kom_penjualan_sdb + $k->kom + $komaj - $k->kasbon - $k->denda, 0));

        //     $kolomSdba++;
        // }

        // $batasAwala = $rowSdba + 1;
        // $batasSdba = $kolomSdba - 1;

        // $sheet5->getStyle('A' . $batasAwala . ':R' . $batasSdba)->applyFromArray($styleSdb);


        // Start Denda

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(5);
        $sheet6 = $spreadsheet->getActiveSheet();
        $sheet6->setTitle('Denda');

        $denda_kitchen = DB::select("SELECT a.nama, a.alasan, a.nominal
        FROM tb_denda as a
        Left join tb_karyawan as b on b.nama = a.nama
        where b.id_status = '1' and a.tgl BETWEEN ' $tgl1' and ' $tgl2' and a.nominal != 0
        order by a.nama ASC");

        $denda_server = DB::select("SELECT a.nama, a.alasan, a.nominal
        FROM tb_denda as a
        Left join tb_karyawan as b on b.nama = a.nama
        where b.id_status = '2' and a.tgl BETWEEN ' $tgl1' and ' $tgl2' and a.nominal != 0
        order by a.nama ASC");

        $sheet6
            ->setCellValue('A1', 'Denda Kitchen')
            ->setCellValue('A2', 'Nama')
            ->setCellValue('B2', 'Alasan')
            ->setCellValue('C2', 'Nominal')

            ->setCellValue('E1', 'Denda Server')
            ->setCellValue('E2', 'Nama')
            ->setCellValue('F2', 'Alasan')
            ->setCellValue('G2', 'Nominal');

        $kolom_denda = 3;
        foreach ($denda_kitchen as $d) {
            $sheet6->setCellValue('A' . $kolom_denda, $d->nama);
            $sheet6->setCellValue('B' . $kolom_denda, $d->alasan);
            $sheet6->setCellValue('C' . $kolom_denda, $d->nominal);
            $kolom_denda++;
        }
        $batasD = count($denda_kitchen) + 2;
        $sheet6->getStyle('A2:C' . $batasD)->applyFromArray($styleSdb);

        $kolom_denda2 = 3;
        foreach ($denda_server as $s) {
            $sheet6->setCellValue('E' . $kolom_denda2, $s->nama);
            $sheet6->setCellValue('F' . $kolom_denda2, $s->alasan);
            $sheet6->setCellValue('G' . $kolom_denda2, $s->nominal);
            $kolom_denda2++;
        }
        $batasS = count($denda_server) + 2;
        $sheet6->getStyle('E2:G' . $batasS)->applyFromArray($styleSdb);


        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="POINT KITCHEN TS.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
