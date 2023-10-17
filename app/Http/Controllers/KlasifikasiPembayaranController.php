<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KlasifikasiPembayaranController extends Controller
{
    function index(Request $r)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 249)->first();

        if (empty($id_menu)) {
            return back();
        } else {
            if (Auth::user()->jenis == 'adm') {
                $data = [
                    'title' => 'Data Pembayaran',
                    'logout' => $r->session()->get('logout'),
                    'klasifikasi' => DB::table('klasifikasi_pembayaran')->get(),
                    'akun_pembayaran' => DB::table('akun_pembayaran')->where('aktif', 'T')->get(),
                ];
                $groupedAkunPembayaran = [];
                foreach ($data['akun_pembayaran'] as $akun) {
                    $groupedAkunPembayaran[$akun->id_klasifikasi][] = $akun;
                }

                $data['groupedAkunPembayaran'] = $groupedAkunPembayaran;
                return view("pembayaran.index", $data);
            } else {
                return back();
            }
        }
    }

    function sub_klasifikasi(Request $r)
    {
        $data = ['akun_pembayaran' => DB::table('akun_pembayaran')->where('id_klasifikasi', $r->id_klasifikasi)->get(),];
        return view('pembayaran.sub_klasifikasi', $data);
    }

    function save_akun_klasifikasi(Request $r)
    {
        $data = [
            'nm_akun' => $r->nm_akun,
            'id_klasifikasi' => $r->id_klasifikasi,
        ];
        DB::table('akun_pembayaran')->insert($data);
    }

    function save_klasifikasi(Request $r)
    {
        $data = [
            'nm_klasifikasi' => $r->nm_klasifikasi,
        ];
        DB::table('klasifikasi_pembayaran')->insert($data);
        return back();
    }

    function delete_klasifikasi(Request $r)
    {
        DB::table('klasifikasi_pembayaran')->where('id_klasifikasi_pembayaran', $r->id_klasifikasi)->update(['aktif' => 'Y']);
        return back();
    }
}
