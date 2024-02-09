<?php

use App\Http\Controllers\ApiInvoiceController;
use App\Models\Absen;
use Illuminate\Http\Request as r;
use Illuminate\Support\Facades\Route;
use App\Models\Transaksi;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use App\Models\Mencuci;
use App\Models\Order2;
use App\Models\Denda;
use App\Models\Tips;
use App\Models\Kasbon;
use App\Models\Voucher;
use App\Models\Discount;
use App\Models\Harga;
use App\Models\Menu;
use App\Models\Karyawan;
use App\Models\Users;
use App\Models\Permission;
use App\Models\Kategori;
use App\Models\Ctt_driver;
use App\Models\Voucher_hapus;
use App\Models\Tb_hapus_invoice;
use App\Models\Point_kerja;
use App\Models\Jurnal;
use App\Models\Handicap;
use App\Models\Jumlah_orang;
use App\Models\Persentase_kom;
use App\Models\Gaji;
use App\Models\Orderan;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::get('komisi_gaji', function () {
    $komisi = Http::get("https:/majoo.ptagafood.com/api/komisiGaji/1/2022-11-01/2022-11-09");
    $kom = $komisi['komisi'];
    dd($kom[0]);
    return $kom;
});

Route::post('tb_order', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'no_order' => $t['no_order'],
            'id_harga' => $t['id_harga'],
            'qty' => $t['qty'],
            'harga' => $t['harga'],
            'tambahan' => $t['tambahan'],
            'request' => $t['request2'],
            'page' => $t['page'],
            'id_meja' => $t['id_meja'],
            'selesai' => $t['selesai'],
            'id_lokasi' => $t['id_lokasi'],
            'tgl' => $t['tgl'],
            'pengantar' =>  $t['pengantar'],
            'admin' => $t['admin'],
            'void' => $t['void'],
            'round' => $t['round'],
            'alasan' => $t['alasan'],
            'nm_void' => $t['nm_void'],
            'j_mulai' => $t['j_mulai'],
            'j_selesai' => $t['j_selesai'],
            'diskon' => $t['diskon'],
            'wait' => $t['wait'],
            'aktif' => $t['aktif'],
            'id_koki1' => $t['id_koki1'],
            'id_koki2' => $t['id_koki2'],
            'id_koki3' => $t['id_koki3'],
            'ongkir' => $t['ongkir'],
            'id_distribusi' => $t['id_distribusi'],
            'orang' => $t['orang'],
            'no_checker' => $t['no_checker'],
            'print' => 'Y',
            'copy_print' => 'Y',
            'voucher' => $t['voucher']

        ];
        DB::table('tb_order')->insert($data);
    }
});

Route::post('pembayaran', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'id_akun_pembayaran' => $t['id_akun_pembayaran'],
            'no_nota' => $t['no_nota'],
            'nominal' => $t['nominal'],
            'diskon_bank' => $t['diskon_bank'],
            'pengirim' => $t['pengirim'],
            'tgl' => $t['tgl'],
            'tgl_waktu' => $t['tgl_waktu'],
            'id_lokasi' => $t['id_lokasi'],
        ];
        DB::table('pembayaran')->insert($data);
    }
});





Route::post('tb_jurnal', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'id_buku' => $t['id_buku'],
            'id_akun' => $t['id_akun'],
            'kd_gabungan' => $t['kd_gabungan'],
            'no_nota' => $t['no_nota'],
            'id_lokasi' => $t['id_lokasi'],
            'debit' => $t['debit'],
            'kredit' => $t['kredit'],
            'tgl' => $t['tgl'],
            'ket' => $t['ket'],
            'admin' => $t['admin'],
            'status' => $t['status'],
            'created_at' => $t['created_at'],
            'updated_at' => $t['updated_at'],
        ];
        Jurnal::create($data);
    }
});


