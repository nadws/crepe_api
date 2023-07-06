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
        $service_charge_tkm = PointQuery::getService(1,$tgl1, $tgl2);
        $service_charge_sdb = PointQuery::getService(2,$tgl1, $tgl2);
        $komstk = Server::komstk($tgl1, $tgl2);
        $komisiMajo = Http::get("https://majoo-laravel.putrirembulan.com/api/kom_majo_server/$tgl1/$tgl2");
        $laporanMajo = $komisiMajo['komisi'];
        $jumlah_orang = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', 1)->first();
        $persen = DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', 1)->first();
        

        $total_jam=0;
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
        $sheet->getColumnDimension('F')->setWidth(11);
        $sheet->getColumnDimension('G')->setWidth(11);
        $sheet->getColumnDimension('H')->setWidth(11);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(18);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(48);
        $sheet->getColumnDimension('O')->setWidth(28);
        $sheet->getColumnDimension('P')->setWidth(22);
        $sheet->getColumnDimension('Q')->setWidth(22);
        $sheet->getColumnDimension('R')->setWidth(22);
        $sheet->getColumnDimension('S')->setWidth(22);

        
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
            ->setCellValue('I1', 'TTL Hari')
            ->setCellValue('J1', 'TTL Jam')
            ->setCellValue('K1', 'TOTAL GAJI')
            ->setCellValue('L1', 'Penjualan , STK dan Majo')
            ->setCellValue('M1', 'TOTAL KOM & GAJI')
            ->setCellValue('N1', 'TIPS')
            ->setCellValue('O1', 'KASBON')
            ->setCellValue('P1', 'DENDA')
            ->setCellValue('Q1', 'SISA GAJI')


            ->setCellValue('S1', 'SC TKM')
            ->setCellValue('U1', (($service_charge_tkm->total * 0.07) / 7) * $persen->jumlah_persen)
            ->setCellValue('S2', 'SC SDB')
            ->setCellValue('U2', (($service_charge_sdb->total * 0.07) / 7) * $persen->jumlah_persen)
            ->setCellValue('S4', 'Total SC')
            ->setCellValue('U4', $total_service)
            ->setCellValue('S5', 'P')
            ->setCellValue('U5', $jumlah_orang->jumlah)
            ->setCellValue('S6', 'R')
            ->setCellValue('U6', $orang)
            ->setCellValue('S7', 'Sc Dibagi')
            ->setCellValue('U7', ($total_service / $jumlah_orang->jumlah) * $orang);
            $sc_dibagi = ($total_service / $jumlah_orang->jumlah) * $orang;

            $col_majo = 8;
            $kom_majo = 0;
            foreach ($laporanMajo as $l) {
                $kom_majo += $l['komisi_bagi'];
                $sheet
                ->setCellValue('S'.$col_majo, $l['lokasi'] == 'SOONDOBU'? 'STK SDB' : 'STK TKM')
                ->setCellValue('T'.$col_majo, $l['komisi'] . '%')
                ->setCellValue('U'.$col_majo, $l['total'] * ($l['komisi'] / 100));
                $col_majo++;
            }
            $sheet->getStyle('S'. $col_majo)->getFont()->setBold(true);
            $sheet->getStyle('U'. $col_majo)->getFont()->setBold(true);
            $sheet
            ->setCellValue('S'.$col_majo, 'MAJO TKM + SDB')
            ->setCellValue('U'.$col_majo, $kom_majo); 
            

            $col = 11;
            $kom_bagi = 0;
            foreach ($komstk as $k) {
                $kom_bagi += $k->komisi_bagi;
                $sheet->setCellValue('S'. $col, $k->lokasi == '2'? 'STK SDB' : 'STK TKM');
                $sheet->setCellValue('T'. $col, $k->komisi . '%');
                $sheet ->setCellValue('U'. $col, $k->total*($k->komisi / 100));
            $col++;
            }
            $sheet->getStyle('S'. $col)->getFont()->setBold(true);
            $sheet->getStyle('U'. $col)->getFont()->setBold(true);
            $sheet
            ->setCellValue('S'.$col, 'STK TKM + SDB')
            ->setCellValue('U'.$col, $kom_bagi); 

            $sheet->getStyle('S'. $col+1)->getFont()->setBold(true);
            $sheet->getStyle('U'. $col+1)->getFont()->setBold(true);
            $sheet
            ->setCellValue('S'.$col+1, 'Total Komisi')
            ->setCellValue('U'.$col+1, $sc_dibagi+$kom_majo+$kom_bagi);

            $sheet
            ->setCellValue('S'.$col+2, 'Jam Dibagi')
            ->setCellValue('U'.$col+2, $total_jam);

            $sheet->getStyle('S'. $col+3)->getFont()->setBold(true);
            $sheet->getStyle('U'. $col+3)->getFont()->setBold(true);
            $sheet
            ->setCellValue('S'.$col+3, 'Kom/Jam')
            ->setCellValue('U'.$col+3, ($sc_dibagi+$kom_majo+$kom_bagi) / $total_jam);
            $kom_jam = ($sc_dibagi+$kom_majo+$kom_bagi) / $total_jam;

            

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
            $sheet->setCellValue('I' . $kolom, $g->m + $g->e + $g->sp);
            $sheet->setCellValue('J' . $kolom, (($g->m + $g->e) * 8) + ($g->sp * 13));
            $jam = (($g->m + $g->e) * 8) + ($g->sp * 13);
            // $sheet->setCellValue('K' . $kolom, (($g->m + $g->e) * $g->rp_e));
            // $sheet->setCellValue('L' . $kolom, $g->rp_sp * $g->sp );
            $sheet->setCellValue('K' . $kolom, ($g->rp_sp * $g->sp) +  (($g->m + $g->e) * $g->rp_e) + $g->g_bulanan);
            $gaji_h = ($g->rp_sp * $g->sp) +  (($g->m + $g->e) * $g->rp_e) + $g->g_bulanan;
            $sheet->setCellValue('L' . $kolom, $jam * $point * $kom_jam);
            $kom_ser = $jam * $point * $kom_jam;
            $sheet->setCellValue('M' . $kolom, $kom_ser + $gaji_h);
            $sheet->setCellValue('N' . $kolom, '');
            $sheet->setCellValue('O' . $kolom, $g->kasbon);
            $sheet->setCellValue('P' . $kolom, $g->denda);
            $sheet->setCellValue('Q' . $kolom, $kom_ser + $gaji_h - $g->kasbon - $g->denda);
            $kolom++;
        }
        $sheet->mergeCells('A' . $kolom .':'. 'J'.$kolom);
        $sheet
        ->setCellValue('A' . $kolom, 'TOTAL')
        ->setCellValue('K' . $kolom, $ttl_gaji)
        ->setCellValue('L' . $kolom, $ttl_kom)
        ->setCellValue('M' . $kolom, $ttl_gaji + $ttl_kom)
        ->setCellValue('N' . $kolom, '')
        ->setCellValue('O' . $kolom, $ttl_kasbon)
        ->setCellValue('P' . $kolom, $ttl_denda)
        ->setCellValue('Q' . $kolom, $ttl_gaji + $ttl_kom - $ttl_kasbon - $ttl_denda);

        

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
        $sheet->getStyle('A1:Q1')->applyFromArray($style_header);
        $sheet->getStyle('A' . $kolom .':'. 'Q'.$kolom)->applyFromArray($style_header);

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
        $sheet->getStyle('A2:Q' . $batas)->applyFromArray($style);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="GAJI SERVER TS.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
