<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_lokasi = $request->session()->get('id_lokasi');
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 12)->first();
        if (empty($id_menu)) {
            return back();
        } else {

            $data = [
                'title' => 'Produk Majo',
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

                'logout' => $request->session()->get('logout'),
            ];
            return view("produk.index", $data);
        }
    }
    
    public function stok_adm(Request $request)
    {
         $data = [
                  'title' => 'Gaji',
                  'logout' => $request->session()->get('logout'),
                  'produk_tkm' => DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
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
                
                WHERE a.id_lokasi = '1'"),
                
                  'produk_sdb' => DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
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
                
                WHERE a.id_lokasi = '2'"),
                ];
        return view('produk.stk_adm', $data);  
    }

    public function tbh_produk_majo(Request $request)
    {

        $data = [
            'nm_produk' => $request->nm_produk,
            'id_kategori' => $request->id_kategori,
            'id_satuan' => $request->id_satuan,
            'stok' => $request->stok,
            'harga_modal' => $request->harga_modal,
            'harga' => $request->harga,
            'komisi' => $request->komisi,
            'id_lokasi' =>  $request->session()->get('id_lokasi')
        ];
        $sku = Produk::create($data);

        Produk::where('id_produk', $sku->id)->update(['sku' => 'TS' . $sku->id]);

        return redirect()->route('produk')->with('sukses', 'Berhasil tambah Produk');
    }

    public function delete(Request $r)
    {
        Produk::where('id_produk', $r->id_produk)->delete();
        return redirect()->route('produk')->with('error', 'Berhasil hapus produk');
    }

    public function get_edit_majo(Request $r)
    {
        $produk = DB::table('tb_produk')->where('id_produk', $r->id_produk)->first();
        $data = [
            'produk' => $produk,
            'kategori' => DB::table('tb_kategori_majo')->get(),
            'satuan' => DB::table('tb_satuan_majo')->get(),
        ];
        return view("produk.edit", $data);
    }

    public function edit(Request $r)
    {
        $data = [
            'nm_produk' => $r->nm_produk,
            'id_kategori' => $r->id_kategori,
            'id_satuan' => $r->id_satuan,
            // 'stok' => $r->stok,
            'harga' => $r->harga,
            'harga_modal' => $r->harga_modal,
            'komisi' => $r->komisi,
        ];
        Produk::where('id_produk', $r->id_produk)->update($data);
        return redirect()->route('produk')->with('sukses', 'Berhasil ubah produk');
    }
}
