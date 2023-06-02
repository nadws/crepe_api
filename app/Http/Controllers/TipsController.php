<?php

namespace App\Http\Controllers;

use App\Models\Tips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TipsController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user',$id_user)
        ->where('id_menu', 8)->first();
        
        if(empty($request->tgl1)){
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-t');
        }else{
            $tgl1 = $request->tgl1;
            $tgl2 = $request->tgl2;
        }
        if(empty($id_menu)) {
            return back();
        } else {

            $data = [
                'title' => 'Data Tips',
                'karyawan' => DB::table('tb_karyawan')->where('id_status',2)->get(),
                'logout' => $request->session()->get('logout'),
                'tips' => DB::select("SELECT * FROM tb_tips as a left join tb_karyawan as b on b.id_karyawan = a.id_karyawan
                where a.tgl between '$tgl1' and '$tgl2'
                "),
                'tgl1' => $tgl1,
                'tgl2' => $tgl2
            ];
    
            return view('tips.tips', $data);
        }
    }

    public function addTips(Request $request)
    {
        
        $data = [
            'tgl' => $request->tgl,
            'nominal' => $request->nominal,
            'id_karyawan' => $request->id_karyawan,
        ];
        DB::table('tb_tips')->insert($data);
        return redirect()->route('tips')->with('sukses', 'Berhasil tambah tips');
    }

    public function editTips(Request $request)
    {
        $data = [
            'nominal' => $request->nominal,
        ];
        Tips::where('id_tips',$request->id_tips)->update($data);
        return redirect()->route('tips')->with('sukses', 'Berhasil ubah tips');
    }

    public function deleteTips(Request $request)
    {
        Tips::where('id_tips', $request->id_tips)->delete();
        return redirect()->route('tips')->with('error', 'Berhasil hapus tips');
    }
}
