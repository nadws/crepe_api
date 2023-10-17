<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Karyawan;
use App\Models\Posisi;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 12)->first();
        if (empty($id_menu)) {
            return back();
        } else {
            if (Auth::user()->jenis == 'adm') {
                $data = [
                    'title' => 'Karyawan',
                    'logout' => $request->session()->get('logout'),
                    'karyawan' =>
                    Karyawan::select('tb_karyawan.*', 'tb_status.nm_status', 'tb_posisi.nm_posisi', 'tb_gaji.*')->join('tb_status', 'tb_karyawan.id_status', '=', 'tb_status.id_status')->join('tb_posisi', 'tb_karyawan.id_posisi', '=', 'tb_posisi.id_posisi')->join('tb_gaji', 'tb_karyawan.id_karyawan', 'tb_gaji.id_karyawan')->orderBy('tgl_masuk', 'desc')->get(),
                    'status' => DB::table('tb_status')->get(),
                    'posisi' => DB::table('tb_posisi')->get(),
                ];
                return view("karyawan.karyawan", $data);
            } else {
                return back();
            }
        }
    }

    public function addKaryawan(Request $r)
    {
        $cek = Karyawan::where('nama', $r->nama)->first();
        if ($cek) {
            return redirect()->route('karyawan')->with('error', 'Gagal! Nama sudah ada');
        } else {
            $data = [
                'nama' => $r->nama,
                'id_status' => $r->status,
                'id_posisi' => $r->posisi,
                'tgl_masuk' => $r->tgl_masuk,
            ];

            $kr = Karyawan::create($data);
            $nm_karyawan = $r->status == '1' ? 'K-' . $r->nama : $r->nama;



            $data2 = [
                'id_karyawan' => $kr->id,
                'rp_m' => $r->rp_m,
                'rp_e' => $r->rp_e,
                'rp_sp' => $r->rp_sp,
                'g_bulanan' => $r->g_bulanan,
            ];
            Gaji::create($data2);

            $data3 = [
                'id_karyawan' => $kr->id,
                'nm_karyawan' => $nm_karyawan,
                'posisi' => 'WAITRESS',
                'pangkat' => 'SERVER'
            ];
            DB::table('tb_karyawan_majo')->insert($data3);
            Http::withHeaders([
                'X-API-KEY' => '@Takemor.'
            ])->get("https://majoo-laravel.putrirembulan.com/api/add_karyawan/$nm_karyawan");

            return redirect()->route('karyawan')->with('sukses', 'Berhasil tambah karyawan');
        }
    }

    public function editKaryawan(Request $request)
    {
        $data = [
            'nama' => $request->nama,
            'id_status' => $request->status,
            'id_posisi' => $request->posisi,
            'tgl_masuk' => $request->tgl_masuk
        ];

        Karyawan::where('id_karyawan', $request->id_karyawan)->update($data);


        $id_gaji = $request->id_gaji;
        $id_karyawan = $request->id_karyawan;
        if (empty($id_gaji || $id_karyawan)) {
            $data = [
                'id_karyawan' => $id_karyawan,
                'rp_m' => $request->rp_m,
                'rp_e' => $request->rp_e,
                'rp_sp' => $request->rp_sp,
                'g_bulanan' => $request->g_bulanan,
            ];
            Gaji::create($data);
        } else {
            $data = [
                'rp_m' => $request->rp_m,
                'rp_e' => $request->rp_e,
                'rp_sp' => $request->rp_sp,
                'g_bulanan' => $request->g_bulanan,
            ];

            Gaji::where('id_gaji', $id_gaji)->update($data);
        }

        return redirect()->route('karyawan')->with('sukses', 'Berhasil Ubah Data Karyawan');
    }

    public function deleteKaryawan(Request $request)
    {
        Karyawan::where('id_karyawan', $request->id_karyawan)->delete();
        Gaji::where('id_karyawan', $request->id_karyawan)->delete();
        DB::table('tb_karyawan_majo')->where('id_karyawan', $request->id_karyawan)->delete();

        return redirect()->route('karyawan')->with('error', 'Berhasil hapus karyawan');
    }

    public function addPoint(Request $request)
    {
        Karyawan::where('id_karyawan', $request->id_karyawan)->update(['point' => $request->value]);
        return redirect()->route('karyawan')->with('error', 'Berhasil add point karyawan');
    }
}
