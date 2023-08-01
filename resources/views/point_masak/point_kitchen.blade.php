@extends('template.master')
@section('content')
<?php $l = 1; 
$point = 0;
$point2 = 0;
$orang = 0;
foreach ($masak as $k) : ?>
<?php $orang = $l++ ?>
<?php $point += $k->point_berhasil + $k->point_gagal ?>



<?php  endforeach ?>
<?php $orang2 = !$orang ? '0': $orang ?>
<div class="content-wrapper" style="min-height: 511px;">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2 justify-content-center">
                <div class="col-sm-12">
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <?php $service_charge = $service->total * 0.07; ?>
    <?php $kom =  (((($service_charge  / 7 ) * $persen->jumlah_persen) / $jumlah_orang->jumlah)  * $orang2)  ?>
    <!-- Main content -->
    <div class="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs mb-2" id="custom-tabs-two-tab" role="tablist">

                        <li class="nav-item">
                            <a class="nav-link <?= $id_lokasi == 1 ? 'active btn-info' : '' ?>"
                                href="<?= route('point_kitchen') ?>?id_lokasi=1&tgl1={{$tgl1}}&tgl2={{$tgl2}}">Takemori</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $id_lokasi == 2 ? 'active btn-info' : '' ?>"
                                href="<?= route('point_kitchen') ?>?id_lokasi=2&tgl1={{$tgl1}}&tgl2={{$tgl2}}">Soondobu</a>
                        </li>

                    </ul>
                </div>
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>Org p/r :
                                <?= number_format($jumlah_orang->jumlah,0) ?> /
                                <?= number_format($orang,0) ?>
                                <!--({{$service_charge}})-->
                            </h5>
                            <h5>Service Charge Dibagi :
                                <?= number_format(($service_charge / 7) * $persen->jumlah_persen,0) ?>
                            </h5>
                            @if($jumlah_orang->jumlah < $orang) @else <h5>Service charge real :

                                <?= number_format($kom,0) ?>
                                </h5>
                                @endif

                        </div>
                        <div class="col-lg-6">
                            <a href="{{ route('point_export_server')}}?id_lokasi={{$id_lokasi}}&tgl1={{$tgl1}}&tgl2={{$tgl2}}"
                                class="btn btn-info float-right btn-sm "><i class="fas fa-file-excel"></i>
                                Export Kitchen</a>
                            <a href="{{ route('Export_gaji_server')}}?id_lokasi={{$id_lokasi}}&tgl1={{$tgl1}}&tgl2={{$tgl2}}"
                                class="btn btn-info float-right btn-sm mr-2"><i class="fas fa-file-excel"></i>
                                Export Server</a>
                            <a href="" data-target="#view" data-toggle="modal"
                                class="btn btn-info float-right btn-sm mr-2"><i class="fas fa-eye"></i> View</a>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 style="font-weight: bold">
                                        Point masak
                                        <?= tanggal($tgl1) ?> ~
                                        <?= tanggal($tgl2) ?>
                                    </h5>


                                </div>

                                <div class="card-body">

                                    <table width="100%" class="table " id="table" style="font-size: 11px">
                                        <thead style="white-space: nowrap; ">
                                            <tr>
                                                <th>#</th>
                                                <th style="font-size: 10px;text-align: center">Nama
                                                    {{number_format($point,1)}}</th>
                                                <th style="font-size: 10px;text-align: right">M</th>
                                                <th style="font-size: 10px;text-align: right">Gaji</th>
                                                <th style="font-size: 10px;text-align: right">Point <br> Masak </th>
                                                <th style="font-size: 10px;text-align: right">Non Point <br> Masak</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php $i = 1; foreach ($masak as $k) : ?>
                                            <tr>
                                                <td>
                                                    <?= $i++ ?>
                                                </td>
                                                <td>
                                                    <a href="#" id_lokasi="{{Request::get('id_lokasi')}}"
                                                        tgl1="{{$tgl1}}" tgl2="{{$tgl2}}" id="detailPoint"
                                                        data-target="#viewDetail" data-toggle="modal"
                                                        id_karyawan={{$k->id_karyawan}}>
                                                        <?= $k->nama ?>
                                                    </a>
                                                </td>
                                                <td style="text-align: right">
                                                    <?= number_format($k->rp_m,0) ?>
                                                </td>
                                                <?php $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp)  ?>
                                                <td style="text-align: right">
                                                    <?= number_format($gaji,0) ?>
                                                </td>
                                                <?php $kom1 = empty($point) ? '0' :  ($k->point_berhasil / $point) * $kom  ?>

                                                <td style="text-align: right">
                                                    <?= number_format($k->point_berhasil,1) ?>
                                                    <?= $jumlah_orang->jumlah < $orang ? '' : '/' .  number_format($kom1,0) ?>
                                                    <!--({{$k->point_berhasil}}) ({{$point}}) ({{$kom}})-->
                                                </td>
                                                <?php $kom3 =  empty($point) ? '0' : ($k->point_gagal / $point) * $kom  ?>

                                                <td style="text-align: right">
                                                    <?= number_format($k->point_gagal,0) ?>
                                                    <?= $jumlah_orang->jumlah < $orang ? '' : '/' . number_format($kom3,0) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 style="font-weight: bold">
                                        Total Absen
                                        <?= tanggal($tgl1) ?> ~
                                        <?= tanggal($tgl2) ?>
                                    </h5>
                                </div>
                                <div class="card-body">

                                    <table width="100%" class="table " id="table10" style="font-size: 11px">
                                        <thead style="white-space: nowrap; ">
                                            <tr>
                                                <th>#</th>
                                                <th style="font-size: 10px;text-align: center">Nama</th>
                                                <th style="font-size: 10px;text-align: right">M</th>
                                                <th style="font-size: 10px;text-align: right">E</th>
                                                <th style="font-size: 10px;text-align: right">SP</th>
                                                <th style="font-size: 10px;text-align: right">Rp M</th>
                                                <th style="font-size: 10px;text-align: right">Gaji</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php $i = 1; foreach ($absen as $k) : ?>
                                            <tr>
                                                <td>
                                                    <?= $i++ ?>
                                                </td>
                                                <td>
                                                    <?= $k->nama ?>
                                                </td>
                                                <td style="text-align: right">
                                                    <?= number_format($k->qty_m,0) ?>
                                                </td>
                                                <td style="text-align: right">
                                                    <?= number_format($k->qty_e,0) ?>
                                                </td>
                                                <td style="text-align: right">
                                                    <?= number_format($k->qty_sp,0) ?>
                                                </td>
                                                {{-- <td style="text-align: right">
                                                    <?= number_format($k->qty_e+ $k->qty_m+$k->qty_sp,0) ?>
                                                </td> --}}
                                                <td style="text-align: right">
                                                    <?= number_format($k->rp_m,0) ?>
                                                </td>
                                                <?php $gaji = ($k->rp_m * $k->qty_m) + ($k->rp_e * $k->qty_e) + ($k->rp_sp * $k->qty_sp)  ?>
                                                <td style="text-align: right">
                                                    <?= number_format($gaji,0) ?>
                                                </td>


                                            </tr>
                                            <?php endforeach ?>
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