Route::post('tb_transaksi', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'tgl_transaksi' => $t['tgl_transaksi'],
            'no_order' => $t['no_order'],
            'total_orderan' => $t['total_orderan'],
            'discount' => $t['discount'],
            'voucher' => $t['voucher'],
            'dp' => $t['dp'],
            'gosen' => $t['gosen'],
            'diskon_bank' => $t['diskon_bank'],
            'total_bayar' => $t['total_bayar'],
            'admin' => $t['admin'],
            'round' => $t['round'],
            'id_lokasi' => $t['id_lokasi'],
            'cash' => $t['cash'],
            'd_bca' => $t['d_bca'],
            'k_bca' => $t['k_bca'],
            'd_mandiri' => $t['d_mandiri'],
            'k_mandiri' => $t['k_mandiri'],
            'ongkir' => $t['ongkir'],
            'service' => $t['service'],
            'tax' => $t['tax'],
            'kembalian' => $t['kembalian'],
        ];
        Transaksi::create($data);
    }
});

Route::post('tb_pembelian_majo', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'no_nota' => $t['no_nota'],
            'no_nota2' => $t['no_nota2'],
            'id_karyawan' => $t['id_karyawan'],
            'id_produk' => $t['id_produk'],
            'tanggal' => $t['tanggal'],
            'tgl_input' => $t['tgl_input'],
            'jumlah' => $t['jumlah'],
            'harga' => $t['harga'],
            'jml_komisi' => $t['jml_komisi'],
            'total' => $t['total'],
            'admin' => $t['admin'],
            'no_meja' => $t['no_meja'],
            'lokasi' => $t['lokasi'],
            'void' => $t['void'],
            'selesai' => $t['selesai'],
            'bayar' => $t['bayar'],
        ];
        DB::table('tb_pembelian')->insert($data);
    }
});

Route::post('tb_invoice_new', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'no_nota' => $t['no_nota'],
            'total' => $t['total'],
            'bayar' => $t['bayar'],
            'tgl_jam' => $t['tgl_jam'],
            'tgl_input' => $t['tgl_input'],

            'admin' => $t['admin'],
            'no_meja' => $t['no_meja'],
            'lokasi' => $t['lokasi'],
            'id_distribusi' => $t['id_distribusi'],

        ];
        DB::table('tb_invoice')->insert($data);
    }
});

Route::post('tb_hapus_invoice', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'no_order' => $t['no_order'],
            'tgl_order' => $t['tgl_order'],
            'alasan' => $t['alasan'],
            'nominal_invoice' => $t['nominal_invoice'],
            'id_lokasi' => $t['id_lokasi'],
            'meja' => $t['meja'],
            'admin' => $t['admin'],

        ];
        Tb_hapus_invoice::create($data);
    }
});

Route::post('tb_absen', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'id_karyawan' => $t['id_karyawan'],
            'status' => $t['status'],
            'tgl' => $t['tgl'],
            'id_lokasi' => $t['id_lokasi'],
        ];
        Absen::create($data);
    }
});


Route::post('tb_mencuci', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'nm_karyawan' => $t['nm_karyawan'],
            'id_ket' => $t['id_ket'],
            'j_awal' => $t['j_awal'],
            'j_akhir' => $t['j_akhir'],
            'tgl' => $t['tgl'],
            'admin' => $t['admin'],
        ];
        Mencuci::create($data);
    }
});

Route::post('tb_dp', function (r $b) {
    foreach ($b->all() as $t) {
        $status = $t['status'];
        $kd_dp = $t['kd_dp'];
        $cek = DB::table('tb_dp')->where('kd_dp', $kd_dp)->first();
        $data = [
            'kd_dp' => $t['kd_dp'],
            'nm_customer' =>  $t['nm_customer'],
            'server' =>  $t['server'],
            'jumlah' =>  $t['jumlah'],
            'tgl' =>  $t['tgl'],
            'ket' =>  $t['ket'],
            'metode' =>  $t['metode'],
            'tgl_input' =>  $t['tgl_input'],
            'tgl_digunakan' =>  $t['tgl_digunakan'],
            'status' =>  $t['status'],
            'admin' => $t['admin'],
            'id_lokasi' =>  $t['id_lokasi'],
            'created_at' =>  $t['created_at'],
            'updated_at' =>  $t['updated_at'],
        ];
        if ($cek) {
            DB::table('tb_dp')->where('kd_dp', $kd_dp)->update($data);
        } else {
            DB::table('tb_dp')->insert($data);
        }
    }
});

