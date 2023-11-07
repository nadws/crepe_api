@extends('template.master')
@section('content')
    <style>
        .freeze-cell1_th {
            position: sticky;
            z-index: 30;
            background-color: #F2F7FF;
            top: 0;
            left: 0;
        }
    </style>
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
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-tabs mb-2" id="custom-tabs-two-tab" role="tablist">

                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 1 ? 'active btn-info' : '' ?>"
                                            href="<?= route('penjualan_per_item') ?>?id_lokasi=1&tahun={{ $thn }}">Takemori</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $id_lokasi == 2 ? 'active btn-info' : '' ?>"
                                            href="<?= route('penjualan_per_item') ?>?id_lokasi=2&tahun={{ $thn }}">Soondobu</a>
                                    </li>

                                </ul>
                                <br>
                                <br>
                                <h5 class="float-left">{{ $title }} : Tahun {{ $thn }}</h5>
                                <a href="" data-target="#view" data-toggle="modal"
                                    class="btn btn-info float-right btn-sm ml-2"><i class="fas fa-eye"></i> View</a>
                                <a href="{{ route('export_per_item', ['tahun' => $thn]) }}"
                                    class="btn btn-info btn-sm float-right"><i class="fas fa-file-excel"></i>
                                    Export</a>
                            </div>
                            @include('flash.flash')
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="freeze-cell1_th">#</th>
                                            <th class="freeze-cell1_th">Station</th>
                                            <th class="freeze-cell1_th">Harga</th>
                                            <th class="freeze-cell1_th">Nama Menu</th>
                                            <th class="freeze-cell1_th">1</th>
                                            <th class="freeze-cell1_th">2</th>
                                            <th class="freeze-cell1_th">3</th>
                                            <th class="freeze-cell1_th">4</th>
                                            <th class="freeze-cell1_th">5</th>
                                            <th class="freeze-cell1_th">6</th>
                                            <th class="freeze-cell1_th">7</th>
                                            <th class="freeze-cell1_th">8</th>
                                            <th class="freeze-cell1_th">9</th>
                                            <th class="freeze-cell1_th">10</th>
                                            <th class="freeze-cell1_th">11</th>
                                            <th class="freeze-cell1_th">12</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penjualan as $no => $p)
                                            <tr>
                                                <td>{{ $no + 1 }}</td>
                                                <td>{{ $p->Station }}</td>
                                                <td>{{ number_format($p->Harga, 0) }}</td>
                                                <td>{{ $p->Nama_Menu }}</td>
                                                @for ($i = 1; $i < 13; $i++)
                                                    @php
                                                        $bulanIndex = 'bulan' . $i;
                                                        $qty = $p->$bulanIndex;
                                                    @endphp
                                                    <td>{{ $qty }}</td>
                                                @endfor

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
    <form action="" method="get">
        <div class="modal fade" role="dialog" id="view" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content ">
                    <div class="modal-header btn-costume">
                        <h5 class="modal-title text-light" id="exampleModalLabel">View Tahun</h5>
                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12 mt-2">
                            <label for="">Tahun</label>
                            <input type="hidden" name="id_lokasi" value="{{ $id_lokasi }}">
                            <select name="tahun" id="" class="select ">
                                @foreach ($tahun as $t)
                                    <option value="{{ $t->tahun }}" {{ $thn == $t->tahun ? 'SELECTED' : '' }}>
                                        {{ $t->tahun }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Search</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- /.content-wrapper -->
    <style>
        .modal-lg-max1 {
            max-width: 1100px;
        }
    </style>
    {{-- import --}}




    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <style>
        .modal-lg-max {
            max-width: 900px;
        }
    </style>



    {{-- ---------------- --}}
@endsection
@section('script')
    <script>
        $(document).ready(function() {

        })
    </script>
@endsection
