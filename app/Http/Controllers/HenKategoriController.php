<?php

namespace App\Http\Controllers;

use App\Models\Dp;
use App\Models\Handicap;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class HenKategoriController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user',$id_user)
        ->where('id_menu', 239)->first();
        if(empty($id_menu)) {
            return back();
        } else {
            $id_lokasi = $request->id_lokasi == '' ? 1 : $request->id_lokasi;
            $lokasi = $id_lokasi == 1 ? 'TAKEMORI' : 'SOONDOBU';
            
            $data = [
                'title' => 'Level Point',
                'id_lokasi' => $id_lokasi,
                'logout' => $request->session()->get('logout'),
                'kategori' => Kategori::where('lokasi', $lokasi)->orderBy('kd_kategori', 'desc')->get(),
                'handicap' => Handicap::where('id_lokasi', $id_lokasi)->orderBy('id_handicap', 'desc')->get()
            ];
    
            return view('henKategori.henKategori',$data);
        }
        
    }

    public function edit(Request $request)
    {
        $id_lokasi = $request->id_lokasi;
        Handicap::where('id_handicap',$request->id_handicap)->update(['point' => $request->point,'handicap' => $request->handicap,'ket' => $request->ket]);
        return redirect()->route('setOrang', ['id_lokasi' => $request->id_lokasi])->with('success', 'Berhasil ubah point');
    }

    public function tbhHenKategori(Request $request)
    {
        $id_lokasi = $request->id_lokasi == '' ? 1 : $request->id_lokasi;
        // dd($id_lokasi);
        $data = [
            'handicap' => $request->handicap,
            'point' => $request->point,
            'ket' => $request->ket,
            'id_lokasi' => $id_lokasi,
        ];
        Handicap::create($data);
        return redirect()->route('setOrang', ['id_lokasi' => $id_lokasi])->with('success', 'Berhasil tambah Handicap');
    }
    
    public function hapus(Request $request)
    {
        Handicap::where('id_handicap',$request->id_handicap)->delete();
        return redirect()->route('setOrang', ['id_lokasi' => $request->id_lokasi])->with('success', 'Berhasil tambah Handicap');
    }
    
    public function exportMenuLevel(Request $request)
    {
        $id_lokasi = $request->lokasi;
        $lokasiTs = $id_lokasi == 1 ? 'TAKEMORI' : 'SOONDOBU';
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1:D4')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        // lebar kolom
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(13);
        // header text
        $sheet
            ->setCellValue('A1', 'ID KATEGORI')
            ->setCellValue('B1', 'KATEGORI')
            ->setCellValue('C1', 'ID MENU')
            ->setCellValue('D1', 'NAMA MENU')
            ->setCellValue('E1', 'TIPE(FOOD/DRINK)')
            ->setCellValue('F1', 'DINE IN / TAKEAWAY')
            ->setCellValue('G1', 'GOJEK')
            ->setCellValue('H1', 'DELIVERY')
            ->setCellValue('I1', 'ID LEVEL')
            ->setCellValue('J1', 'POINT')
            ->setCellValue('K1', 'ID STATION')
            ->setCellValue('L1', 'STATION');

        $sheet
            ->setCellValue('N1', 'Level Point')
            ->setCellValue('O1', 'Id Level')
            ->setCellValue('P1', 'Level')
            ->setCellValue('Q1', 'Point')
            ->setCellValue('R1', 'Keterangan');
        
        $sheet
            ->setCellValue('T1', 'Station')
            ->setCellValue('U1', 'Id Station')
            ->setCellValue('V1', 'Station');
        
        $sheet
            ->setCellValue('X1', 'Kategori')
            ->setCellValue('Y1', 'Id Kategori')
            ->setCellValue('Z1', 'Kategori');
            
        $level = DB::table('tb_handicap')->where('id_lokasi', $id_lokasi)->get();
        $station = DB::table('tb_station')->where('id_lokasi', $id_lokasi)->get();
        $tbMenu = DB::table('mHandicap as a')->join('tb_station as b', 'a.id_station', 'b.id_station')->where([['a.lokasiMenu', $id_lokasi],['a.aktif', 'on']])->orderBy('a.id_menu', 'DESC')
                        ->get();
        $lom = 2;
        foreach($tbMenu as $t) {
            $sheet
                ->setCellValue('A'.$lom,$t->kd_kategori)
                ->setCellValue('B'.$lom,$t->kategori)
                ->setCellValue('C'.$lom,$t->id_menu)
                ->setCellValue('D'.$lom,$t->nm_menu)
                ->setCellValue('E'.$lom,$t->tipe);
                $h1 = DB::table('tb_harga')->where([['id_menu', $t->id_menu],['id_distribusi', 1]])
                    ->first();
                $h2 = DB::table('tb_harga')->where([['id_menu', $t->id_menu],['id_distribusi', 2]])
                    ->first();
                $h3 = DB::table('tb_harga')->where([['id_menu', $t->id_menu],['id_distribusi', 3]])
                    ->first();
                $sheet->setCellValue('F'.$lom,$h1 != '' ? $h1->harga : '');
                $sheet->setCellValue('G'.$lom,$h2 != '' ? $h2->harga : '');
                $sheet->setCellValue('H'.$lom,$h3 != '' ? $h3->harga : '');
                
                // $sheet->setCellValue('G'.$lom,$h->harga);
                // $sheet->setCellValue('H'.$lom,$h->harga);

                $sheet->setCellValue('I'.$lom,$t->id_handicap)
                ->setCellValue('J'.$lom,$t->point)
                ->setCellValue('K'.$lom,$t->id_station)
                ->setCellValue('L'.$lom,$t->nm_station);
                
            $lom++;
        }
        $kolom = 2;
        foreach ($level as $k) {
            $sheet
                ->setCellValue('O'.$kolom,$k->id_handicap)
                ->setCellValue('P'.$kolom,$k->handicap)
                ->setCellValue('Q'.$kolom,$k->point)
                ->setCellValue('R'.$kolom,$k->ket);
            $kolom++;
        }
        
        $kom = 2;
        foreach ($station as $k) {
            $sheet
                ->setCellValue('U'.$kom,$k->id_station)
                ->setCellValue('V'.$kom,$k->nm_station);
            $kom++;
        }
        $kategori = DB::table('tb_kategori')->where('lokasi', $lokasiTs)->get();
        $ko = 2;
        foreach ($kategori as $k) {
            $sheet
                ->setCellValue('Y'.$ko,$k->kd_kategori)
                ->setCellValue('Z'.$ko,$k->kategori);
            $ko++;
        }

        $writer = new Xlsx($spreadsheet);
        $style = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];
        
        
        // tambah style
        $batas1 = count($tbMenu) + 1;
        $sheet->getStyle('A1:L'.$batas1)->applyFromArray($style);
        $batas = count($level) + 1;
        $sheet->getStyle('O1:R'.$batas)->applyFromArray($style);
        $batas2 = count($station) + 1;
        $sheet->getStyle('U1:V'.$batas2)->applyFromArray($style);
        $batas3 = count($kategori) + 1;
        $sheet->getStyle('Y1:Z'.$batas3)->applyFromArray($style);
        
        

        $sheet->getStyle('I1')->getAlignment()->setHorizontal('center');
        // $sheet->getStyle('M1')->getAlignment()->setHorizontal('center');
        
        $sheet->getStyle('A1')
        ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $sheet->getStyle('I1')
        ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $sheet->getStyle('K1')
        ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

        $merah = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'cd4c4c'
                ]
            ]
        ];
        $sheet->getStyle('A1')->applyFromArray($merah);
        $sheet->getStyle('F1')->applyFromArray($merah);
        $sheet->getStyle('H1')->applyFromArray($merah);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Format Level & Station Menu '.$lokasiTs.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    
    public function importMenuLevel(Request $request)
    {
            // include APPPATH.'third_party/PHPExcel/PHPExcel.php';
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            // $loadexcel = $excelreader->load('excel/'.$this->filename.'.xlsx'); // Load file yang telah diupload ke folder excel
            // $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $lokasi = $request->id_lokasi;
    
    
            $data = array();
            $numrow = 1;
            // cek
            $cek = 0;
            foreach ($sheet as $row) {
    
                if ($row['A'] == "" && $row['B'] == "" && $row['C'] == "" && $row['D'] == "" && $row['E'] == "" && $row['F'] == "" && $row['G'] == "" && $row['H'] == "")
                    continue;
                $numrow++; // Tambah 1 setiap kali looping
            }
            // endcek
        
            
            $kmenu = Menu::orderBy('kd_menu', 'desc')->where('lokasi', $lokasi)->first();

            $kd_menu = $kmenu->kd_menu + 1;

            foreach ($sheet as $row) {
    
                if ($numrow > 1) {
                    
                    $data = [
                        'id_handicap' => $row['I'],
                        'id_station' => $row['K'],
                        'id_kategori' => $row['A'],
                    ];
                    Menu::where('id_menu', $row['C'])->update($data);

                    
                    if($row['O'] == '' && $row['P'] == '' && $row['Q'] == '') {
                        continue;
                    } elseif($row['O'] == '') {
                        $data = [
                            'handicap' => $row['P'],
                            'point' => $row['Q'],
                            'id_lokasi' => $request->lokasi,
                        ];
                        Handicap::create($data);
                    } else {
                        $data = [
                            'handicap' => $row['P'],
                            'point' => $row['Q'],
                        ];
                        Handicap::where('id_handicap', $row['O'])->update($data);
                    }
                    
                    if($row['U'] == '' && $row['V'] == '') {
                        continue;
                    } elseif($row['U'] == '') {
                        $dataS = [
                            'nm_station' => $row['V'],
                            'id_lokasi' => $request->lokasi,
                        ];
                        DB::table('tb_station')->insert($dataS);
                    } else {
                        $dataS = [
                            'nm_station' => $row['V'],
                        ];
                        DB::table('tb_station')->where('id_station', $row['U'])->update($dataS);
                    }
                    
                    if($row['Y'] == '' && $row['Z'] == '') {
                        continue;
                    } elseif($row['V'] == '') {
                        $dataK = [
                            'kategori' => $row['Z'],
                            'lokasi' => $request->lokasi == 1 ? 'TAKEMORI' : 'SOONDOBU',
                        ];
                        DB::table('tb_kategori')->insert($dataK);
                    } else {
                        $dataK = [
                            'kategori' => $row['Z'],
                        ];
                        DB::table('tb_kategori')->where('kd_kategori', $row['Y'])->update($dataK);
                    }
                }
                $numrow++; // Tambah 1 setiap kali looping
            }
    
            return redirect()->route('menu', ['id_lokasi' => 1])->with('sukses', 'Data berhasil Diimport');

        
    }
}
