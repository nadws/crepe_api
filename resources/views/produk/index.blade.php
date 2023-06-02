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
                <div class="col-lg-12">
                    @include('flash.flash')
                    <div class="card">
                        <div class="card-header">
                            <h5>Data Produk</h5>

                            <a href="" data-toggle="modal" data-target="#tambah" class="btn btn-info float-right"><i
                                    class="fas fa-plus"></i> Tambah Produk</a>
                        </div>

                        <div class="card-body">
                            <table class="table  " id="table">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>SKU</th>
                                        <th>Kategori</th>
                                        <th>QTy</th>
                                        <th>Satuan</th>
                                        <th>Harga Jual</th>
                                        <th>Komisi</th>
                                        <th>Akses1</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i=1;
                                    @endphp
                                    @foreach ($produk as $p)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{$p->nm_produk}}</td>
                                        <td>{{$p->sku}}</td>
                                        <td>{{$p->nm_kategori}}</td>
                                        <td>{{$p->debit - ($p->kredit + $p->kredit_penjualan)}}</td>
                                        <td>{{$p->satuan}}</td>
                                        <td>{{number_format($p->harga,0)}}</td>
                                        <td>{{$p->komisi}} %</td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#edit"
                                                id_produk="{{$p->id_produk}}" class="btn btn-info btn-sm btn_edit"><i
                                                    class="fas fa-edit"></i>
                                            </a>


                                            <a href="{{ route('delete_majo', ['id_produk' => $p->id_produk]) }}"
                                                onclick="return confirm('Apakah anda yakin ?')"
                                                class="btn btn-sm btn-danger"><i
                                                    class="fas text-light fa-trash-alt"></i>
                                            </a>
                                        </td>
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


<form action="{{ route('tbh_produk_majo') }}" method="post">
    @csrf
    <div class="modal fade" id="tambah" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Tambah Produk</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4 ol-md-6 col-xs-12 mb-2">
                            <label for="">Masukkan Gambar</label>
                            <input type="file" class="dropify" data-height="150" name="image" placeholder="Image">
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group row">

                                <div class="col-lg-4 mb-2">
                                    <label for="">
                                        <dt>Nama Produk</dt>
                                    </label>
                                    <input type="text" name="nm_produk" class="form-control" placeholder="Nama Produk"
                                        required>
                                </div>

                                <div class="col-lg-4 mb-2">
                                    <label for="">
                                        <dt>Kategori</dt>
                                    </label>
                                    <select name="id_kategori" class="form-control select" required>
                                        <option value="">-Pilih Kategori-</option>
                                        @foreach ($kategori as $k)
                                        <option value="{{$k->id_kategori}}">{{$k->nm_kategori}}</option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-lg-4 mb-2">
                                    <label for="">
                                        <dt>Satuan</dt>
                                    </label>
                                    <select name="id_satuan" class="form-control select" id="" required>
                                        <option value="">-Pilih Satuan-</option>
                                        @foreach ($satuan as $s)
                                        <option value="{{$s->id_satuan}}">{{$s->satuan}}</option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-lg-4 mb-2">
                                    <label for="">
                                        <dt>Stok</dt>
                                    </label>
                                    <input type="text" class="form-control" name="stok" placeholder="cth : 1" required>
                                </div>

                                <div class="col-lg-4 mb-2">
                                    <label for="">
                                        <dt>Harga Modal</dt>
                                    </label>
                                    <input type="text" class="form-control" name="harga_modal" placeholder="cth : 5000"
                                        required>
                                </div>

                                <div class="col-lg-4 mb-2">
                                    <label for="">
                                        <dt>Harga Jual</dt>
                                    </label>
                                    <input type="text" class="form-control" name="harga" placeholder="cth : 5000"
                                        required>
                                </div>

                                <div class="col-lg-4 mb-2">
                                    <label for="">
                                        <dt>Komisi</dt>
                                    </label>
                                    <select name="komisi" class="form-control select" id="" required>

                                        <option value="5">5%</option>
                                        <option value="2.5">2.5%</option>
                                        <option value="0">0%</option>


                                    </select>
                                </div>




                            </div>
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

<form action="{{ route('edit_majo') }}" method="post">
    @csrf
    <div class="modal fade" id="edit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Edit Produk</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="edit_majo"></div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Edit/Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

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