Route::post('tb_voucherUpdate', function (r $b) {
    foreach ($b->all() as $t) {
        $kode = $t['kode'];
        $terpakai = $t['terpakai'];
        if ($terpakai == 'belum') {
            Voucher::where('kode', $kode)->update(['terpakai' => 'belum', 'updated_at' => $t["updated_at"]]);
        } else {
            Voucher::where('kode', $kode)->update(['terpakai' => 'sudah', 'updated_at' => $t["updated_at"]]);
        }
    }
});

Route::post('tb_produk_majo', function (r $b) {
    foreach ($b->all() as $t) {
        $id_produk = $t['id_produk'];
        $id_lokasi = $t['id_lokasi'];
        DB::table('tb_produk')->where([['id_produk', $id_produk], ['id_lokasi', $id_lokasi]])->update(['stok' => $t['stok']]);
    }
});


Route::post('tb_order2', function (r $t) {
    foreach ($t->all() as  $b) {
        $data = [
            'no_order' =>  $b['no_order'],
            'no_order2' =>  $b['no_order2'],
            'id_harga' =>  $b['id_harga'],
            'qty' => $b['qty'],
            'harga' => $b['harga'],
            'tgl' => $b['tgl'],
            'id_lokasi' => $b['id_lokasi'],
            'admin' => $b['admin'],
            'id_distribusi' => $b['id_distribusi'],
            'id_meja' => $b['id_meja'],
        ];
        Order2::insert($data);
    }
});

Route::post('tb_denda', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'nama' => $t['nama'],
            'alasan' =>  $t['alasan'],
            'nominal' =>  $t['nominal'],
            'tgl' =>  $t['tgl'],
            'id_lokasi' =>  $t['id_lokasi'],
            'admin' => $t['admin'],
        ];
        // Denda::create($data);
    }
});

Route::post('tips_tb', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'tgl' => $t['tgl'],
            'admin' =>  $t['admin'],
            'nominal' =>  $t['nominal'],
        ];
        Tips::create($data);
    }
});

Route::post('tb_kasbon', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'tgl' => $t['tgl'],
            'nm_karyawan' => $t['nm_karyawan'],
            'admin' =>  $t['admin'],
            'nominal' =>  $t['nominal'],
        ];
        // Kasbon::create($data);
    }
});

Route::post('tb_driver', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'no_order' => $t['no_order'],
            'nm_driver' => $t['nm_driver'],
            'nominal' =>  $t['nominal'],
            'tgl' =>  $t['tgl'],
            'admin' =>  $t['admin'],
        ];
        Ctt_driver::create($data);
    }
});

Route::post('komisi', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'id_pembelian' => $t['id_pembelian'],
            'id_kry' => $t['id_kry'],
            'komisi' =>  $t['komisi'],
            'tgl' =>  $t['tgl'],
            'id_lokasi' =>  $t['id_lokasi'],
        ];
        DB::table('komisi')->insert($data);
    }
});

