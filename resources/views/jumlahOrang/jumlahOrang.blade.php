@extends('template.master')
@section('content')

<div class="content-wrapper" style="min-height: 511px;">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2 justify-content-center">
                <div class="col-sm-12">

                    <h4 style="color: rgb(120, 120, 120); font-weight: bold; --darkreader-inline-color:#837e75;"
                        data-darkreader-inline-color="">Setting Point</h4>


                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h5 class="mt-2">Level Point</h5>
                                </div>
                                <div class="col-lg-6">
                                    <a href="" class="btn btn-info float-right" data-target="#tambahPoint1" data-toggle="modal"><i
                                    class="fas fa-plus"></i> Tambah data</a>
                                </div>
                            </div>
                            
                        </div>
                        <div class="card-body">
                            <table class="table " id="point1">
                                <ul class="nav nav-tabs mb-2" id="custom-tabs-two-tab" role="tablist">
                                    
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 1 ? 'active btn-info' : '' ?>"
                                            href="<?= route('setOrang') ?>?id_lokasi=1">Takemori</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 2 ? 'active btn-info' : '' ?>"
                                            href="<?= route('setOrang') ?>?id_lokasi=2">Soondobu</a>
                                    </li>
                            
                                </ul>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Level</th>
                                        <th>Point</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                        $handicap = DB::table('tb_handicap')->where('id_lokasi',$id_lokasi)->orderBy('id_handicap', 'DESC')->get();
                                    @endphp
                                    @foreach ($handicap as $k)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $k->handicap }}</td>
                                        <td>{{ $k->point }}</td>
                                        <td>{{ $k->ket }}</td>
                                        <td><a href="#" data-target="#edit1<?= $k->id_handicap ?>" data-toggle="modal"
                                            class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> </a>
                                        <a onclick="rerutn confirm('Apakah yakin dihapus ?)" href="{{ route('hapusHandicap', ['id_handicap' => $k->id_handicap, 'id_lokasi' => $id_lokasi]) }}" class="btn btn-danger btn-sm"><i
                                                class="fas fa-trash"></i> </a>
                                    </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                        <div class="card-header">
                            <h5>Setting Jumlah Orang</h5>
                        </div>
                        <div class="card-body">
                            <table class="table " id="">
                                <ul class="nav nav-tabs mb-2" id="custom-tabs-two-tab" role="tablist">
                                    
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 1 ? 'active btn-info' : '' ?>"
                                            href="<?= route('setOrang') ?>?id_lokasi=1">Takemori</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 2 ? 'active btn-info' : '' ?>"
                                            href="<?= route('setOrang') ?>?id_lokasi=2">Soondobu</a>
                                    </li>
                            
                                </ul>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Karyawan</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($jumlahOrang as $k)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $k->ket_karyawan }}</td>
                                        <td>{{ $k->jumlah }}</td>
                                        <td><a href="#" data-target="#edit<?= $k->id_orang ?>" data-toggle="modal"
                                            class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> </a>
                             
                                    </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                        <div class="card-header">
                           <h5>Persentase Komisi</h5>
                        </div>
                        <div class="card-body">
                            <table class="table " id="">
                                <ul class="nav nav-tabs mb-2" id="custom-tabs-two-tab" role="tablist">
                                    
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 1 ? 'active btn-info' : '' ?>"
                                            href="<?= route('setOrang') ?>?id_lokasi=1">Takemori</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 2 ? 'active btn-info' : '' ?>"
                                            href="<?= route('setOrang') ?>?id_lokasi=2">Soondobu</a>
                                    </li>
                            
                                </ul>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Persentase</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($persen as $p)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $p->nama_persentase }}</td>
                                        <td>{{ $p->jumlah_persen }}</td>
                                        <td><a href="#" data-target="#edit_persen<?= $p->id_persentase ?>" data-toggle="modal"
                                            class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> </a>
                             
                                    </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                        <div class="card-header">
                           <h5>Setting Menit</h5>
                        </div>
                        <div class="card-body">
                            <table class="table " id="">
                                <ul class="nav nav-tabs mb-2" id="custom-tabs-two-tab" role="tablist">
                                    
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 1 ? 'active btn-info' : '' ?>"
                                            href="<?= route('setOrang') ?>?id_lokasi=1">Takemori</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 2 ? 'active btn-info' : '' ?>"
                                            href="<?= route('setOrang') ?>?id_lokasi=2">Soondobu</a>
                                    </li>
                            
                                </ul>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Menit</th>
                                        <th>Menit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($menit as $p)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $p->nm_menit }}</td>
                                        <td>{{ $p->menit }}</td>
                                        <td><a href="#" data-target="#edit_menit<?= $p->id_menit ?>" data-toggle="modal"
                                            class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> </a>
                             
                                    </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<?php  foreach($jumlahOrang as $t): ?>
