@extends('template.master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="float-left">Daftar Produk Opname</h3>
                            <a href="{{ route('buatOpname') }}" class="btn btn-costume btn-sm float-right ml-2"><i
                                    class="fa fa-truck-loading"></i> Stok Opname</a>
                            <a href="#" class="btn btn-costume btn-sm float-right ml-2" data-toggle="modal"
                                data-target="#view"><i class="fa fa-eye"></i> view</a>
                        </div>
                        <div class="card-body">
                            <div id="table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-hover" width="100%" id="table">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>TANGGAL</th>
                                                    <th>KODE</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i=1;
                                                foreach ($opname as $d) : ?>
                                                <tr
                                                    onclick="window.location = '{{route('detailOpname', ['kode_opname' => $d->kode_stok_produk])}}'">
                                                    <td>
                                                        <?=$i++;?>
                                                    </td>
                                                    <td>
                                                        <?= date('d M Y', strtotime($d->tgl)) ?>,
                                                        <?= date('H:i', strtotime($d->tgl)) ?>
                                                    </td>
                                                    <td>
                                                        <?= $d->kode_stok_produk ?>
                                                    </td>
                                                    <td>
                                                        <?= $d->status ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
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

{{-- tambah --}}
<form action="" method="GET">
    <div class="modal fade" id="view">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-costume">
                    <h4 class="modal-title text-light">View Data</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="">Dari</label>
                                <input class="form-control" type="date" value="" id="tgl1" name="tgl1">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Sampai</label>
                                <input class="form-control" type="date" value="" id="tgl2" name="tgl2">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-costume">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- --------------------- --}}



@endsection
@section('script')

@endsection