// majo stok ---------------------
Route::get('tb_stok_majo', function () {
    $data = [
        'stok_masuk_tkm' => DB::table('tb_stok_produk')->where('id_lokasi', '1')->get(),
        'stok_masuk_sdb' => DB::table('tb_stok_produk')->where('id_lokasi', '2')->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::post('edit_stok_server', function (r $b) {
    foreach ($b->all() as $t) {
        $id_stok_produk = $t['id_stok_produk'];
        $kode_stok_produk = $t['kode_stok_produk'];
        $id_lokasi = $t['id_lokasi'];
        DB::table('tb_stok_produk')->where('id_stok_produk', $id_stok_produk)->update(['import' => 'Y']);
    }
});
// --------------------------------


// data download / get dari lokal
Route::get('voucher', function () {
    $data = [
        'voucher' => Voucher::all(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('voucher_tkmr', function () {
    $data = [
        'voucher' => Voucher::where('lokasi', '1')->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('voucher_sdb', function () {
    $data = [
        'voucher' => Voucher::where('lokasi', '2')->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('diskon_tkm', function () {
    $data = [
        'diskon' => DB::table('tb_discount')->where('lokasi', 1)->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('diskon_sdb', function () {
    $data = [
        'diskon' => DB::table('tb_discount')->where('lokasi', 2)->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('diskon_peritem', function () {
    $data = [
        'tkm' => DB::table('tb_discount_peritem')->where('id_lokasi', 1)->get(),
        'sdb' => DB::table('tb_discount_peritem')->where('id_lokasi', 2)->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('menu_tb', function () {
    $data = [
        'menu' => Menu::all(),
        'station' => DB::table('tb_station')->get(),
        'harga' => Harga::all(),
        'handicap' => Handicap::all(),
        'kategori_menu' => Kategori::all(),
        'kategori_majo' => DB::table('tb_kategori_majo')->get(),
        'satuan_majo' => DB::table('tb_satuan_majo')->get(),
        'produk_majo' => DB::table('tb_produk')->get(),
        'produk_majo_tkm' => DB::table('tb_produk')->where('id_lokasi', 1)->get(),
        'produk_majo_sdb' => DB::table('tb_produk')->where('id_lokasi', 2)->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});


Route::get('karyawan_tb', function () {
    $data = [
        'karyawan' => Karyawan::all(),
        'karyawan_majo' => DB::table('tb_karyawan_majo')->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});


// delete berskala
Route::get('delete_berskala', function () {
    $data = [
        'order_tkm' => DB::table('tb_order')->where('id_lokasi', 1)->get(),
        'order2_tkm' => DB::table('tb_order2')->where('id_lokasi', 1)->get(),
        'transaksi_tkm' => DB::table('tb_transaksi')->where('id_lokasi', 1)->get(),
        'pembelian_majo_tkm' => DB::table('tb_pembelian')->where('lokasi', 1)->get(),
        'invoice_majo_tkm' => DB::table('tb_invoice')->where('lokasi', 1)->get(),

        'order_sdb' => DB::table('tb_order')->where('id_lokasi', 2)->get(),
        'order2_sdb' => DB::table('tb_order2')->where('id_lokasi', 2)->get(),
        'transaksi_sdb' => DB::table('tb_transaksi')->where('id_lokasi', 2)->get(),
        'pembelian_majo_sdb' => DB::table('tb_pembelian')->where('lokasi', 2)->get(),
        'invoice_majo_sdb' => DB::table('tb_invoice')->where('lokasi', 2)->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});


Route::get('tb_jumlah_orang', function () {
    $data = [
        'tb_jumlah_orang' => Jumlah_orang::all(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('persentase_komisi', function () {
    $data = [
        'persentase_komisi' => Persentase_kom::all(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('tb_menit', function () {
    $data = [
        'tb_menit' => DB::table('tb_menit')->get(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('gaji', function () {
    $data = [
        'gaji' => Gaji::all(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('users', function () {
    $data = [
        'users' => Users::all(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('importAbsen', function () {
    $tgl1 = date('Y-m-1');
    $tgl2 = date('Y-m-t');
    $data = [
        'absenTkmr' => DB::select("SELECT * FROM `tb_absen` WHERE id_lokasi = 1 AND tgl BETWEEN '$tgl1' AND '$tgl2'"),
        'absenSdb' => DB::select("SELECT * FROM `tb_absen` WHERE id_lokasi = 2 AND tgl BETWEEN '$tgl1' AND '$tgl2'"),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('tb_permission', function () {
    $data = [
        'tb_permission' => Permission::all(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('tb_voucher_hapus', function () {
    $data = [
        'tb_voucher_hapus' => Voucher_hapus::all(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('point_kerja', function () {
    $data = [
        'point_kerja' => Point_kerja::all(),
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('pointKitchen/{id_lokasi}/{tgl1}/{tgl2}', function ($id_lokasi, $tgl1, $tgl2) {
    $lamaMenit = DB::table('tb_menit')->where('id_lokasi', $id_lokasi)->first();

    $total_not_gojek = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
            LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
            WHERE tb_transaksi.id_lokasi = '$id_lokasi' and  dt_order.id_distribusi != '2' AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

    $masak = DB::select("SELECT a.nama,a.id_karyawan,b.rp_m, sum(l.qty_m) AS qty_m, sum(l.qty_e) AS qty_e, sum(l.qty_sp) AS qty_sp,e.point_gagal,f.point_berhasil, b.rp_e, b.rp_sp
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

    $absen = DB::select("SELECT a.nama,b.rp_m, sum(l.qty_m) AS qty_m, sum(l.qty_e) AS qty_e, sum(l.qty_sp) AS qty_sp,e.point_gagal,f.point_berhasil, b.rp_e, b.rp_sp
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
                    SELECT koki, SUM(nilai_koki) as point_gagal FROM view_point2 
                    WHERE tgl BETWEEN '$tgl1' AND '$tgl2' AND lama_masak > $lamaMenit->menit and id_lokasi = '$id_lokasi'
                    GROUP BY koki , id_lokasi
                    )e ON a.id_karyawan = e.koki
                    
                    LEFT JOIN (
                        SELECT koki, SUM(nilai_koki) as point_berhasil FROM view_point2 
                        WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND lama_masak <= $lamaMenit->menit and id_lokasi = '$id_lokasi'
                        GROUP BY koki , id_lokasi
                    )f ON a.id_karyawan = f.koki

                        WHERE a.id_status = '1' and a.tgl_masuk <= '$tgl2' and l.id_lokasi ='$id_lokasi' and a.id_posisi not in ('3','2')
                        group by a.id_karyawan
        ");

    $jumlah_orang = DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Kitchen')->where('id_lokasi', $id_lokasi)->first()->jumlah;
    $jumlah_persen = DB::table('persentse_komisi')->where('nama_persentase', 'Kitchen')->where('id_lokasi', $id_lokasi)->first()->jumlah_persen;

    $l = 1;
    $point = 0;
    $orang = 0;

    foreach ($masak as $k) {
        $orang = $l++;
        $point += $k->point_berhasil + $k->point_gagal;
    }

    $orang2 = !$orang ? '0' : $orang;
    $service_charge = $total_not_gojek->total * 0.07;
    $kom =  (((($service_charge  / 7) * $jumlah_persen) / $jumlah_orang)  * $orang2);

    $data = [
        'masak' => $masak,
        'absen' => $absen,
        'service_charge' => $service_charge,
        'orang' => $orang,
        'kom' => $kom,
        'orang2' => $orang2,
        'point' => $point,
        'jumlah_orang' => $jumlah_orang,
        'persen' => $jumlah_persen,

    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('detailPoint/{id_lokasi}/{tgl1}/{tgl2}/{id_karyawan}', function ($id_lokasi, $tgl1, $tgl2, $id_karyawan) {
    $nm_karyawan = DB::table('tb_karyawan')->where('id_karyawan', $id_karyawan)->first();

    $detail = DB::select("SELECT b.tipe,c.nama,a.tgl,a.no_order, b.nm_menu, b.point_menu, a.lama_masak, a.nilai_koki FROM `view_point2` as a
        LEFT JOIN view_menu2 as b on a.id_harga = b.id_harga
        LEFT JOIN tb_karyawan as c on a.koki = c.id_karyawan
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' AND a.id_lokasi = '$id_lokasi' AND c.id_karyawan = '$id_karyawan' AND a.lama_masak <= 25");

    $data = [
        'nm_karyawan' => $nm_karyawan,
        'detail' => $detail,
    ];

    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});

Route::get('komisiServer/{id_lokasi}/{tgl1}/{tgl2}', function ($id_lokasi, $tgl1, $tgl2) {
    $id_lokasi = $id_lokasi;
    $tgl1 = $tgl1;
    $tgl2 = $tgl2;

    $total_not_gojek = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
          LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
          WHERE id_lokasi = '$id_lokasi' AND dt_order.id_distribusi != 2 AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

    $server = DB::select("SELECT 
            a.point, 
            a.nama, 
            a.tgl_masuk, 
            kpi.ttl,
            b.rp_m, 
            sum(l.qty_m) AS qty_m, 
            sum(l.qty_e) AS qty_e, 
            sum(l.qty_sp) AS qty_sp, 
            b.rp_e, 
            b.rp_sp, 
            b.komisi, 
            c.kom, 
            c.nm_karyawan as karyawan_majo
            FROM 
            tb_karyawan AS a 
            left join tb_gaji AS b ON b.id_karyawan = a.id_karyawan 
            LEFT JOIN (
            SELECT 
                c.id_karyawan, 
                c.status, 
                c.id_lokasi, 
                if(
                c.status = 'M', 
                COUNT(c.status), 
                0
                ) AS qty_m, 
                if(
                c.status = 'E', 
                COUNT(c.status), 
                0
                ) AS qty_e, 
                if(
                c.status = 'SP', 
                COUNT(c.status), 
                0
                ) AS qty_sp, 
                if(
                c.status = 'OFF', 
                COUNT(c.status), 
                0
                ) AS qty_off 
            FROM 
                tb_absen AS c 
            WHERE 
                c.tgl BETWEEN '$tgl1' 
                AND '$tgl2' 
                and c.id_lokasi = '$id_lokasi' 
                and c.status != 'OFF' 
            GROUP BY 
                c.id_karyawan, 
                c.status
            ) AS l ON l.id_karyawan = a.id_karyawan 
            LEFT JOIN (
            SELECT 
                a.admin, 
                SUM(
                if(a.voucher = 'Y', 0, a.hrg)
                ) AS komisi 
            FROM 
                view_summary_server AS a 
                LEFT JOIN tb_karyawan as b on a.admin = b.nama 
            WHERE 
                a.tgl BETWEEN '$tgl1' 
                AND '$tgl2' 
                and a.id_lokasi = '$id_lokasi' 
                AND b.point = 'Y' 
            GROUP BY 
                a.admin
            ) AS b ON b.admin = a.nama 
            LEFT JOIN (
            SELECT 
                SUM(a.komisi) AS kom, 
                b.id_karyawan, 
                b.nm_karyawan 
            FROM 
                komisi AS a 
                LEFT JOIN tb_karyawan_majo AS b ON b.kd_karyawan = a.id_kry 
            WHERE 
                a.tgl BETWEEN '$tgl1' 
                AND '$tgl2' 
             
            GROUP BY 
                a.id_kry
            ) AS c ON c.id_karyawan = a.id_karyawan 
            LEFT JOIN (
                SELECT a.id_karyawan,count(a.id_karyawan) as ttl FROM tb_denda_kpi as a
                WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
                GROUP BY a.id_karyawan
            ) kpi ON a.id_karyawan = kpi.id_karyawan
            WHERE 
            a.tgl_masuk <= '$tgl2' 
            and l.id_lokasi = '$id_lokasi' 
            and a.id_status = '2' 
            group by 
            a.id_karyawan

                
            ");

    $data = [
        'server' => $server,
        'service' => $total_not_gojek,
        'jumlah_orang' => DB::table('tb_jumlah_orang')->where('ket_karyawan', 'Server')->where('id_lokasi', $id_lokasi)->first(),
        'persen' => DB::table('persentse_komisi')->where('nama_persentase', 'Server')->where('id_lokasi', $id_lokasi)->first(),
        'settingOrang' => DB::table('db_denda_kpi')->where('id', 1)->first()->rupiah,
        'persenBagi' => DB::table('db_denda_kpi')->where('id', 3)->first()->rupiah,
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});
Route::get('komisiKpi/{id_lokasi}/{tgl1}/{tgl2}', function ($id_lokasi, $tgl1, $tgl2) {
    $client = new Client();

    $response = $client->request('GET', "https://ptagafood.com/api/komisiServer/1/$tgl1/$tgl2");

    $data = json_decode($response->getBody(), true);

    $service = json_decode(json_encode($data['service']), false);
    $server = json_decode(json_encode($data['server']), false);
    $jumlah_orang = json_decode(json_encode($data['jumlah_orang']), false);
    $persen = json_decode(json_encode($data['persen']), false);

    $l = 1;
    $ttl_kom = 0;

    foreach ($server as $k) {
        $o = $l++;
        $ttl_kom += $k->komisi;
    }

    $bagi_kom = $service->total;
    $service_charge = $service->total * 0.07;
    $orang = $o ?? 0;

    $kom = ((($service_charge / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah) * $orang;


    // sdb
    $response = $client->request('GET', "https://ptagafood.com/api/komisiServer/2/$tgl1/$tgl2");
    $data2 = json_decode($response->getBody(), true);
    $service2 = json_decode(json_encode($data2['service']), false);
    $server2 = json_decode(json_encode($data2['server']), false);
    $jumlah_orang2 = json_decode(json_encode($data2['jumlah_orang']), false);
    $persen2 = json_decode(json_encode($data2['persen']), false);


    $l2 = 1;
    $ttl_kom2 = 0;

    foreach ($server2 as $k) {
        $o2 = $l2++;
        $ttl_kom2 += $k->komisi;
    }


    $service_charge2 = $service2->total * 0.07;
    $orang2 = $o2 ?? 0;

    $kom2 = ((($service_charge2 / 7) * $persen2->jumlah_persen) / $jumlah_orang2->jumlah) * $orang2;

    $settingOrang = DB::table('db_denda_kpi')->where('id', 1)->first()->rupiah;
    $persenBagi = DB::table('db_denda_kpi')->where('id', 3)->first()->rupiah;

    $ttlRp = $kom * $persenBagi + $kom2 * $persenBagi;
    $pointR = $ttlRp / $settingOrang;
    $ttlPointRp = $pointR / 10;
    dd($ttlPointRp);
    $komKpi = $pointR - $ttlPointRp * $k->ttl;
});

Route::get('laporan/{id_lokasi}/{tgl1}/{tgl2}', function ($id_lokasi, $tgl1, $tgl2) {

    // $laporan = DB::select("")->result();
    $loc = $id_lokasi;
    $tgl1 = $tgl1;
    $tgl2 = $tgl2;

    $total_gojek = DB::selectOne("SELECT SUM(tb_transaksi.total_orderan - discount - voucher) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE id_lokasi = $loc AND dt_order.id_distribusi = 2 AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

    $total_not_gojek = DB::selectOne("SELECT SUM(if(tb_transaksi.total_orderan - discount - voucher < 0 ,0,tb_transaksi.total_orderan - discount - voucher)) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE id_lokasi = $loc AND dt_order.id_distribusi != 2 AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");


    $majo = DB::selectOne("SELECT SUM(a.bayar) AS bayar_majo
        FROM tb_invoice AS a
        WHERE a.tgl_jam BETWEEN '$tgl1' AND '$tgl2' and a.lokasi = '$loc' and a.id_distribusi = '1'");

    $majo_gojek = DB::selectOne("SELECT SUM(a.bayar) AS bayar_majo
        FROM tb_invoice AS a
        WHERE a.tgl_jam BETWEEN '$tgl1' AND '$tgl2' and a.lokasi = '$loc' and a.id_distribusi = '2'");

    $dp = DB::selectOne("SELECT SUM(a.jumlah) AS jumlah_dp
        FROM tb_dp AS a
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'");

    $pembayaran = DB::select("SELECT b.id_akun_pembayaran as id_akun,b.nm_akun, c.nm_klasifikasi, sum(a.nominal) as nominal, a.pengirim
                FROM pembayaran as a 
                left join akun_pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran
                left join klasifikasi_pembayaran as c on c.id_klasifikasi_pembayaran = b.id_klasifikasi
                where a.tgl between '$tgl1' and '$tgl2' and a.id_lokasi = '$loc'
                group by a.id_akun_pembayaran
                ");

    $transaksi = DB::selectOne("SELECT COUNT(a.no_order) AS ttl_invoice, SUM(a.discount) as discount, SUM(a.voucher) as voucher, sum(if(total_bayar = 0 ,0,a.round)) as rounding, a.id_lokasi, 
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

    $dp = $transaksi->dp ?? 0;
    $total_bayar  = $transaksi->total_bayar ?? 0;
    $service_charge = $total_not_gojek->total * 0.07;
    $pb1_not_gojek = ($total_not_gojek->total + $service_charge) * 0.1;
    $penjualan = $total_not_gojek->total + $service_charge + $pb1_not_gojek;

    $pb1_gojek = ($total_gojek->total + $majo_gojek->bayar_majo * 0.8) / 11;
    $pb1_all = $pb1_gojek + $pb1_not_gojek + $majo->bayar_majo * 0.1;
    $totalSubtotal = $total_gojek->total + $majo_gojek->bayar_majo - $pb1_gojek + ($total_not_gojek->total + $majo->bayar_majo);
    $sub_all = $pb1_all + $totalSubtotal + $service_charge;
    $rounding = $dp + $total_bayar - $sub_all;


    $pnjlGojek = ($total_gojek->total + $majo_gojek->bayar_majo - $pb1_gojek) + $pb1_gojek;

    $pnjlStk = $majo->bayar_majo + ($majo->bayar_majo * 0.1);

    $totalPb1 = $pb1_gojek + $pb1_not_gojek + $majo->bayar_majo * 0.1;
    $totalTotalTanpaDp = $total_bayar;
    $totalTotalTambahDp = $total_bayar + $dp;

    $grabOnline = 0;
    $gojekOnline = 0;
    $shopeOnline = 0;
    $bcaEdc = 0;
    $briEdc = 0;
    $mandiriEdc = 0;
    $mandiriQris = 0;
    $bcaTf = 0;
    $cash = 0;

    $mapping = [
        '1' => 'grabOnline',
        '2' => 'gojekOnline',
        '3' => 'shopeOnline',
        '4' => 'bcaEdc',
        '5' => 'briEdc',
        '6' => 'mandiriEdc',
        '10' => 'mandiriQris',
        '12' => 'bcaTf',
        '13' => 'cash',
    ];

    foreach ($pembayaran as $p) {
        $key = $p->id_akun;

        if (isset($mapping[$key])) {
            $target = $mapping[$key];

            if (is_array($target)) {
                foreach ($target as $t) {
                    $$t = $p->nominal;
                }
            } else {
                $$target = $p->nominal;
            }
        }
    }



    $data = [
        'penjualan' => [
            'pnjl' => $penjualan,
            'rounding' => $rounding,
            'penjualanGojek' => $pnjlGojek,
            'penjualanStk' => $pnjlStk,
        ],
        'biaya' => [
            'pb1gojek' => $pb1_gojek,
            'pb1stk' => $majo->bayar_majo * 0.1,
            'pb1dinein' => $pb1_not_gojek,
            'serviceCharge' => $service_charge,
        ],
        'hutang' => [
            'dp' => $dp,
        ],
        'total' => [
            'subtotal' => $totalSubtotal,
            'totalPb1' => $totalPb1,
            'totalTanpaDp' => $totalTotalTanpaDp,
            'totalTambahDp' => $totalTotalTambahDp,
        ],
        'pembayaran' => [
            'grabOnline' => $grabOnline,
            'gojekOnline' => $gojekOnline,
            'shopeOnline' => $shopeOnline,
            'bcaEdc' => $bcaEdc,
            'briEdc' => $briEdc,
            'mandiriEdc' => $mandiriEdc,
            'mandiriQris' => $mandiriQris,
            'bcaTf' => $bcaTf,
            'cash' => $cash,

            'cashLama' => $transaksi->cash ?? 0,
            'bcaDebit' => $transaksi->d_bca ?? 0,
            'bcaKredit' => $transaksi->k_bca ?? 0,
            'mandiriDebit' => $transaksi->d_mandiri ?? 0,
            'mandiriKredit' => $transaksi->k_mandiri ?? 0
        ]

    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});
Route::get('/invoice_nanda', [ApiInvoiceController::class, 'invoice'])->name('invoice_nanda');
Route::get('/menu', [ApiInvoiceController::class, 'menu'])->name('menu');


Route::post('api_tes', function (r $b) {
    foreach ($b->all() as $t) {
        $data = [
            'tes' => $t['tes'],
        ];
        DB::table('api_tes')->insert($data);
    }
});
