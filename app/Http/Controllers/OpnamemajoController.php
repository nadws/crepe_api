<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OpnamemajoController extends Controller
{
    public function index(Request $r)
    {
        $id_user = Auth::user()->id;
        $id_lokasi = $r->session()->get('id_lokasi');


        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 12)->first();
        if (empty($id_menu)) {
            return back();
        } else {
            if (empty($r->tgl1)) {
                $tgl1   = date('Y-m-01');
                $tgl2   = date('Y-m-d');
            } else {
                $tgl1   = $r->tgl1;
                $tgl2   = $r->tgl2;
            }
            $data = [
                'title' => 'Opname Produk',
                'opname' => DB::select("SELECT * FROM tb_stok_produk as a left join tb_produk as b on a.id_produk = b.id_produk where a.tgl between '$tgl1' AND '$tgl2' and b.id_lokasi = '$id_lokasi' and a.ket = 'opname'  GROUP BY kode_stok_produk ORDER BY a.id_stok_produk DESC"),
                'id_lokasi' => $id_lokasi,
                'logout' => $r->session()->get('logout'),
            ];
            // dd($data['produk']);
            return view('opname.index', $data);
        }
    }

    public function buatOpname(Request $r)
    {
        $id_lokasi = $r->session()->get('id_lokasi');
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 12)->first();
        if (empty($id_menu)) {
            return back();
        } else {
            $data = [
                'title' => 'Opname Produk',
                'produk' => DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
                FROM tb_produk AS a
                LEFT JOIN tb_satuan_majo AS b ON b.id_satuan = a.id_satuan
                LEFT JOIN tb_kategori_majo AS c ON c.id_kategori = a.id_kategori
                
                LEFT JOIN (
                SELECT d.id_produk, SUM(d.debit) AS debit, SUM(d.kredit) AS kredit
                FROM tb_stok_produk AS d 
                GROUP BY d.id_produk
                ) AS d ON d.id_produk = a.id_produk

                LEFT JOIN (
                SELECT e.id_produk , SUM(e.jumlah) AS kredit_penjualan
                FROM tb_pembelian AS e 
                GROUP BY e.id_produk
                )AS e ON e.id_produk = a.id_produk
                
                WHERE a.id_lokasi = '$id_lokasi'"),
                'kategori' => DB::table('tb_kategori_majo')->get(),
                'satuan' => DB::table('tb_satuan_majo')->get(),
                'logout' => $r->session()->get('logout'),
            ];
            return view('opname.buat_opname', $data);
        }
    }

    public function inputOpname(Request $r)
    {
        $id_produk = $r->id_produk_opname;
        $kode_opname = 'OPM' . date('ymd') . strtoupper(Str::random(3));
        foreach ($id_produk as $id) {

            $get_produk = DB::selectOne("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
            FROM tb_produk AS a
            LEFT JOIN tb_satuan_majo AS b ON b.id_satuan = a.id_satuan
            LEFT JOIN tb_kategori_majo AS c ON c.id_kategori = a.id_kategori
            
            LEFT JOIN (
            SELECT d.id_produk, SUM(d.debit) AS debit, SUM(d.kredit) AS kredit
            FROM tb_stok_produk AS d 
            GROUP BY d.id_produk
            ) AS d ON d.id_produk = a.id_produk

            LEFT JOIN (
            SELECT e.id_produk , SUM(e.jumlah) AS kredit_penjualan
            FROM tb_pembelian AS e 
            GROUP BY e.id_produk
            )AS e ON e.id_produk = a.id_produk
            
            WHERE a.id_produk = '$id'");

            $data = [
                'kode_stok_produk' => $kode_opname,
                'id_produk' => $get_produk->id_produk,
                'stok_program' => $get_produk->debit - ($get_produk->kredit + $get_produk->kredit_penjualan),
                'stok_aktual' => $get_produk->debit - ($get_produk->kredit + $get_produk->kredit_penjualan),
                'harga' => $get_produk->harga,
                'catatan' => '',
                'status' => 'Draft',
                'tgl' => date('Y-m-d H:i:s'),
                'id_lokasi' => $r->session()->get('id_lokasi'),
                'ket' => 'opname'
            ];
            DB::table('tb_stok_produk')->insert($data);
        }
        return redirect()->route('detailOpname', ['kode_opname' => $kode_opname]);
    }

    public function detailOpname(Request $r)
    {
        $id_lokasi = $r->session()->get('id_lokasi');
        $kode_opname = $r->kode_opname;
        $data = [
            'title'  => "Detail Opname Produk",

            'cek_status' => DB::table('tb_stok_produk')->where('kode_stok_produk', $kode_opname)->groupBy('kode_stok_produk')->first(),

            'opname' => DB::select("SELECT * FROM tb_stok_produk as o
            left join tb_produk as p on p.id_produk = o.id_produk
            left join tb_kategori_majo as k on k.id_kategori = p.id_kategori
            left join tb_satuan_majo as s on s.id_satuan = p.id_satuan
            where o.kode_stok_produk = '$kode_opname'"),
            'kode_opname' => $kode_opname,

            'produk' => DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
                FROM tb_produk AS a
                LEFT JOIN tb_satuan_majo AS b ON b.id_satuan = a.id_satuan
                LEFT JOIN tb_kategori_majo AS c ON c.id_kategori = a.id_kategori
                
                LEFT JOIN (
                SELECT d.id_produk, SUM(d.debit) AS debit, SUM(d.kredit) AS kredit
                FROM tb_stok_produk AS d 
                GROUP BY d.id_produk
                ) AS d ON d.id_produk = a.id_produk

                LEFT JOIN (
                SELECT e.id_produk , SUM(e.jumlah) AS kredit_penjualan
                FROM tb_pembelian AS e 
                GROUP BY e.id_produk
                )AS e ON e.id_produk = a.id_produk
                
                WHERE a.id_lokasi = '$id_lokasi'"),
                'kategori' => DB::table('tb_kategori_majo')->get(),
            'logout' => $r->session()->get('logout'),
        ];
        return view('opname.detail', $data);
    }

    public function editStokAktual(Request $r)
    {
        if ($r->action == 'selesai') {

            $id_opname = $r->id_opname;
            $id_produk = $r->id_produk;
            $stok_aktual = $r->stok_aktual;
            $stok_program = $r->stok_program;
            $catatan = $r->catatan;
            $kode_opname = $r->kode_opname;


            for ($x = 0; $x < sizeof($id_opname); $x++) {
                $selisih = $stok_program[$x] - $stok_aktual[$x];

                if ($selisih < '0') {
                    $data = [
                        'stok_aktual' => $stok_aktual[$x],
                        'catatan' => $catatan[$x],
                        'status' => 'Selesai',
                        'tgl' => date('Y-m-d H:i:s'),
                        'debit' => $selisih * -1
                    ];

                    DB::table('tb_stok_produk')->where('id_stok_produk', $id_opname[$x])->update($data);
                } else {
                    $data = [
                        'stok_aktual' => $stok_aktual[$x],
                        'catatan' => $catatan[$x],
                        'status' => 'Selesai',
                        'tgl' => date('Y-m-d H:i:s'),
                        'kredit' => $selisih
                    ];

                    DB::table('tb_stok_produk')->where('id_stok_produk', $id_opname[$x])->update($data);
                }
            }
        }

        if ($r->action == 'edit') {
            $id_opname = $r->id_opname;
            $id_produk = $r->id_produk;
            $stok_aktual = $r->stok_aktual;
            $catatan = $r->catatan;
            $kode_opname = $r->kode_opname;


            for ($x = 0; $x < sizeof($id_opname); $x++) {
                $data = [
                    'stok_aktual' => $stok_aktual[$x],
                    'catatan' => $catatan[$x]
                ];
                DB::table('tb_stok_produk')->where('id_stok_produk', $id_opname[$x])->update($data);
            }
        }
        return redirect()->route('detailOpname', ['kode_opname' => $kode_opname])->with('sukses', 'Data Berhasil Di Opname');
    }
    public function deleteOpname(Request $r)
    {
        $kode_opname = $r->kode_opname;

        DB::table('tb_stok_produk')->where('kode_stok_produk', $kode_opname)->delete();

        return redirect()->route('opname_majo')->with('error', 'Berhasil hapus data');
    }

    public function printOpname(Request $r)
    {
        $kode_opname = $r->kode_opname;
        $data = [
            'opname' => DB::table('tb_stok_produk')->where('kode_stok_produk', $kode_opname)->groupBy('kode_stok_produk')->first(),
            'detail_opname' => DB::table('tb_stok_produk')->join('tb_produk', 'tb_stok_produk.id_produk', 'tb_produk.id_produk')
                ->join('tb_kategori_majo', 'tb_produk.id_kategori', 'tb_kategori_majo.id_kategori')->join('tb_satuan_majo', 'tb_produk.id_satuan', 'tb_satuan_majo.id_satuan')
                ->where('tb_stok_produk.kode_stok_produk', $kode_opname)->get(),
            'kode_opname' => $kode_opname
        ];
        return view('opname.print', $data);
    }
    public function formulirOpname(Request $r)
    {
        $kode_opname = $r->kode_opname;
        $id_lokasi = $r->session()->get('id_lokasi');
        $data = array(
            'title'  => "Detail Opname Produk",
            'cek_status' => DB::table('tb_opname')->where('kode_opname', $kode_opname)->groupBy('kode_opname')->first(),
            // 'produk'   => $this->db->join('tb_kategori', 'tb_produk.id_kategori = tb_kategori.id_kategori', 'left')->get('tb_produk')->result(),
            // 'kategori'    => $this->db->get('tb_kategori')->result(),
            
             'opname' => DB::table('tb_stok_produk')->join('tb_produk', 'tb_stok_produk.id_produk', 'tb_produk.id_produk')
                ->join('tb_kategori_majo', 'tb_produk.id_kategori', 'tb_kategori_majo.id_kategori')->join('tb_satuan_majo', 'tb_produk.id_satuan', 'tb_satuan_majo.id_satuan')
                ->where('tb_stok_produk.kode_stok_produk', $kode_opname)->get(),
                
             
                
            'kode_opname' => $kode_opname,
            'produk' => DB::select("SELECT a.*,b.*,c.* FROM tb_produk as a
            LEFT JOIN tb_kategori_majo as b ON a.id_kategori = b.id_kategori
            LEFT JOIN tb_satuan_majo as c ON a.id_satuan = c.id_satuan
            WHERE a.id_lokasi = '$id_lokasi'
            ORDER BY a.nm_produk ASC"),
            'kategori' => DB::table('tb_kategori_majo')->get(),
        );

        return view('opname.formulir', $data);
    }
    
    public function inputOpname2(Request $r)
    {
        $id_produk = $r->id_produk_opname;
        $kode_opname = $r->kode_opname;
        foreach ($id_produk as $id) {

            $get_produk = DB::selectOne("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
            FROM tb_produk AS a
            LEFT JOIN tb_satuan_majo AS b ON b.id_satuan = a.id_satuan
            LEFT JOIN tb_kategori_majo AS c ON c.id_kategori = a.id_kategori
            
            LEFT JOIN (
            SELECT d.id_produk, SUM(d.debit) AS debit, SUM(d.kredit) AS kredit
            FROM tb_stok_produk AS d 
            GROUP BY d.id_produk
            ) AS d ON d.id_produk = a.id_produk

            LEFT JOIN (
            SELECT e.id_produk , SUM(e.jumlah) AS kredit_penjualan
            FROM tb_pembelian AS e 
            GROUP BY e.id_produk
            )AS e ON e.id_produk = a.id_produk
            
            WHERE a.id_produk = '$id'");
            
     
            $cek = DB::table('tb_stok_produk')->where('kode_stok_produk',$kode_opname)->where('id_produk',$id)->first();

            if ($cek) {
                continue;
            }else{
                $data = [
                'kode_stok_produk' => $kode_opname,
                'id_produk' => $get_produk->id_produk,
                'stok_program' => $get_produk->debit - ($get_produk->kredit + $get_produk->kredit_penjualan),
                'stok_aktual' => $get_produk->debit - ($get_produk->kredit + $get_produk->kredit_penjualan),
                'harga' => $get_produk->harga,
                'catatan' => '',
                'status' => 'Draft',
                'tgl' => date('Y-m-d H:i:s'),
                'ket' => 'opname'
            ];
            DB::table('tb_stok_produk')->insert($data);
            }

            
        }
        return redirect()->route('detailOpname', ['kode_opname' => $kode_opname]);
    }
}