<form action="{{route('editSetOrang')}}" method="post" accept-charset="utf-8">
    @csrf
    <div class="modal fade" id="edit<?= $t->id_orang ?>" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Edit data</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id_lokasi" value="{{ $id_lokasi }}">
                        <input type="hidden" name="id_orang" value="{{ $t->id_orang }}">
                        <div class="col-lg-6">
                            <label for="">Keterangan</label>
                           <input type="text" class="form-control" value="{{ $t->ket_karyawan }}" name="ket">
                            
                        </div>
                        <div class="col-lg-6">
                            <label for="">Jumlah</label>
                           <input type="text" class="form-control" value="{{ $t->jumlah }}" name="jumlah">
                            
                        </div>

                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endforeach ?>
<?php  foreach($persen as $p): ?>
<form action="{{route('edit_persen')}}" method="post" accept-charset="utf-8">
    @csrf
    <div class="modal fade" id="edit_persen<?= $p->id_persentase ?>" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Edit data</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id_persentase" value="{{ $p->id_persentase }}">
                        <input type="hidden" name="id_lokasi" value="{{ $id_lokasi }}">
                        <div class="col-lg-6">
                            <label for="">Nama</label>
                           <input type="text" class="form-control" value="{{ $p->nama_persentase }}"  readonly>
                            
                        </div>
                        <div class="col-lg-6">
                            <label for="">Persentase</label>
                           <input type="text" class="form-control" value="{{ $p->jumlah_persen }}" name="jumlah_persen">
                            
                        </div>

                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endforeach ?>
<?php  foreach($menit as $p): ?>
<form action="{{route('edit_menit')}}" method="post" accept-charset="utf-8">
    @csrf
    <div class="modal fade" id="edit_menit<?= $p->id_menit ?>" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Edit data</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id_menit" value="{{ $p->id_menit }}">
                        <input type="hidden" name="id_lokasi" value="{{ $id_lokasi }}">
                        <div class="col-lg-6">
                            <label for="">Nama Menit</label>
                           <input type="text" class="form-control" value="{{ $p->nm_menit }}"  readonly>
                            
                        </div>
                        <div class="col-lg-6">
                            <label for="">Menit</label>
                           <input type="text" class="form-control" value="{{ $p->menit }}" name="menit">
                            
                        </div>

                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endforeach ?>

<form action="{{route('tbhHenKategori')}}" method="post" accept-charset="utf-8">
    @csrf
    <div class="modal fade" id="tambahPoint1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Tambah data</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id_lokasi" value="{{$id_lokasi}}">
                    <div class="row">
                        <input type="hidden" name="id_lokasi" value="{{ $id_lokasi }}">
                        <div class="col-lg-3">
                            <label for="">Level</label>
                           <input type="text" class="form-control" name="handicap">
                        </div>
                        <div class="col-lg-6">
                            <label for="">Keterangan</label>
                           <input type="text" class="form-control" name="ket">
                        </div>
                        <div class="col-lg-3">
                            <label for="">Point</label>
                            <input required type="number" name="point" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php  foreach($handicap as $t): ?>
<form action="{{route('editHenKategori')}}" method="post" accept-charset="utf-8">
    @csrf
    <div class="modal fade" id="edit1<?= $t->id_handicap ?>" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Edit data</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id_lokasi" value="{{ $id_lokasi }}">
                        <input type="hidden" name="id_handicap" value="{{ $t->id_handicap }}">
                        <div class="col-lg-3">
                            <label for="">Handicap</label>
                           <input type="text" class="form-control" value="{{ $t->handicap }}" name="handicap">
                            
                        </div>
                        <div class="col-lg-6">
                            <label for="">Keterangan</label>
                           <input type="text" class="form-control" value="{{ $t->ket }}" name="ket">
                            
                        </div>
                        <div class="col-lg-3">
                            <label for="">Point</label>
                            <input required type="number" name="point" value="{{ $t->point }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endforeach ?>

<form action="{{route('tambah_ket')}}" method="post" accept-charset="utf-8">
    @csrf
    <div class="modal fade" id="tambahPoint2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Tambah data</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id_lokasi" value="{{$id_lokasi}}">
                    <div class="row">
                        <div class="col-lg-8">
                            <label for="">Keterangan kerja</label>
                            <input type="text" class="form-control" name="ket">
                        </div>
                        <div class="col-lg-4">
                            <label for="">Point</label>
                            <input required type="number" name="point" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>



@endsection
@section('script')
<script>
$('#point1').DataTable({

"bSort": true,
"scrollY": true,
"paging": true,
"stateSave": true,
"scrollCollapse": true
});
$('#point2').DataTable({

"bSort": true,
"scrollY": true,
"paging": true,
"stateSave": true,
"scrollCollapse": true
});
</script>
@endsection