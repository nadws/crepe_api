<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Arr;

class Kom_server extends Controller
{
    public function index(Request $r)
    {
        $id_user = Auth::user()->id;
        $arr = [97,1,2,3,4];
        if (in_array($id_user, $arr)) {
            return back();
        } else {
            $id_lokasi = $r->id_lokasi;
            if (empty($r->tgl1)) {
                $tgl1 = date('Y-m-01');
                $tgl2 = date('Y-m-d');
            } else {
                $tgl1 = $r->tgl1;
                $tgl2 = $r->tgl2;
            }



            $total_not_gojek = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
            LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
            WHERE tb_transaksi.id_lokasi = '$id_lokasi' and  dt_order.id_distribusi != '2' AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

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
                    WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2' and c.id_lokasi = '$id_lokasi' and c.status != 'OFF'
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
                'title' => 'Kom Server',
                'server' => $server,
                'tgl1' => $tgl1,
                'tgl2' => $tgl2,
                'service' => $total_not_gojek,
                'jumlah_orang' => DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', $id_lokasi)->first(),
                'persen' => DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', $id_lokasi)->first(),
                'logout' => $r->session()->get('logout'),
            ];

            return view('kom_server.kom_server', $data);
        }
    }
    public function kom_serve(Request $r)
    {
        
        $id_user = Auth::user()->id;
        $arr = [97,1,2,3,4];
        if (!in_array($id_user, $arr)) {
            return back();
        } else {
            if (empty($r->id_lokasi)) {
                $id_lokasi = '1';
            } else {
                $id_lokasi = $r->id_lokasi;
            }
             

            if (empty($r->tgl1)) {
                $tgl1 = date('Y-m-01');
                $tgl2 = date('Y-m-d');
            } else {
                $tgl1 = $r->tgl1;
                $tgl2 = $r->tgl2;
            }
 
            // $total_not_gojek = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
            // LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
            // WHERE tb_transaksi.id_lokasi = '$id_lokasi' and  dt_order.id_distribusi != '2' AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");
            
            $total_not_gojek = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE id_lokasi = '$id_lokasi' AND dt_order.id_distribusi != 2 AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

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
                    WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2' and c.id_lokasi = '$id_lokasi' and c.status != 'OFF'
                    GROUP BY c.id_karyawan, c.status
                    ) AS l ON l.id_karyawan = a.id_karyawan

            LEFT JOIN (
            SELECT a.admin, SUM(if(a.voucher = 'Y' ,0, a.hrg )) AS komisi
            FROM view_summary_server AS a
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$id_lokasi'
            GROUP BY a.admin
            ) AS b ON b.admin = a.nama

                    WHERE  a.tgl_masuk <= '$tgl2' and l.id_lokasi ='$id_lokasi' and a.id_status ='2' and a.point = 'Y'
                    group by a.id_karyawan
                


                        
        ");
            $server2 = DB::select("SELECT a.nama, b.rp_m, sum(l.qty_m) AS qty_m, sum(l.qty_e) AS qty_e, sum(l.qty_sp) AS qty_sp, b.rp_e, b.rp_sp, b.komisi
            FROM tb_karyawan AS a
            left join tb_gaji AS b ON b.id_karyawan = a.id_karyawan
            LEFT JOIN (
                   SELECT c.id_karyawan,  c.status, c.id_lokasi,
                    if(c.status = 'M', COUNT(c.status), 0) AS qty_m,
                    if(c.status = 'E', COUNT(c.status), 0) AS qty_e,
                    if(c.status = 'SP', COUNT(c.status), 0) AS qty_sp,
                    if(c.status = 'OFF', COUNT(c.status), 0) AS qty_off
                    FROM tb_absen AS c 
                    WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2' and c.id_lokasi = '$id_lokasi' and c.status != 'OFF'
                    GROUP BY c.id_karyawan, c.status
                    ) AS l ON l.id_karyawan = a.id_karyawan

            LEFT JOIN (
            SELECT a.admin, SUM(if(a.voucher = 'Y' ,0, a.hrg )) AS komisi
            FROM view_summary_server AS a
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$id_lokasi'
            GROUP BY a.admin
            ) AS b ON b.admin = a.nama

                    WHERE  a.tgl_masuk <= '$tgl2' and l.id_lokasi ='$id_lokasi' and a.id_status ='2' 
                    group by a.id_karyawan
                


                        
        ");
        $lokasiApi = $id_lokasi == 1 ? 'takemori' : 'soondobu';
        
       
        $komisi = Http::get("https://majoo.ptagafood.com/api/komisi/$lokasiApi/$tgl1/$tgl2");
       
    
        // $kom = $komisi['komisi'];
       
        // $dt_rules = $komisi['dt_rules'];
        // $rules_active = $komisi['rules_active'];
        // $total_penjualan = $komisi['total_penjualan'];
        // $komisi_resto = $komisi['komisi_resto'];

            $data = [
                'title' => 'Kom Server',
                'server' => $server,
                'server2' => $server2,
                'komisi' => '0',
                // 'dt_rules' => $dt_rules,
                // 'rules_active' => $rules_active,
                // 'total_penjualan' => $total_penjualan,
                // 'komisi_resto' => $komisi_resto,
                'tgl1' => $tgl1,
                'tgl2' => $tgl2,
                'service' => $total_not_gojek,
                'jumlah_orang' => DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', $id_lokasi)->first(),
                'persen' => DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', $id_lokasi)->first(),
                'logout' => $r->session()->get('logout'),
                'id_lokasi' => $id_lokasi
            ];

            return view('kom_server.kom_serve', $data);
        }
    }

    public function komisi_server(Request $r)
    {
        $id_lokasi = $r->id_lokasi;
        if (empty($r->tgl1)) {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-d');
        } else {
            $tgl1 = $r->tgl1;
            $tgl2 = $r->tgl2;
        }

        $service = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE tb_transaksi.id_lokasi = '$id_lokasi' and  dt_order.id_distribusi != '2' AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $jumlah_orang = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', $id_lokasi)->first();
        $persen = DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', $id_lokasi)->first();

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
                    WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2' and c.id_lokasi = '$id_lokasi' and c.status != 'OFF'
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

        $l = 1;
        $ttl_kom = 0;
        foreach ($server as $m) {
            $orang = $l++;
            $ttl_kom += $m->komisi;
        }

        $service_charge = $service->total * 0.07;
        $kom =  (((($service_charge  / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah)  * $orang);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Kom Server');

        $sheet->getStyle('A1:F1')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        // lebar kolom
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(17);
        $sheet->getColumnDimension('E')->setWidth(17);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->getColumnDimension('H')->setWidth(21);
        $sheet->getColumnDimension('J')->setWidth(17);
        // header text
        $sheet
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'NAMA')
            ->setCellValue('C1', 'TOTAL PENJUALAN')
            ->setCellValue('D1', 'KOM')
            ->setCellValue('F1', 'Org P ')
            ->setCellValue('G1', $jumlah_orang->jumlah)
            ->setCellValue('F2', 'Org R ')
            ->setCellValue('G2', $orang)
            ->setCellValue('F4', 'Service charge P ')
            ->setCellValue('G4', ($service_charge / 7) * $persen->jumlah_persen)
            ->setCellValue('F5', 'Service charge R')
            ->setCellValue('G5', $kom);
        $kolom = 2;



        $i = 1;
        foreach ($server as $k) {
            $sheet->setCellValue('A' . $kolom, $i++);
            $sheet->setCellValue('B' . $kolom, $k->nama);
            $sheet->setCellValue('C' . $kolom, $k->komisi);
            $kom1 = $ttl_kom == '' ? '0' : ($kom / $ttl_kom) * $k->komisi;
            $sheet->setCellValue('D' . $kolom, round($kom1, 0));
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
        $batas = $server;
        $batas = count($batas) + 1;
        $sheet->getStyle('A1:D' . $batas)->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);

        $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2->setTitle('Absensi');

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
            ->setCellValue('F1', 'Gaji');

        $kolom = 2;
        $i = 1;
        foreach ($server as $k) {
            $spreadsheet->setActiveSheetIndex(1);
            $sheet2->setCellValue('A' . $kolom, $i++);
            $sheet2->setCellValue('B' . $kolom, $k->nama);
            $sheet2->setCellValue('C' . $kolom, $k->qty_m);
            $sheet2->setCellValue('D' . $kolom, $k->qty_e);
            $sheet2->setCellValue('E' . $kolom, $k->qty_sp);
            $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp);
            $sheet2->setCellValue('F' . $kolom, $gaji);
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
        $batas = $server;
        $batas = count($batas) + 1;
        $sheet2->getStyle('A1:F' . $batas)->applyFromArray($style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Kom-server.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    public function komisi_server_export(Request $r)
    {

        if (empty($r->id_lokasi)) {
            $id_lokasi = '2';
        } else {
            $id_lokasi = $r->id_lokasi;
        }
        
        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-d');
        

        $lokasiApi = $id_lokasi == 1 ? 'takemori' : 'soondobu';
        // $komisi = Http::get("https://majoo.ptagafood.com/api/komisi/$lokasiApi/$tgl1/$tgl2");
        
        // $komisiMajoo = $komisi['komisi'];
        // $dt_rules = $komisi['dt_rules'];
        // $rules_active = $komisi['rules_active'];
        // $total_penjualan = $komisi['total_penjualan'];
        // $komisi_resto = $komisi['komisi_resto'];

        $service = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE tb_transaksi.id_lokasi = '$id_lokasi' and  dt_order.id_distribusi != '2' AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $jumlah_orang = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', $id_lokasi)->first();
        $persen = DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', $id_lokasi)->first();

        $server = DB::select("SELECT a.nama, b.rp_m, sum(l.qty_m) AS qty_m, sum(l.qty_e) AS qty_e, sum(l.qty_sp) AS qty_sp, 
        b.rp_e, b.rp_sp, b.komisi, c.kom,c.nm_karyawan as karyawan_majo
                    FROM tb_karyawan AS a
                    left join tb_gaji AS b ON b.id_karyawan = a.id_karyawan
                    LEFT JOIN (
                           SELECT c.id_karyawan,  c.status, c.id_lokasi,
                            if(c.status = 'M', COUNT(c.status), 0) AS qty_m,
                            if(c.status = 'E', COUNT(c.status), 0) AS qty_e,
                            if(c.status = 'SP', COUNT(c.status), 0) AS qty_sp,
                            if(c.status = 'OFF', COUNT(c.status), 0) AS qty_off
                            FROM tb_absen AS c 
                            WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2' and  c.id_lokasi = '$id_lokasi' and c.status != 'OFF'
                            GROUP BY c.id_karyawan, c.status
                            ) AS l ON l.id_karyawan = a.id_karyawan
        
                                LEFT JOIN (
                                SELECT a.admin, SUM(if(a.voucher = 'Y' ,0, a.hrg )) AS komisi
                                FROM view_summary_server AS a
                                WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$id_lokasi'
                                GROUP BY a.admin
                                ) AS b ON b.admin = a.nama
                                
                                LEFT JOIN (SELECT SUM(a.komisi) AS kom, b.id_karyawan,b.nm_karyawan
                                    FROM komisi AS a LEFT JOIN tb_karyawan_majo AS b ON b.kd_karyawan = a.id_kry
                                    WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$id_lokasi'
                                    GROUP BY a.id_kry
                                    ) AS c ON c.id_karyawan = a.id_karyawan
        
                            WHERE  a.tgl_masuk <= '$tgl2' and l.id_lokasi ='$id_lokasi' and a.id_status ='2' and a.point = 'Y'
                            group by a.id_karyawan
                      
        ");

        $l = 1;
        $ttl_kom = 0;
        foreach ($server as $m) {
            $orang =  $l++;
            $ttl_kom += $m->komisi;
        }
        
        $bagi_kom = $service->total;
        $service_charge = $service->total * 0.07;
        $kom =  round((((($service_charge  / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah)  * $orang));

         $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Kom Server');

        $sheet->getStyle('A1:F1')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        // lebar kolom
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(4);
        $sheet->getColumnDimension('D')->setWidth(4);
        $sheet->getColumnDimension('E')->setWidth(4);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(11);
        $sheet->getColumnDimension('I')->setWidth(5);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(13);
        // header text
        $sheet
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama')
            ->setCellValue('C1', 'M')
            ->setCellValue('D1', 'E')
            ->setCellValue('E1', 'SP')
            ->setCellValue('F1', 'Gaji')
            // ->setCellValue('F1', 'TOTAL PENJUALAN')
            ->setCellValue('G1', 'KOM PENJUALAN')
            ->setCellValue('H1', 'KOM STK')
            ->setCellValue('I1', 'KOM MAJO')
            ->setCellValue('J1', 'TIPS')
            ->setCellValue('K1', 'GAJI')
            ->setCellValue('L1', 'TOTAL GAJI')
            ->setCellValue('N1', 'Org P ')
            ->setCellValue('O1', $jumlah_orang->jumlah)
            ->setCellValue('N2', 'Org R ')
            ->setCellValue('O2', $orang)
            ->setCellValue('N4', 'Service charge P ')
            ->setCellValue('O4', ($service_charge / 7) * $persen->jumlah_persen)
            ->setCellValue('N5', 'Service charge R')
            ->setCellValue('O5', $kom);
        $kolom = 2;


        $i = 1;
        foreach ($server as $k) {
            $sheet->setCellValue('A' . $kolom, $i++);
            $sheet->setCellValue('B' . $kolom, $k->nama);
            $sheet->setCellValue('C' . $kolom, $k->qty_m);
            $sheet->setCellValue('D' . $kolom, $k->qty_e);
            $sheet->setCellValue('E' . $kolom, $k->qty_sp);
            $sheet->setCellValue('F' . $kolom, $k->rp_m);
            // $sheet->setCellValue('F' . $kolom, $k->komisi);
            $kom1 = $ttl_kom == '' ? '0' : ($kom / $bagi_kom) * $k->komisi;
            $sheet->setCellValue('G' . $kolom, round($kom1, 0));
            $sheet->setCellValue('H' . $kolom, round($k->kom, 0));
            
            $komisiG = Http::get("https://majoo.ptagafood.com/api/komisiGaji/$id_lokasi/$k->karyawan_majo/$tgl1/$tgl2");
            $komaj = empty($komisiG['komisi']) ? 0 : $komisiG['komisi'][0]['dt_komisi'];

            $sheet->setCellValue('I' . $kolom, $komaj);
            $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp);
            $sheet->setCellValue('K' . $kolom, $gaji);

            $sheet->setCellValue('L' . $kolom, $gaji + $kom1 + $k->kom + $komaj);

            $kolom++;
        }


        $writer = new Xlsx($spreadsheet);

        $style_header = array(
            'font' => array(
                'size' => 10,
                'bold'  =>  true
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ),
        );
        $style = [
            'font' => array(
                'size' => 10,
            ),
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
        $batas = $server;
        $batas = count($batas) + 1;
        $sheet->getStyle('A2:L' . $batas)->applyFromArray($style);
        $sheet->getStyle('A1:L1')->applyFromArray($style_header);
        
        
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
 

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $fileName = $id_lokasi == 1 ? 'Takemori' : 'Soondobu';
        header('Content-Disposition: attachment;filename="Kom-server '.$fileName.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
