@extends('template.master')
@section('content')
    <style>
        .table-container {
            overflow-x: auto;
            max-height: 400px;
        }

        .freeze-cell1_th {
            position: sticky;
            z-index: 30;
            background-color: #F2F7FF;
            top: 0;
        }

        .freeze-cell1_th2 {
            position: sticky;
            z-index: 36;
            background-color: #F2F7FF;
            top: 0;
            left: 0;
        }

        .freeze-samping {
            position: sticky;
            z-index: 35;
            left: 0;
            background-color: #FFFFFF;
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
                                <div class="row">
                                    {{-- <div class="col-l-g-4 mb-2">
                                        <select name="" id="" class="select">
                                            <option value="">Pilih Station</option>
                                            @foreach ($station as $s)
                                                <option value="{{ $s->id_station }}">{{ $s->nm_station }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <div class="col-lg-12">
                                        <div class="table-responsive table-container">
                                            <table class="table table-bordered" id="sortable-table">
                                                <thead>
                                                    <tr>
                                                        <th class="freeze-cell1_th">#</th>
                                                        <th class="freeze-cell1_th" data-sort="station"
                                                            data-sort-type="alphabetic">Station <i
                                                                class="fas fa-sort float-right" data-order="asc"></i></th>
                                                        <th class="freeze-cell1_th text-right" data-sort="harga"
                                                            data-sort-type="numeric">Harga <i
                                                                class="fas fa-sort float-right" data-order="asc"></i></th>
                                                        <th class="freeze-cell1_th2" data-sort="nm_menu"
                                                            data-sort-type="alphabetic">Nama Menu <i
                                                                class="fas fa-sort float-right" data-order="asc"></i></th>
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
                                                            <td align="right">{{ number_format($p->Harga, 0) }}</td>
                                                            <td class="freeze-samping">{{ $p->Nama_Menu }}</td>
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
            $('#sortable-table th').click(function() {
                const table = $(this).parents('table').eq(0);
                const columnIndex = $(this).index();
                const dataType = $(this).data('sort-type');
                const rows = table.find('tr:gt(0)').toArray().sort(comparator(columnIndex, dataType));
                this.asc = !this.asc;
                if (!this.asc) {
                    rows.reverse();
                }
                for (let i = 0; i < rows.length; i++) {
                    table.append(rows[i]);
                }
            });

            function comparator(index, dataType) {
                return function(a, b) {
                    const valA = getCellValue(a, index, dataType);
                    const valB = getCellValue(b, index, dataType);

                    if (dataType === 'numeric') {
                        return valA - valB; // Perbandingan numerik
                    } else if (dataType === 'alphabetic') {
                        return valA.localeCompare(valB);
                    }
                }

                function getCellValue(row, index, dataType) {
                    const cellText = $(row).children('td').eq(index).text();
                    if (dataType === 'numeric') {
                        return parseFloat(cellText.replace(/[^0-9.-]+/g, '')); // Mengambil angka dari teks
                    } else {
                        return cellText;
                    }
                }
            }
        })
    </script>
@endsection
