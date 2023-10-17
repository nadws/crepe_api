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
                        <div class="card">
                            <div class="card-header">
                                <h5 class="float-left">{{ $title }}</h5>
                                <a href="#" data-toggle="modal" data-target="#tambah_klasifikasi"
                                    class="btn btn-sm btn-info float-right"><i class="fas fa-plus"></i> Tambah
                                </a>
                            </div>
                            @include('flash.flash')
                            <div class="card-body">
                                <table class="table table-bordered" id="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Klasifikasi Pembayaran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($klasifikasi as $k)
                                            <tr>

                                                <td style="font-weight: bold;" colspan="2">
                                                    <a href="#" data-toggle="modal" data-target="#tambah"
                                                        class="tambah"
                                                        id_klasifikasi="{{ $k->id_klasifikasi_pembayaran }}">{{ $k->nm_klasifikasi }}</a>
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm btn-buka float-right btn_buka buka{{ $k->id_klasifikasi_pembayaran }}"
                                                        id_klasifikasi="{{ $k->id_klasifikasi_pembayaran }}">
                                                        <i class="fas fa-caret-down"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm btn-buka float-right btn_tutup tutup{{ $k->id_klasifikasi_pembayaran }}"
                                                        hidden id_klasifikasi="{{ $k->id_klasifikasi_pembayaran }}">
                                                        <i class="fas fa-caret-up"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-warning btn-sm"><i
                                                            class="fas fa-edit"></i></a>
                                                    <a href="{{ route('delete_klasifikasi', ['id_klasifikasi' => $k->id_klasifikasi_pembayaran]) }}"
                                                        class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                    <tbody class="load_sub{{ $k->id_klasifikasi_pembayaran }}"></tbody>
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

    <form id="save_akun_pembayaran">
        <div class="modal fade" id="tambah" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg-max" role="document">
                <div class="modal-content ">
                    <div class="modal-header btn-costume">
                        <h5 class="modal-title text-light" id="exampleModalLabel">Tambah Akun Pembayaran</h5>
                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="">Nama Akun Pembayaran</label>
                                <input type="text" class="form-control" name="nm_akun">
                                <input type="hidden" id="id_klasifikasi" name="id_klasifikasi">
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
    <form action="{{ route('save_klasifikasi') }}" method="post">
        @csrf
        <div class="modal fade" id="tambah_klasifikasi" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg-max" role="document">
                <div class="modal-content ">
                    <div class="modal-header btn-costume">
                        <h5 class="modal-title text-light" id="exampleModalLabel">Tambah Akun Klasifikasi</h5>
                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="">Nama Klasifikasi</label>
                                <input type="text" class="form-control" name="nm_klasifikasi">
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
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
@endsection
@section('script')
    <script>
        function sub_menu(id_klasifikasi) {
            $.ajax({
                type: "get",
                url: "{{ route('sub_klasifikasi') }}",
                data: {
                    id_klasifikasi: id_klasifikasi
                },
                success: function(r) {
                    $('.load_sub' + id_klasifikasi).html(r)

                }
            });
        }
        $('.btn_buka').click(function() {
            var id_klasifikasi = $(this).attr('id_klasifikasi');
            sub_menu(id_klasifikasi);
            $('.load_sub' + id_klasifikasi).show();
            $('.buka' + id_klasifikasi).attr('hidden', 'hidden');
            $('.tutup' + id_klasifikasi).removeAttr('hidden');
        });
        $('.btn_tutup').click(function() {
            var id_klasifikasi = $(this).attr('id_klasifikasi');
            $('.load_sub' + id_klasifikasi).hide();
            $('.tutup' + id_klasifikasi).attr('hidden', 'hidden');
            $('.buka' + id_klasifikasi).removeAttr('hidden');
        });
        $('.tambah').click(function() {
            var id_klasifikasi = $(this).attr('id_klasifikasi');
            $("#id_klasifikasi").val(id_klasifikasi)

        });

        $(document).on('submit', '#save_akun_pembayaran', function(e) {
            e.preventDefault()
            var data = $("#save_akun_pembayaran").serialize()
            var id_klasifikasi = $('#id_klasifikasi').val();
            $.ajax({
                type: "GET",
                url: "{{ route('save_akun_klasifikasi') }}?" + data,
                success: function(response) {
                    sub_menu(id_klasifikasi);
                    $('#tambah').modal('hide');
                    $('.load_sub' + id_klasifikasi).show();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        title: 'Data berhasil disimpan'
                    });
                }
            });
        });
    </script>
@endsection
