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
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="float-left">Daftar Stok Produk</h3>
                            <a href="{{ route('buatStokProduk') }}" class="btn btn-costume btn-sm float-right ml-2"><i
                                    class="fa fa-truck-loading"></i> Stok Masuk</a>
                            <a href="#" class="btn btn-costume btn-sm float-right ml-2" data-toggle="modal"
                                data-target="#view"><i class="fa fa-eye"></i> view</a>
                        </div>
                        <div class="card-body">
                            <div id="table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-hover" id="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>TANGGAL</th>
                                                    <th>KODE</th>
                                                    <th>STATUS</th>
                                                    <th>JUMLAH BARANG</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i=1;
                                                foreach ($stokProduk as $d) : ?>
                                                <tr
                                                    onclick="window.location = '{{route('detailStokProduk', ['kode' => $d->kode_stok_produk])}}'">
                                                    <td>
                                                        <?= $i++ ?>
                                                    </td>
                                                    <td>
                                                        <?= date('d M Y, H:i', strtotime($d->tgl_input)) ?>
                                                    </td>
                                                    <td>
                                                        <?= $d->kode_stok_produk ?>
                                                    </td>
                                                    <td>
                                                        <?= $d->status ?>
                                                    </td>
                                                    <?php if($d->jenis = 'MASUK'): ?>
                                                    <td>
                                                        <?= $d->debit ?>
                                                    </td>
                                                    <?php else: ?>
                                                    <td>
                                                        <?= $d->kredit ?>
                                                    </td>
                                                    <?php endif; ?>
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
@if (Session::get('sukses'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'success',
        title: "{{Session::get('sukses')}}"
    });
</script>
@endif
@if (Session::get('error'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'error',
        title: "{{Session::get('error')}}"
    });
</script>
@endif
@endsection