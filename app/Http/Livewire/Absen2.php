<?php

namespace App\Http\Livewire;

use App\Models\Absen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Absen2 extends Component
{
    public
        $bulan,
        $tahun,
        $valBulan,
        $valTahun,
        $valPosisi,
        $search,
        $tglTerakhir,
        $perPage = 10,
        $openVal;

    public $listBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    public function mount()
    {
        $this->valBulan = (int) date('m');
        $this->valTahun = (int) date('Y');
        $this->valPosisi = 1;
        $this->openVal = (int) date('d');
    }

    public function updatedValBulan($value)
    {
        $this->valBulan = $value;
    }

    public function open()
    {
        if ($this->valBulan != (int)date('m')) {
            $this->openVal = $this->openVal == 1 ? (int) date('t') : 1;
        }
        $this->openVal = $this->openVal == 1 ? (int) date('d') : 1;
    }

    public function getTotal($id_karyawan, $status)
    {
        $tglTerakhir = cal_days_in_month(CAL_GREGORIAN, $this->valBulan, $this->valTahun);
        $c = Absen::whereBetween('tgl', ["$this->valTahun-$this->valBulan-1", "$this->valTahun-$this->valBulan-$tglTerakhir"])->where([
            ['id_karyawan', $id_karyawan],
            ['status', $status],
        ])->count();
        return $c;
    }


    public function clickEdit($id_absen, $status)
    {
        $query = Absen::where('id_absen', $id_absen);

        if ($status == 'OFF') {
            $query->delete();
        }
        $query->update([
            'status' => $status,
        ]);
    }

    public function clickOff($id_karyawan, $tgl)
    {
        $tgl = $this->valTahun . '-' . $this->valBulan . '-' . $tgl;
        Absen::create([
            'id_karyawan' => $id_karyawan,
            'status' => 'M',
            'tgl' => $tgl,
            'id_lokasi' => session()->get('id_lokasi'),
        ]);
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    public function render()
    {
        $query = DB::table('tb_karyawan')->select('nama as nm_karyawan', 'id_karyawan')->where('id_status', $this->valPosisi);
        if (!empty($this->search)) {
            $query->where('nama', 'like', '%' . $this->search . '%');
            $this->perPage = 10;
        }
        $result = $query->paginate($this->perPage);

        $data = [
            'karyawan' => $result,
            'posisi' => DB::table('tb_status')->get()
        ];
        return view('livewire.absen2', $data);
    }
}
