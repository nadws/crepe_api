<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)->where('id_menu', 28)->first();
        if (empty($id_menu)) {
            return back();
        } else {
            $data = [
                'title' => 'Laporan',
                'logout' => $request->session()->get('logout'),
            ];

            return view('laporan.laporan', $data);
        }
    }

    public function getQueryTransaksi($tgl1, $tgl2, $loc)
    {
        return DB::selectOne("SELECT COUNT(a.no_order) AS ttl_invoice, SUM(a.discount) as discount, SUM(a.voucher) as voucher, sum(if(total_bayar = 0 ,0,a.round)) as rounding, a.id_lokasi, 
        SUM(a.total_orderan) AS rp, d.unit, a.no_order, sum(a.dp) as dp, sum(a.gosen) as gosend, sum(a.service) as ser, sum(a.tax) as tax,f.qty_void, f.void,
        SUM(a.cash) as cash, SUM(a.d_bca) as d_bca, SUM(a.k_bca) as k_bca, SUM(a.d_mandiri) as d_mandiri, SUM(a.k_mandiri) as k_mandiri,SUM(a.d_bri) as d_bri, SUM(a.k_bri) as k_bri, SUM(total_bayar) as total_bayar
        
        FROM tb_transaksi AS a
        
        LEFT JOIN(
        SELECT SUM(b.qty) AS unit , b.no_order, b.id_lokasi
        FROM tb_order AS b
        WHERE b.tgl BETWEEN '$tgl1' AND '$tgl2' AND b.id_lokasi = '$loc' AND b.void = 0
        GROUP BY b.id_lokasi
        )AS d ON d.id_lokasi = a.id_lokasi
        
        LEFT JOIN(
        SELECT SUM(e.void) AS void , COUNT(e.void) AS qty_void, e.no_order, e.id_lokasi
        FROM tb_order AS e
        WHERE e.tgl BETWEEN '$tgl1' AND '$tgl2' AND e.id_lokasi = '$loc' AND e.void != '0'
        GROUP BY e.id_lokasi
        )AS f ON f.id_lokasi = a.id_lokasi
        
        
        where a.tgl_transaksi BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'
        GROUP BY a.id_lokasi");
    }

    public function laporan_ibu(Request $r)
    {
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $tkmrTotal = $this->getQueryTransaksi($tgl1, $tgl2, 1)->dp + $this->getQueryTransaksi($tgl1, $tgl2, 1)->total_bayar;
        $sdbTotal = $this->getQueryTransaksi($tgl1, $tgl2, 2)->dp + $this->getQueryTransaksi($tgl1, $tgl2, 2)->total_bayar;

        $komisiTkm = Http::get("https://majoo.ptagafood.com/api/laporan/takemori/$tgl1/$tgl2");
        $laporanTkm = $komisiTkm['laporan'];
        $tkmrMajo = 0;
        foreach ($laporanTkm as $d) {
            $tkmrMajo += $d['total'];
        }

        $komisiSdb = Http::get("https://majoo.ptagafood.com/api/laporan/soondobu/$tgl1/$tgl2");
        $laporanSdb = $komisiSdb['laporan'];
        $sdbMajo = 0;
        foreach ($laporanSdb as $d) {
            $sdbMajo += $d['total'];
        }

        $takemori = $tkmrTotal + $tkmrMajo;
        $soondobu = $sdbTotal + $sdbMajo;

        $data = [
            'takemori' => $takemori,
            'soondobu' => $soondobu,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ];
        // return view('laporan.laporan_ibu', $data);
    }

    public function summary(Request $request)
    {
        // $laporan = DB::select("")->result();
        $loc = $request->session()->get('id_lokasi');
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;

        $total_gojek = DB::selectOne("SELECT SUM(tb_transaksi.total_orderan - discount - voucher) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE id_lokasi = $loc AND dt_order.id_distribusi = 2 AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $total_not_gojek = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE id_lokasi = $loc AND dt_order.id_distribusi != 2 AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $jml_telat = DB::selectOne("SELECT SUM(qty) AS jml_telat FROM view_koki_masak WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND id_lokasi = $loc AND menit_bagi > 25");
        $jml_telat20 = DB::selectOne("SELECT SUM(qty) AS jml_telat FROM view_koki_masak WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND id_lokasi = $loc AND menit_bagi > 20");
        $jml_ontime = DB::selectOne("SELECT SUM(qty) AS jml_ontime FROM view_koki_masak WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND id_lokasi = $loc AND menit_bagi <= 25");

        $majo = DB::selectOne("SELECT sum(a.bayar) AS bayar_majo , a.no_nota, b.no_order
        FROM tb_invoice AS a
        left join tb_transaksi as b on b.no_order = a.no_nota
        WHERE a.tgl_jam BETWEEN '$tgl1' AND '$tgl2' and a.lokasi = '$loc' and a.id_distribusi = '1' and b.no_order is not null;");

        $majo_gojek = DB::selectOne("SELECT SUM(a.bayar) AS bayar_majo
        FROM tb_invoice AS a
        left join tb_transaksi as b on b.no_order = a.no_nota
        WHERE a.tgl_jam BETWEEN '$tgl1' AND '$tgl2' and a.lokasi = '$loc' and a.id_distribusi = '2'and b.no_order is not null;");

        $dp = DB::selectOne("SELECT SUM(a.jumlah) AS jumlah_dp
        FROM tb_dp AS a
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'");



        $data = [
            'title'    => 'Summary',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'dp' => $dp,
            'jml_telat20' => $jml_telat20,
            'transaksi' => DB::selectOne("SELECT COUNT(a.no_order) AS ttl_invoice, SUM(a.discount) as discount, SUM(a.voucher) as voucher, sum(if(total_bayar = 0 ,0,a.round)) as rounding, a.id_lokasi, 
            SUM(a.total_orderan) AS rp, d.unit, a.no_order, sum(a.dp) as dp, sum(a.gosen) as gosend, sum(a.service) as ser, sum(a.tax) as tax,f.qty_void, f.void,
            SUM(a.cash) as cash, SUM(a.d_bca) as d_bca, SUM(a.k_bca) as k_bca, SUM(a.d_mandiri) as d_mandiri, SUM(a.k_mandiri) as k_mandiri,SUM(a.d_bri) as d_bri, SUM(a.k_bri) as k_bri, SUM(total_bayar) as total_bayar
            
            FROM tb_transaksi AS a
            
            LEFT JOIN(
            SELECT SUM(b.qty) AS unit , b.no_order, b.id_lokasi
            FROM tb_order AS b
            WHERE b.tgl BETWEEN '$tgl1' AND '$tgl2' AND b.id_lokasi = '$loc' AND b.void = 0
            GROUP BY b.id_lokasi
            )AS d ON d.id_lokasi = a.id_lokasi
            
            LEFT JOIN(
            SELECT SUM(e.void) AS void , COUNT(e.void) AS qty_void, e.no_order, e.id_lokasi
            FROM tb_order AS e
            WHERE e.tgl BETWEEN '$tgl1' AND '$tgl2' AND e.id_lokasi = '$loc' AND e.void != '0'
            GROUP BY e.id_lokasi
            )AS f ON f.id_lokasi = a.id_lokasi
            
            
            where a.tgl_transaksi BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'
            GROUP BY a.id_lokasi"),

            'kategori' => DB::select("SELECT b.nm_menu, c.kategori ,sum(e.harga2) as hargaT, sum(a.qty) AS qty
                FROM tb_order AS a 
                LEFT JOIN view_menu2 AS b ON b.id_harga = a.id_harga
                left join tb_kategori as c on c.kd_kategori = b.id_kategori

                left join(select d.id_harga, d.id_order, (d.harga * d.qty) as harga2 from tb_order as d 
                WHERE d.tgl BETWEEN '$tgl1' AND '$tgl2' and d.id_lokasi = '$loc' and d.id_distribusi = '1'
                group by d.id_order) as e on e.id_order = a.id_order           
                        
                WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc' and a.id_distribusi = '1' 
                GROUP BY b.id_kategori"),

            'gojek' => DB::select("SELECT b.nm_menu, c.kategori, sum(e.harga2) as harga, sum(a.qty) AS qty
            FROM tb_order AS a 
            LEFT JOIN view_menu2 AS b ON b.id_harga = a.id_harga
            left join tb_kategori as c on c.kd_kategori = b.id_kategori
            left join(select d.id_harga, d.id_order, (d.harga * d.qty) as harga2 from tb_order as d 
            WHERE d.tgl BETWEEN '$tgl1' AND '$tgl2' and d.id_lokasi = '$loc' and d.id_distribusi = '2'
            group by d.id_order) as e on e.id_order = a.id_order  
                        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc' and a.id_distribusi = '2'
                        GROUP BY b.id_kategori"),

            'total_gojek' => $total_gojek,
            'total_not_gojek' => $total_not_gojek,
            'jml_telat' => $jml_telat,
            'lokasi' => $loc,
            'jml_ontime' => $jml_ontime,
            'majo' => $majo,
            'majo_gojek' => $majo_gojek,
            'void' => DB::select("SELECT c.kategori,b.nm_menu,sum(a.void) as void, sum(a.harga) as harga FROM `tb_order` as a 
                        LEFT JOIN view_menu2 as b on a.id_harga = b.id_harga
                        left join tb_kategori as c on b.id_kategori = c.kd_kategori
                        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' AND a.void = 1 AND id_lokasi = '$loc'
                        GROUP BY c.kd_kategori"),
            'pembayaran' => DB::select("SELECT b.nm_akun, c.nm_klasifikasi, sum(a.nominal) as nominal, a.pengirim
            FROM pembayaran as a 
            left join akun_pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran
            left join klasifikasi_pembayaran as c on c.id_klasifikasi_pembayaran = b.id_klasifikasi
            where a.tgl between '$tgl1' and '$tgl2' and a.id_lokasi = '$loc'
            group by a.id_akun_pembayaran
            "),


        ];
        return view('laporan.summary', $data);
    }
    public function ex_summary(Request $request)
    {
        // $laporan = DB::select("")->result();
        $loc = $request->session()->get('id_lokasi');
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;

        $total_gojek = DB::selectOne("SELECT SUM(tb_transaksi.total_orderan - discount - voucher) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE id_lokasi = $loc AND dt_order.id_distribusi = 2 AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $total_not_gojek = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE id_lokasi = $loc AND dt_order.id_distribusi != 2 AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $jml_telat = DB::selectOne("SELECT SUM(qty) AS jml_telat FROM view_koki_masak WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND id_lokasi = $loc AND menit_bagi > 25");
        $jml_telat20 = DB::selectOne("SELECT SUM(qty) AS jml_telat FROM view_koki_masak WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND id_lokasi = $loc AND menit_bagi > 20");
        $jml_ontime = DB::selectOne("SELECT SUM(qty) AS jml_ontime FROM view_koki_masak WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND id_lokasi = $loc AND menit_bagi <= 25");

        $majo = DB::selectOne("SELECT SUM(a.bayar) AS bayar_majo
        FROM tb_invoice AS a
        WHERE a.tgl_jam BETWEEN '$tgl1' AND '$tgl2' and a.lokasi = '$loc' and a.id_distribusi = '1'");

        $majo_gojek = DB::selectOne("SELECT SUM(a.bayar) AS bayar_majo
        FROM tb_invoice AS a
        WHERE a.tgl_jam BETWEEN '$tgl1' AND '$tgl2' and a.lokasi = '$loc' and a.id_distribusi = '2'");

        $dp = DB::selectOne("SELECT SUM(a.jumlah) AS jumlah_dp
        FROM tb_dp AS a
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'");

        $data = [
            'title'    => 'Summary',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'dp' => $dp,
            'jml_telat20' => $jml_telat20,
            'transaksi' => DB::selectOne("SELECT COUNT(a.no_order) AS ttl_invoice, SUM(a.discount) as discount, SUM(a.voucher) as voucher, sum(if(total_bayar = 0 ,0,a.round)) as rounding, a.id_lokasi, 
            SUM(a.total_orderan) AS rp, d.unit, a.no_order, sum(a.dp) as dp, sum(a.gosen) as gosend, sum(a.service) as ser, sum(a.tax) as tax,f.qty_void, f.void,
            SUM(a.cash) as cash, SUM(a.d_bca) as d_bca, SUM(a.k_bca) as k_bca, SUM(a.d_mandiri) as d_mandiri, SUM(a.k_mandiri) as k_mandiri,SUM(a.d_bri) as d_bri, SUM(a.k_bri) as k_bri, SUM(total_bayar) as total_bayar
            
            FROM tb_transaksi AS a
            
            LEFT JOIN(
            SELECT SUM(b.qty) AS unit , b.no_order, b.id_lokasi
            FROM tb_order AS b
            WHERE b.tgl BETWEEN '$tgl1' AND '$tgl2' AND b.id_lokasi = '$loc' AND b.void = 0
            GROUP BY b.id_lokasi
            )AS d ON d.id_lokasi = a.id_lokasi
            
            LEFT JOIN(
            SELECT SUM(e.void) AS void , COUNT(e.void) AS qty_void, e.no_order, e.id_lokasi
            FROM tb_order AS e
            WHERE e.tgl BETWEEN '$tgl1' AND '$tgl2' AND e.id_lokasi = '$loc' AND e.void != '0'
            GROUP BY e.id_lokasi
            )AS f ON f.id_lokasi = a.id_lokasi
            
            
            where a.tgl_transaksi BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'
            GROUP BY a.id_lokasi"),

            'kategori' => DB::select("SELECT b.nm_menu, c.kategori ,sum(e.harga2) as hargaT, sum(a.qty) AS qty
        FROM tb_order AS a 
        LEFT JOIN view_menu2 AS b ON b.id_harga = a.id_harga
        left join tb_kategori as c on c.kd_kategori = b.id_kategori

        left join(select d.id_harga, d.id_order, (d.harga * d.qty) as harga2 from tb_order as d 
        WHERE d.tgl BETWEEN '$tgl1' AND '$tgl2' and d.id_lokasi = '$loc' and d.id_distribusi = '1'
        group by d.id_order) as e on e.id_order = a.id_order           
                
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc' and a.id_distribusi = '1' 
        GROUP BY b.id_kategori"),

            'gojek' => DB::select("SELECT b.nm_menu, c.kategori, sum(e.harga2) as harga, sum(a.qty) AS qty
            FROM tb_order AS a 
            LEFT JOIN view_menu2 AS b ON b.id_harga = a.id_harga
            left join tb_kategori as c on c.kd_kategori = b.id_kategori
            left join(select d.id_harga, d.id_order, (d.harga * d.qty) as harga2 from tb_order as d 
        WHERE d.tgl BETWEEN '$tgl1' AND '$tgl2' and d.id_lokasi = '$loc' and d.id_distribusi = '2'
        group by d.id_order) as e on e.id_order = a.id_order  
                    WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc' and a.id_distribusi = '2'
                    GROUP BY b.id_kategori"),

            'total_gojek' => $total_gojek,
            'total_not_gojek' => $total_not_gojek,
            'jml_telat' => $jml_telat,
            'lokasi' => $loc,
            'jml_ontime' => $jml_ontime,
            'majo' => $majo,
            'majo_gojek' => $majo_gojek,
            'void' => DB::select("SELECT c.kategori,b.nm_menu,sum(a.void) as void, sum(a.harga) as harga FROM `tb_order` as a 
                        LEFT JOIN view_menu2 as b on a.id_harga = b.id_harga
                        left join tb_kategori as c on b.id_kategori = c.kd_kategori
                        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' AND a.void = 1 AND id_lokasi = '$loc'
                        GROUP BY c.kd_kategori"),
            'pembayaran' => DB::select("SELECT b.nm_akun, c.nm_klasifikasi, sum(a.nominal) as nominal, a.pengirim
                        FROM pembayaran as a 
                        left join akun_pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran
                        left join klasifikasi_pembayaran as c on c.id_klasifikasi_pembayaran = b.id_klasifikasi
                        where a.tgl between '$tgl1' and '$tgl2' and a.id_lokasi = '$loc'
                        group by a.id_akun_pembayaran
                        "),
        ];
        return view('laporan.ex_summary', $data);
    }

    public function item(Request $request)
    {
        // $laporan = $this->db->query("")->result();
        $loc = $request->session()->get('id_lokasi');
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $data = [
            'title'    => 'Summary',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,

            'kategori' => DB::select("SELECT b.nm_menu, a.harga, sum(a.qty) AS qty
            FROM tb_order AS a 
            LEFT JOIN view_menu AS b ON b.id_harga = a.id_harga
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'
            GROUP BY a.id_harga")
        ];
        return view('laporan.item', $data);
    }

    public function export_item(Request $request)
    {
        $loc = $request->session()->get('id_lokasi');
        $lokasi = $loc == 1 ? 'Laporan Per Item TAKEMORI.xlsx' : 'Laporan Per Item SOONDOBU.xlsx';
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;

        $dt_item = DB::select("SELECT b.id_distribusi,d.nm_station, b.nm_menu, a.harga, sum(a.qty) AS qty
        FROM tb_order AS a 
        LEFT JOIN view_menu AS b ON b.id_harga = a.id_harga
        LEFT JOIN tb_menu as c ON b.id_menu = c.id_menu
        LEFT JOIN tb_station as d ON d.id_station = c.id_station
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'
        GROUP BY a.id_harga");

        $spreadsheet = new Spreadsheet;

        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getActiveSheet()->setCellValue('A1', '#');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Station');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'Nama Menu');
        $spreadsheet->getActiveSheet()->setCellValue('D1', 'Qty');
        $spreadsheet->getActiveSheet()->setCellValue('E1', 'Harga Satuan');
        $spreadsheet->getActiveSheet()->setCellValue('F1', 'Subtotal');


        $style = array(
            'font' => array(
                'size' => 9
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

        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->applyFromArray($style);


        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setWrapText(true);


        $kolom = 2;
        $no = 1;
        foreach ($dt_item as $d) {
            if ($d->nm_menu == '') {
                continue;
            }
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setCellValue('A' . $kolom, $no++);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $kolom, $d->nm_station);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $kolom, $d->id_distribusi == 2 ? $d->nm_menu . ' Gojek' : $d->nm_menu);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $kolom, $d->qty);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $kolom, $d->harga);
            $spreadsheet->getActiveSheet()->setCellValue('F' . $kolom, $d->qty * $d->harga);
            $kolom++;
        }

        $border_collom = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            )
        );

        $batas = $kolom - 1;
        $spreadsheet->getActiveSheet()->getStyle('A1:F' . $batas)->applyFromArray($style);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $lokasi);
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function get_telat(Request $request)
    {
    }

    public function get_ontime(Request $request)
    {
    }

    public function masak(Request $request)
    {
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;

        $service = DB::selectOne("SELECT a.admin, SUM(if(a.hrg - a.voucher < 0 ,0, a.hrg - a.voucher)) AS komisi
        FROM view_summary_server AS a
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'");

        $masak = DB::select("SELECT a.nama, a.id_status, b.point_berhasil, c.point_gagal,d.point_bar,e.clear_up, f.mencuci,g.prepare,h.prepare_salmon,i.prepare_gyoza, j.checker,k.pasar,sum(l.qty_m) AS M, sum(l.qty_e) AS E, sum(l.qty_sp) AS Sp,sum(l.qty_off) AS of,
        e.clear_point, f.mencuci_point,g.prepare_point,h.prepare_salmon_point,i.prepare_gyoza_point,j.checker_point,k.pasar_point,m.nm_posisi, m.ket, n.komisi1,o.komisi2
        FROM tb_karyawan AS a
        LEFT JOIN (
            SELECT a.no_order , c.nama, a.koki, a.id_lokasi, sum(if(b.voucher = '0',a.nilai_koki,0)) AS point_berhasil, SUM(a.nilai_koki) AS nilai2, if(b.voucher = '0','T','Y') AS vouceher
            FROM view_nilai_masak2 AS a
            LEFT JOIN tb_karyawan AS c ON c.id_karyawan = a.koki
            LEFT JOIN (SELECT b.no_order, b.no_order2 , c.voucher 
            FROM tb_order2 AS b 
            LEFT JOIN tb_transaksi AS c ON c.no_order = b.no_order2
            GROUP BY b.no_order ) AS b ON b.no_order = a.no_order
            WHERE tgl BETWEEN '$tgl1' AND '$tgl2' AND lama_masak <= 30
            GROUP BY a.koki ) AS b ON a.id_karyawan = b.koki
        
        LEFT JOIN (
        SELECT koki, SUM(nilai_koki) as point_gagal FROM view_nilai_masak2 
        WHERE tgl BETWEEN '$tgl1' AND '$tgl2' AND lama_masak > 30
        GROUP BY koki
        )c ON a.id_karyawan = c.koki
        LEFT JOIN (
        SELECT koki, SUM(nilai_koki) as point_bar FROM view_bar2 
        WHERE tgl BETWEEN '$tgl1' AND '$tgl2' 
        GROUP BY koki)d ON a.id_karyawan = d.koki

        LEFT JOIN (SELECT b.nm_karyawan , b.id_ket, c.point as clear_point,  SUM(b.lama_cuci) AS clear_up
        FROM view_mencuci AS b left join keterangan_cuci as c on c.id_ket = b.id_ket WHERE b.id_ket = '1' AND b.tgl BETWEEN '$tgl1' AND '$tgl2'  GROUP BY b.nm_karyawan ) AS e ON e.nm_karyawan = a.nama

        LEFT JOIN (SELECT b.nm_karyawan , b.id_ket , c.point as mencuci_point, SUM(b.lama_cuci) AS mencuci
        FROM view_mencuci AS b left join keterangan_cuci as c on c.id_ket = b.id_ket WHERE b.id_ket = '2' AND b.tgl BETWEEN '$tgl1' AND '$tgl2'  GROUP BY b.nm_karyawan ) AS f ON f.nm_karyawan = a.nama

        LEFT JOIN (SELECT b.nm_karyawan , b.id_ket ,c.point as prepare_point, SUM(b.lama_cuci) AS prepare
        FROM view_mencuci AS b left join keterangan_cuci as c on c.id_ket = b.id_ket WHERE b.id_ket = '4' AND b.tgl BETWEEN '$tgl1' AND '$tgl2'  GROUP BY b.nm_karyawan ) AS g ON g.nm_karyawan = a.nama

        LEFT JOIN (SELECT b.nm_karyawan , b.id_ket ,c.point as prepare_salmon_point, SUM(b.lama_cuci) AS prepare_salmon
        FROM view_mencuci AS b left join keterangan_cuci as c on c.id_ket = b.id_ket  WHERE b.id_ket = '8' AND b.tgl BETWEEN '$tgl1' AND '$tgl2'  GROUP BY b.nm_karyawan ) AS h ON h.nm_karyawan = a.nama

        LEFT JOIN (SELECT b.nm_karyawan , b.id_ket ,c.point as prepare_gyoza_point, SUM(b.lama_cuci) AS prepare_gyoza
        FROM view_mencuci AS b left join keterangan_cuci as c on c.id_ket = b.id_ket WHERE b.id_ket = '9' AND b.tgl BETWEEN '$tgl1' AND '$tgl2'  GROUP BY b.nm_karyawan ) AS i ON i.nm_karyawan = a.nama

        LEFT JOIN (SELECT b.nm_karyawan , b.id_ket , c.point as checker_point, SUM(b.lama_cuci) AS checker
        FROM view_mencuci AS b left join keterangan_cuci as c on c.id_ket = b.id_ket WHERE b.id_ket = '5' AND b.tgl BETWEEN '$tgl1' AND '$tgl2'  GROUP BY b.nm_karyawan ) AS j ON j.nm_karyawan = a.nama

        LEFT JOIN (SELECT b.nm_karyawan , b.id_ket , c.point as pasar_point, SUM(b.lama_cuci) AS pasar
        FROM view_mencuci AS b left join keterangan_cuci as c on c.id_ket = b.id_ket WHERE b.id_ket = '10' AND b.tgl BETWEEN '$tgl1' AND '$tgl2'  GROUP BY b.nm_karyawan ) AS k ON k.nm_karyawan = a.nama

        LEFT JOIN (
        SELECT c.id_karyawan,  c.status,
        if(c.status = 'M', COUNT(c.status), 0) AS qty_m,
        if(c.status = 'E', COUNT(c.status), 0) AS qty_e,
        if(c.status = 'SP', COUNT(c.status), 0) AS qty_sp,
        if(c.status = 'OFF', COUNT(c.status), 0) AS qty_off
        FROM tb_absen AS c 
        WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY c.id_karyawan, c.status
        ) AS l ON l.id_karyawan = a.id_karyawan

        left join tb_posisi as m on m.id_posisi = a.id_posisi

        left join (SELECT a.id_koki1, a.id_koki2, a.id_koki3, SUM(if(a.hrg - a.voucher < 0 ,0, a.hrg - a.voucher)) AS komisi1
        FROM view_summary_koki AS a
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY a.id_koki1) as n on n.id_koki1 = a.id_karyawan

        left join (SELECT a.id_koki1, a.id_koki2, a.id_koki3, SUM(if(a.hrg - a.voucher < 0 ,0, a.hrg - a.voucher)) AS komisi2
        FROM view_summary_koki AS a
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY a.id_koki2) as o on o.id_koki2 = a.id_karyawan
        
        WHERE a.id_status = '1'
        group by a.id_karyawan
        ");
        $data = [
            'masak' => $masak,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'service' => $service
        ];

        return view('laporan.masak', $data);
    }

    public function item_majo(Request $r)
    {
        $loc = $r->session()->get('id_lokasi');
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $data = [
            'title'    => 'Summary',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,

            'kategori' => DB::select("SELECT b.nm_produk, a.harga, SUM(a.jumlah) as qty FROM `tb_pembelian` as a
            LEFT JOIN tb_produk as b ON a.id_produk = b.id_produk
            join (
                SELECT no_nota FROM tb_invoice GROUP BY no_nota
            ) c on c.no_nota = a.no_nota2
            WHERE a.tanggal BETWEEN '$tgl1' AND '$tgl2' AND a.lokasi = '$loc' GROUP BY a.id_produk;")
        ];
        return view('laporan.itemMajo', $data);
    }

    public function export_itemMajo(Request $r)
    {
        $loc = $r->session()->get('id_lokasi');
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;

        // $dt_item = DB::select("SELECT b.nm_produk, b.harga, SUM(a.jumlah) as qty FROM `tb_pembelian` as a
        // LEFT JOIN tb_produk as b ON a.id_produk = b.id_produk
        // WHERE a.tanggal BETWEEN '$tgl1' AND '$tgl2' AND a.lokasi = '$loc' GROUP BY a.id_produk");

        $dt_item = DB::select("SELECT a.nm_produk, b.qty, b.total from tb_produk as a left join ( SELECT b.id_produk, sum(b.jumlah) as qty , sum(b.total) as total FROM tb_pembelian as b where b.tanggal BETWEEN '$tgl1' and '$tgl2' and lokasi = '$loc' group by b.id_produk ) as b on b.id_produk = a.id_produk where a.id_lokasi ='$loc'");

        $spreadsheet = new Spreadsheet;

        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getActiveSheet()->setCellValue('A1', '#');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Nama Menu');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'Qty');
        $spreadsheet->getActiveSheet()->setCellValue('D1', 'Subtotal');


        $style = array(
            'font' => array(
                'size' => 9
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

        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->applyFromArray($style);


        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setWrapText(true);


        $kolom = 2;
        $no = 1;
        foreach ($dt_item as $d) {
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setCellValue('A' . $kolom, $no++);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $kolom, $d->nm_produk);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $kolom, $d->qty);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $kolom, $d->total);
            $kolom++;
        }

        $border_collom = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            )
        );

        $batas = $kolom - 1;
        $spreadsheet->getActiveSheet()->getStyle('A1:D' . $batas)->applyFromArray($style);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan Per Item Majo.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    function cek_invoice(Request $r)
    {
        $loc = $r->session()->get('id_lokasi');
        $data = [
            'invoice' => DB::select("SELECT a.tgl, a.id_akun_pembayaran, a.no_nota, b.nm_akun, c.nm_klasifikasi, a.nominal, d.id_distribusi, d.id_lokasi, a.pengirim, a.diskon_bank
            FROM pembayaran as a
            left JOIN akun_pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran
            left join klasifikasi_pembayaran as c on c.id_klasifikasi_pembayaran = b.id_klasifikasi
            left join(
                SELECT d.no_order2 , d.id_distribusi, d.id_lokasi
                FROM tb_order2 as d
                GROUP by d.no_order2
            ) as d on d.no_order2 = a.no_nota
            where a.tgl BETWEEN '$r->tgl1' and '$r->tgl2' and a.id_lokasi = '$loc' ;"),
            'tgl1' => $r->tgl1,
            'tgl2' => $r->tgl2,
            'pembayaran' => DB::select("SELECT *
            FROM akun_pembayaran as a
            left join klasifikasi_pembayaran as b on b.id_klasifikasi_pembayaran = a.id_klasifikasi
            ")
        ];

        return view('laporan.cek_invoice', $data);
    }

    function print_cek_invoice(Request $r)
    {
        $loc = $r->session()->get('id_lokasi');
        $data = [
            'invoice' => DB::select("SELECT a.tgl, a.id_akun_pembayaran, a.no_nota, b.nm_akun, c.nm_klasifikasi, a.nominal, d.id_distribusi, d.id_lokasi, a.diskon_bank
            FROM pembayaran as a
            left JOIN akun_pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran
            left join klasifikasi_pembayaran as c on c.id_klasifikasi_pembayaran = b.id_klasifikasi
            left join(
                SELECT d.no_order2 , d.id_distribusi, d.id_lokasi
                FROM tb_order2 as d
                GROUP by d.no_order2
            ) as d on d.no_order2 = a.no_nota
            where a.tgl BETWEEN '$r->tgl1' and '$r->tgl2' and a.id_lokasi = '$loc';"),
            'tgl1' => $r->tgl1,
            'tgl2' => $r->tgl2,
            'lokasi' => $loc == '1' ? 'TAKEMORI' : 'SOONDOBU'
        ];

        return view('laporan.print_cek_invoice', $data);
    }
    function excel_cek_invoice(Request $r)
    {
        $loc = $r->session()->get('id_lokasi');
        $data = [
            'invoice' => DB::select("SELECT a.tgl, a.id_akun_pembayaran, a.no_nota, b.nm_akun, c.nm_klasifikasi, a.nominal, d.id_distribusi, d.id_lokasi,  a.diskon_bank
            FROM pembayaran as a
            left JOIN akun_pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran
            left join klasifikasi_pembayaran as c on c.id_klasifikasi_pembayaran = b.id_klasifikasi
            left join(
                SELECT d.no_order2 , d.id_distribusi, d.id_lokasi
                FROM tb_order2 as d
                GROUP by d.no_order2
            ) as d on d.no_order2 = a.no_nota
            where a.tgl BETWEEN '$r->tgl1' and '$r->tgl2' and a.id_lokasi = '$loc';"),
            'tgl1' => $r->tgl1,
            'tgl2' => $r->tgl2,
            'lokasi' => $loc == '1' ? 'TAKEMORI' : 'SOONDOBU'
        ];

        return view('laporan.excel_cek_invoice', $data);
    }
}
