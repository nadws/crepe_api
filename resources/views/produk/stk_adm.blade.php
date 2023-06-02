@extends('template.master')
@section('content')
<div class="content-wrapper">
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

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    @include('flash.flash')
                    <div class="card">
                        <div class="card-header">
                            <h5>Data Produk Takemori</h5>
                        </div>

                        <div class="card-body">
                            <table class="table  " id="table">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>QTy</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i=1;
                                    @endphp
                                    @foreach ($produk_tkm as $p)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{$p->nm_produk}}</td>
                                        <td>{{$p->debit - ($p->kredit + $p->kredit_penjualan)}}</td>
                                        <td>{{$p->satuan}}</td>
                                    </tr>
                                    @endforeach


                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
                <div class="col-lg-6">
                    @include('flash.flash')
                    <div class="card">
                        <div class="card-header">
                            <h5>Data Produk Soondobu</h5>
                        </div>

                        <div class="card-body">
                            <table class="table  " id="table10">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>QTy</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i=1;
                                    @endphp
                                    @foreach ($produk_sdb as $p)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{$p->nm_produk}}</td>
                                        <td>{{$p->debit - ($p->kredit + $p->kredit_penjualan)}}</td>
                                        <td>{{$p->satuan}}</td>
                                    </tr>
                                    @endforeach


                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>







            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->




<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn_edit', function(event) {
            var id_produk = $(this).attr('id_produk');
            // alert(id_produk);
            $.ajax({
                    url: "{{ route('get_edit_majo') }}?id_produk=" + id_produk,
                    method: "GET",
                
                    success: function(data) {

                    $('#edit_majo').html(data);

                    }
            });
        });
    });
</script>



{{-- ---------------- --}}
@endsection
@section('script')
@endsection