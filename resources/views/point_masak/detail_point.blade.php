<div class="modal-content ">
    <div class="modal-header btn-costume">
        <h5 class="modal-title text-light" id="exampleModalLabel">Detail Point {{ $nm_karyawan }}</h5>
        <a href="{{ route('exportDetailPoint', ['id_karyawan' => $id_karyawan, 'id_lokasi' => $id_lokasi, 'tgl1' => $tgl1, 'tgl2' => $tgl2]) }}" id="exportDetailPoint" class="btn btn-sm btn-info float-right ml-2"><i class="fa fa-file-excel"></i> Export</a>
        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        
    </div>
    <div class="modal-body">
        <table width="100%" class="table" id="tableDetail">
            <thead>
                @php
                    $tpoint = 0;
                    foreach($point as $p) {
                        $point_menu = $p->nilai_koki;
                        if($p->point_menu == null) {
                            $point_menu = 0;
                        }
                        $tpoint += $point_menu;
                    }
                @endphp
                <tr>
                    <th>#</th>
                    <th>No Order</th>
                    <th>Nama Menu</th>
                    <th>Point ({{ number_format($tpoint,1) }})</th>
                    <th>Lama Masak</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($point as $p)  
                @if ($p->point_menu != '')
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $p->no_order }}</td>
                    <td>{{ $p->nm_menu }}</td>
                    <td>{{ $p->tipe == 'drink' ? $p->point_menu / 2 : $p->point_menu }} ({{$p->point_menu}})</td>
                    <td>{{$p->lama_masak}} Menit</td>
                </tr>
                @else
                @php
                    continue;
                @endphp
                @endif   
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Search</button>
    </div>
</div>