<form action="" method="get">
    <div class="modal fade" role="dialog" id="view" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">View Point</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="">Dari</label>
                            <input class="form-control" type="date" name="tgl1">
                            <input class="form-control" type="hidden" name="id_lokasi" value="{{$id_lokasi}}">
                        </div>
                        <div class="col-lg-6">
                            <label for="">Sampai</label>
                            <input class="form-control" type="date" name="tgl2">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Search</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="" method="get">
    <div class="modal fade" role="dialog" id="viewDetail" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div id="viewDetailPoint"></div>

        </div>
    </div>
</form>

@endsection
@section('script')
<script>
    $(document).ready(function () {
        $(document).on('click', '#detailPoint', function(){
            var id_karyawan = $(this).attr('id_karyawan')
            var id_lokasi = $(this).attr('id_lokasi')
            var tgl1 = $(this).attr('tgl1')
            var tgl2 = $(this).attr('tgl2')

            $.ajax({
                type: "GET",
                url: "{{route('detailPoint')}}",
                data: {
                    id_karyawan: id_karyawan,
                    id_lokasi: id_lokasi,
                    tgl1: tgl1,
                    tgl2: tgl2,
                },
                success: function (data) {
                    $("#viewDetailPoint").html(data);
                    $('#tableDetail').DataTable({

                    "bSort": true,
                    // "scrollX": true,
                    "paging": true,
                    "stateSave": true,
                    "scrollCollapse": true
                    });
                }
            });
        })

        $("#exportDetailPoint").click(function (e) { 
            e.preventDefault();
            alert(1)
        });
    });
</script>
@endsection