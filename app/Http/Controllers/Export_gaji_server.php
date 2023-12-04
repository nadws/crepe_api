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
use Server;
use PointQuery;

class Export_gaji_server extends Controller
{
    public function index(Request $r)
    {
        $id_lokasi = $r->id_lokasi ?? 1;

        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-d');
        $lokasi = $id_lokasi == 1 ? 'takemori' : 'soondobu';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Gaji Server');

        $gaji_server = Server::gaji_server($tgl1, $tgl2);
        $service_charge_tkm = PointQuery::getService(1, $tgl1, $tgl2);
        $service_charge_sdb = PointQuery::getService(2, $tgl1, $tgl2);
        $komstk = Server::komstk($tgl1, $tgl2);
        $komisiMajo = Http::get("https://majoo.ptagafood.com/api/kom_majo_server/$tgl1/$tgl2");
        $laporanMajo = $komisiMajo['komisi'];
        $jumlah_orang = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', 1)->first();
        $persen = DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', 1)->first();


        $total_jam = 0;
        $o = 1;
        foreach ($gaji_server as $s) {
            if ($s->point != 'Y') {
                continue;
            } else {
                $total_jam += (($s->m + $s->e) * 8) + ($s->sp * 13);
                $orang = $o++;
            }
        }

        $sheet->getStyle('A1:L1')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        // lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(33);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('M')->setWidth(12);
        $sheet->getColumnDimension('N')->setWidth(10);
        $sheet->getColumnDimension('O')->setWidth(12);
        $sheet->getColumnDimension('P')->setWidth(12);
        $sheet->getColumnDimension('Q')->setWidth(12);
        $sheet->getColumnDimension('R')->setWidth(12);
        $sheet->getColumnDimension('S')->setWidth(12);


        // header text
        // takemori
        $sheet->getStyle("A1:S1")->getFont()->setBold(true);
        $total_service = ((($service_charge_sdb->total * 0.07) / 7) * $persen->jumlah_persen) + ((($service_charge_tkm->total * 0.07) / 7) * $persen->jumlah_persen);
        $sheet->getStyle('U4:W4')->getFont()->setBold(true);
        $sheet->getStyle('U7:W7')->getFont()->setBold(true);

        $sheet
            ->setCellValue('A1', 'NO')
            ->setCellValue('B1', 'LAMA BEKERJA')
            ->setCellValue('C1', 'NAMA')
            ->setCellValue('D1', 'POSISI')
            ->setCellValue('E1', 'Y/T')
            ->setCellValue('F1', 'M')
            ->setCellValue('G1', 'E')
            ->setCellValue('H1', 'SP')
            ->setCellValue('I1', 'Gaji Harian')
            ->setCellValue('J1', 'Gaji SP')
            ->setCellValue('K1', 'TTL Hari')
            ->setCellValue('L1', 'TTL Jam')
            ->setCellValue('M1', 'TOTAL GAJI')
            ->setCellValue('N1', 'Komisi , STK dan Majo')
            ->setCellValue('O1', 'TOTAL KOM & GAJI')
            ->setCellValue('P1', 'TIPS')
            ->setCellValue('Q1', 'KASBON')
            ->setCellValue('R1', 'DENDA')
            ->setCellValue('S1', 'SISA GAJI')


            ->setCellValue('U1', 'SC TKM')
            ->setCellValue('W1', (($service_charge_tkm->total * 0.07) / 7) * $persen->jumlah_persen)
            ->setCellValue('U2', 'SC SDB')
            ->setCellValue('W2', (($service_charge_sdb->total * 0.07) / 7) * $persen->jumlah_persen)
            ->setCellValue('U4', 'Total SC')
            ->setCellValue('W4', $total_service)
            ->setCellValue('U5', 'P')
            ->setCellValue('W5', $jumlah_orang->jumlah)
            ->setCellValue('U6', 'R')
            ->setCellValue('W6', $orang)
            ->setCellValue('U7', 'Sc Dibagi')
            ->setCellValue('W7', ($total_service / $jumlah_orang->jumlah) * $orang);
        $sc_dibagi = ($total_service / $jumlah_orang->jumlah) * $orang;

        $col_majo = 8;
        $kom_majo = 0;
        foreach ($laporanMajo as $l) {
            $kom_majo += $l['komisi_bagi'];
            $sheet
                ->setCellValue('U' . $col_majo, $l['lokasi'] == 'SOONDOBU' ? 'MAJO SDB' : 'MAJO TKM')
                ->setCellValue('V' . $col_majo, $l['komisi'] . '%')
                ->setCellValue('W' . $col_majo, $l['total'] * ($l['komisi'] / 100));
            $col_majo++;
        }
        $sheet->getStyle('U' . $col_majo)->getFont()->setBold(true);
        $sheet->getStyle('W' . $col_majo)->getFont()->setBold(true);
        $sheet->setCellValue("U$col_majo", "MAJO TKM + SDB");
        $sheet->setCellValue("W$col_majo", $kom_majo);


        $col = $col_majo + 1;
        $kom_bagi = 0;
        foreach ($komstk as $k) {
            $kom_bagi += $k->komisi_bagi;
            $sheet->setCellValue('U' . $col, $k->lokasi == '2' ? 'STK SDB' : 'STK TKM');
            $sheet->setCellValue('V' . $col, $k->komisi . '%');
            $sheet->setCellValue('W' . $col, $k->total * ($k->komisi / 100));
            $col++;
        }
        $sheet->getStyle('U' . $col)->getFont()->setBold(true);
        $sheet->getStyle('W' . $col)->getFont()->setBold(true);
        $sheet
            ->setCellValue('U' . $col, 'STK TKM + SDB')
            ->setCellValue('W' . $col, $kom_bagi);

        $sheet->getStyle('U' . $col + 1)->getFont()->setBold(true);
        $sheet->getStyle('W' . $col + 1)->getFont()->setBold(true);
        $sheet
            ->setCellValue('U' . $col + 1, 'Total Komisi')
            ->setCellValue('W' . $col + 1, $sc_dibagi + $kom_majo + $kom_bagi);

        $sheet
            ->setCellValue('U' . $col + 2, 'Jam Dibagi')
            ->setCellValue('W' . $col + 2, $total_jam);

        $sheet->getStyle('U' . $col + 3)->getFont()->setBold(true);
        $sheet->getStyle('W' . $col + 3)->getFont()->setBold(true);
        $sheet
            ->setCellValue('U' . $col + 3, 'Kom/Jam')
            ->setCellValue('W' . $col + 3, ($sc_dibagi + $kom_majo + $kom_bagi) / $total_jam);
        $kom_jam = ($sc_dibagi + $kom_majo + $kom_bagi) / $total_jam;



        $i = 1;
        $kolom = 2;
        $ttl_gaji = 0;
        $ttl_kom = 0;
        $ttl_kasbon = 0;
        $ttl_denda = 0;
        foreach ($gaji_server as $g) {
            $point = $g->point == 'Y' ? '1' : '0';
            $ttl_gaji += ($g->rp_sp * $g->sp) +  (($g->m + $g->e) * $g->rp_e) + $g->g_bulanan;
            $ttl_kom += ((($g->m + $g->e) * 8) + ($g->sp * 13)) * $point * $kom_jam;
            $ttl_kasbon += $g->kasbon;
            $ttl_denda += $g->denda;

            $totalKerja = new DateTime($g->tgl_masuk);
            $today = new DateTime();
            $tKerja = $today->diff($totalKerja);
            $sheet->setCellValue('A' . $kolom, $i++);
            $sheet->setCellValue('B' . $kolom, $tKerja->y . ' Tahun ' . $tKerja->m . ' Bulan');
            $sheet->setCellValue('C' . $kolom, $g->nama);
            $sheet->setCellValue('D' . $kolom, $g->nm_posisi);
            $sheet->setCellValue('E' . $kolom, $g->point == 'Y' ? '1' : '0');

            $sheet->setCellValue('F' . $kolom, $g->m);
            $sheet->setCellValue('G' . $kolom, $g->e);
            $sheet->setCellValue('H' . $kolom, $g->sp);
            $sheet->setCellValue('I' . $kolom, $g->rp_e);
            $sheet->setCellValue('J' . $kolom, $g->rp_sp);
            $sheet->setCellValue('K' . $kolom, $g->m + $g->e + $g->sp);
            $sheet->setCellValue('L' . $kolom, (($g->m + $g->e) * 8) + ($g->sp * 13));
            $jam = (($g->m + $g->e) * 8) + ($g->sp * 13);
            // $sheet->setCellValue('K' . $kolom, (($g->m + $g->e) * $g->rp_e));
            // $sheet->setCellValue('L' . $kolom, $g->rp_sp * $g->sp );
            $sheet->setCellValue('M' . $kolom, ($g->rp_sp * $g->sp) +  (($g->m + $g->e) * $g->rp_e) + $g->g_bulanan);
            $gaji_h = ($g->rp_sp * $g->sp) +  (($g->m + $g->e) * $g->rp_e) + $g->g_bulanan;
            $sheet->setCellValue('N' . $kolom, $jam * $point * $kom_jam);
            $kom_ser = $jam * $point * $kom_jam;
            $sheet->setCellValue('O' . $kolom, $kom_ser + $gaji_h);
            $sheet->setCellValue('P' . $kolom, '');
            $sheet->setCellValue('Q' . $kolom, $g->kasbon);
            $sheet->setCellValue('R' . $kolom, $g->denda);
            $sheet->setCellValue('S' . $kolom, $kom_ser + $gaji_h - $g->kasbon - $g->denda);
            $kolom++;
        }
        $sheet->mergeCells('A' . $kolom . ':' . 'L' . $kolom);
        $sheet
            ->setCellValue('A' . $kolom, 'TOTAL')
            ->setCellValue('M' . $kolom, $ttl_gaji)
            ->setCellValue('N' . $kolom, $ttl_kom)
            ->setCellValue('O' . $kolom, $ttl_gaji + $ttl_kom)
            ->setCellValue('P' . $kolom, '')
            ->setCellValue('Q' . $kolom, $ttl_kasbon)
            ->setCellValue('R' . $kolom, $ttl_denda)
            ->setCellValue('S' . $kolom, $ttl_gaji + $ttl_kom - $ttl_kasbon - $ttl_denda);



        $writer = new Xlsx($spreadsheet);

        $style_header = array(
            'font' => array(
                'size' => 12,
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
        $sheet->getStyle('A1:S1')->applyFromArray($style_header);
        $sheet->getStyle('A' . $kolom . ':' . 'S' . $kolom)->applyFromArray($style_header);

        $style = [
            'font' => array(
                'size' => 12,
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
        $batas = $gaji_server;
        $batas = count($batas) + 1;
        $sheet->getStyle('A2:S' . $batas)->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2->setTitle('Penjualan STK');

        $stk_takemori = Server::penjualan_stk(1, $tgl1, $tgl2);
        $stk_sdb = Server::penjualan_stk(2, $tgl1, $tgl2);

        $sheet2
            ->setCellValue('A1', 'TAKEMORI')
            ->setCellValue('A2', 'NO')
            ->setCellValue('B2', 'Nama Produk')
            ->setCellValue('C2', 'Qty')
            ->setCellValue('D2', 'Persen Komisi')
            ->setCellValue('E2', 'Total Rp')
            ->setCellValue('F2', 'Komisi');

        $sheet2->getStyle('A2:F2')->applyFromArray($style_header);

        $i = 1;
        $kolom = 3;
        $total = 0;
        $total_kom = 0;
        foreach ($stk_takemori as $no => $s) {
            $total += $s->total;
            $total_kom += $s->total * ($s->komisi / 100);
            $sheet2->setCellValue('A' . $kolom, $i++);
            $sheet2->setCellValue('B' . $kolom, $s->nm_produk);
            $sheet2->setCellValue('C' . $kolom, $s->jumlah);
            $sheet2->setCellValue('D' . $kolom, $s->komisi . '%');
            $sheet2->setCellValue('E' . $kolom, $s->total);
            $sheet2->setCellValue('F' . $kolom, $s->total * ($s->komisi / 100));
            $kolom++;
        }
        $sheet2->mergeCells('A' . $kolom . ':' . 'D' . $kolom);
        $sheet2
            ->setCellValue('A' . $kolom, 'TOTAL')
            ->setCellValue('E' . $kolom, $total)
            ->setCellValue('F' . $kolom, $total_kom);

        $sheet2->getStyle('A3:F' . $kolom - 1)->applyFromArray($style);
        $sheet2->getStyle('A' . $kolom . ':' . 'F' . $kolom)->applyFromArray($style_header);

        $sheet2->getStyle('A' . $kolom + 3 . ':' . 'F' . $kolom + 3)->applyFromArray($style_header);
        $sheet2
            ->setCellValue('A' . $kolom + 2, 'SOONDOBU')
            ->setCellValue('A' . $kolom + 3, 'NO')
            ->setCellValue('B' . $kolom + 3, 'Nama Produk')
            ->setCellValue('C' . $kolom + 3, 'Qty')
            ->setCellValue('D' . $kolom + 3, 'Persen Komisi')
            ->setCellValue('E' . $kolom + 3, 'Total Rp')
            ->setCellValue('F' . $kolom + 3, 'Komisi');

        $i = 1;
        $kolom_sdb = $kolom + 4;
        $total_sdb = 0;
        $total_kom_sdb = 0;
        foreach ($stk_sdb as $no => $s) {
            $total_sdb += $s->total;
            $total_kom_sdb += $s->total * ($s->komisi / 100);
            $sheet2->setCellValue('A' . $kolom_sdb, $i++);
            $sheet2->setCellValue('B' . $kolom_sdb, $s->nm_produk);
            $sheet2->setCellValue('C' . $kolom_sdb, $s->jumlah);
            $sheet2->setCellValue('D' . $kolom_sdb, $s->komisi . '%');
            $sheet2->setCellValue('E' . $kolom_sdb, $s->total);
            $sheet2->setCellValue('F' . $kolom_sdb, $s->total * ($s->komisi / 100));
            $kolom_sdb++;
        }

        $sheet2->mergeCells('A' . $kolom_sdb . ':' . 'D' . $kolom_sdb);
        $sheet2
            ->setCellValue('A' . $kolom_sdb, 'TOTAL')
            ->setCellValue('E' . $kolom_sdb, $total_sdb)
            ->setCellValue('F' . $kolom_sdb, $total_kom_sdb);

        $sheet2->getStyle('A' . $kolom + 4 . ':' . 'F' . $kolom_sdb - 1)->applyFromArray($style);
        $sheet2->getStyle('A' . $kolom_sdb . ':' . 'F' . $kolom_sdb)->applyFromArray($style_header);

        // MAJOO
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet();
        $sheet3->setTitle('Penjualan MAJO Bagi Komisi');

        $majo_takemori =  Http::get("https://majoo.ptagafood.com/api/penjualn_server/TAKEMORI/$tgl1/$tgl2");
        $majo_tkmr = $majo_takemori['komisi'];
        $majo_soondobu =  Http::get("https://majoo.ptagafood.com/api/penjualn_server/SOONDOBU/$tgl1/$tgl2");
        $majo_sdb = $majo_soondobu['komisi'];

        $sheet3
            ->setCellValue('A1', 'TAKEMORI')
            ->setCellValue('A2', 'NO')
            ->setCellValue('B2', 'Nama Produk')
            ->setCellValue('C2', 'Qty')
            ->setCellValue('D2', 'Persen Komisi')
            ->setCellValue('E2', 'Total Rp')
            ->setCellValue('F2', 'Komisi');

        $sheet3->getStyle('A2:F2')->applyFromArray($style_header);

        $i = 1;
        $kolom = 3;
        $total = 0;
        $total_kom = 0;
        foreach ($majo_tkmr as $no => $s) {
            $total += $s['total'];
            $total_kom += $s['total'] * ($s['komisi'] / 100);
            $sheet3->setCellValue('A' . $kolom, $i++);
            $sheet3->setCellValue('B' . $kolom, $s['nm_produk']);
            $sheet3->setCellValue('C' . $kolom, $s['jumlah']);
            $sheet3->setCellValue('D' . $kolom, $s['komisi'] . '%');
            $sheet3->setCellValue('E' . $kolom, $s['total']);
            $sheet3->setCellValue('F' . $kolom, $s['total'] * ($s['komisi'] / 100));
            $kolom++;
        }
        $sheet3->mergeCells('A' . $kolom . ':' . 'D' . $kolom);
        $sheet3
            ->setCellValue('A' . $kolom, 'TOTAL')
            ->setCellValue('E' . $kolom, $total)
            ->setCellValue('F' . $kolom, $total_kom);

        $sheet3->getStyle('A3:F' . $kolom - 1)->applyFromArray($style);
        $sheet3->getStyle('A' . $kolom . ':' . 'F' . $kolom)->applyFromArray($style_header);

        $sheet3->getStyle('A' . $kolom + 3 . ':' . 'F' . $kolom + 3)->applyFromArray($style_header);
        $sheet3
            ->setCellValue('A' . $kolom + 2, 'SOONDOBU')
            ->setCellValue('A' . $kolom + 3, 'NO')
            ->setCellValue('B' . $kolom + 3, 'Nama Produk')
            ->setCellValue('C' . $kolom + 3, 'Qty')
            ->setCellValue('D' . $kolom + 3, 'Persen Komisi')
            ->setCellValue('E' . $kolom + 3, 'Total Rp')
            ->setCellValue('F' . $kolom + 3, 'Komisi');

        $i = 1;
        $kolom_sdb = $kolom + 4;
        $total_sdb = 0;
        $total_kom_sdb = 0;
        foreach ($majo_sdb as $no => $s) {
            $total_sdb += $s['total'];
            $total_kom_sdb += $s['total'] * ($s['komisi'] / 100);
            $sheet3->setCellValue('A' . $kolom_sdb, $i++);
            $sheet3->setCellValue('B' . $kolom_sdb, $s['nm_produk']);
            $sheet3->setCellValue('C' . $kolom_sdb, $s['jumlah']);
            $sheet3->setCellValue('D' . $kolom_sdb, $s['komisi'] . '%');
            $sheet3->setCellValue('E' . $kolom_sdb, $s['total']);
            $sheet3->setCellValue('F' . $kolom_sdb, $s['total'] * ($s['komisi'] / 100));
            $kolom_sdb++;
        }

        $sheet3->mergeCells('A' . $kolom_sdb . ':' . 'D' . $kolom_sdb);
        $sheet3
            ->setCellValue('A' . $kolom_sdb, 'TOTAL')
            ->setCellValue('E' . $kolom_sdb, $total_sdb)
            ->setCellValue('F' . $kolom_sdb, $total_kom_sdb);

        $sheet3->getStyle('A' . $kolom + 4 . ':' . 'F' . $kolom_sdb - 1)->applyFromArray($style);
        $sheet3->getStyle('A' . $kolom_sdb . ':' . 'F' . $kolom_sdb)->applyFromArray($style_header);



        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="GAJI SERVER TS.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
