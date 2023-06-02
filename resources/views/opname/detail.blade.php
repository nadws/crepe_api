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
                    <form action="{{ route('editStokAktual', ['kode_opname' => Request::get('kode_opname')]) }}"
                        method="post">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h3 class="float-left">Stok Opname</h3>
                                @if ($cek_status->status != 'Selesai')
                                <button type="button" id="atur_barang" class="btn btn-costume float-right ml-2"
                                    data-toggle="modal" data-target="#exampleModal">
                                    <i class="text-light fas fa-plus"></i> Atur Barang
                                </button>
                                @endif
                                <a target="_blank"
                                    href="<?= route('printOpname', ['kode_opname' => Request::get('kode_opname')]) ?>"
                                    class="btn btn-costume float-right ml-2"><i class="fas fa-print text-light"></i>
                                    Print</a>
                                <a target="_blank"
                                    href="<?= route('formulirOpname', ['kode_opname' => Request::get('kode_opname')]) ?>"
                                    class="btn btn-costume float-right ml-2"><i class="fas fa-print text-light"></i>
                                    formulir</a>
                            </div>
                            <div class="card-body">
                                <div id="table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table" id="" width="100%">
                                                <thead>
                                                    <tr>
                                                        <!-- <th class="sticky-top th-top">SKU</th> -->
                                                        <th class="sticky-top th-top">Product</th>
                                                        <th class="sticky-top th-top">Satuan</th>
                                                        <th class="sticky-top th-top">Kategori</th>
                                                        <th class="sticky-top th-top">Stok Program</th>
                                                        <th class="sticky-top th-top">Stok Aktual</th>
                                                        <th class="sticky-top th-top">Selisih</th>
                                                        <th class="sticky-top th-top">Harga Jual</th>
                                                        <th class="sticky-top th-top">Total Program</th>
                                                        <th class="sticky-top th-top">Total Selisih</th>
                                                        <th class="sticky-top th-top">Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $cek_produk = []; 
                                                    foreach ($opname as $op) : ?>
                                                    <?php
                                                        $cek_produk [] = $op->id_produk;
                                                        $selisih = $op->stok_program - $op->stok_aktual;
                                                        if($selisih < 0 ){
                                                            $selisih = $selisih * -1;
                                                        }
                                                        $ttl_selisih = $selisih * $op->harga;
                                                        ?>
                                                    <?php if ($selisih > 0) : ?>
                                                    <tr class="text-danger">
                                                        <?php else : ?>
                                                    <tr>
                                                        <?php endif; ?>
                                                        <input type="hidden" name="id_opname[]"
                                                            value="<?= $op->id_stok_produk ?>">
                                                        <input type="hidden" name="id_produk[]"
                                                            value="<?= $op->id_produk ?>">
                                                        <td>
                                                            <?= $op->nm_produk ?>
                                                        </td>
                                                        <td>
                                                            <?= $op->satuan ?>
                                                        </td>
                                                        <td>
                                                            <?= $op->nm_kategori ?>
                                                        </td>
                                                        <td>
                                                            <?= $op->stok_program ?>
                                                            <input type="hidden" name="stok_program[]"
                                                                value="<?= $op->stok_program ?>"
                                                                style="width: 120px; text-align: center;"
                                                                class="form-control fill">
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <input type="text" name="stok_aktual[]"
                                                                    value="<?= $op->stok_aktual ?>"
                                                                    style="width: 120px; text-align: center;"
                                                                    class="form-control fill">
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <?= $selisih ?>

                                                        </td>
                                                        <td>
                                                            <?= number_format($op->harga, 0) ?>
                                                        </td>
                                                        <td>
                                                            <?= number_format($op->harga * $op->stok_program, 0) ?>
                                                        </td>
                                                        <td>
                                                            <?= number_format($ttl_selisih, 0) ?>
                                                        </td>
                                                        <td>
                                                            <center><input type="text" name="catatan[]"
                                                                    value="<?= $op->catatan ?>"
                                                                    style="width: 120px; text-align: center;"
                                                                    class="form-control fill"></center>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">

                                            <a href="<?= route('opname_majo') ?>" class="btn btn-secondary">Kembali</a>

                                            <?php if($cek_status->status != 'Selesai') :?>
                                            <a href="<?= route('deleteOpname', ['kode_opname' => $kode_opname]) ?>"
                                                class="btn btn-outline-secondary"
                                                onclick="return confirm('Yakin ingin menghapus?')"><i
                                                    class="fas fa-trash"></i></a>

                                            <button type="submit" name="action" value="selesai"
                                                class="btn btn-costume float-right ml-2">Selesai</button>
                                            <button type="submit" name="action" value="edit"
                                                class="btn btn-costume float-right ml-2">Edit</button>

                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<style>
    .modal-lg {
        max-width: 1100px;
        margin: 2rem auto;
    }

    .modal-body {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
<form action="{{ route('inputOpname2') }}" method="post">
    @csrf
    <input type="hidden" name="kode_opname" value='<?= $kode_opname ?>'>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header btn-costume">
                    <h5 class="modal-title majoo" id="exampleModalLabel">Pilih Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover" width="100%">
                        <div class="sticky-top cari">
                            <div class="row ">
                                <div class="col-6">
                                    <select id="countriesDropdown" class="form-control" oninput="filterTable()">
                                        <option>All</option>
                                        <?php foreach ($kategori as $k) : ?>
                                        <option>
                                            <?= $k->nm_kategori ?>
                                        </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="text" id="myInput" name="keyword" class="form-control"
                                        placeholder="Cari Produk . .">
                                </div>
                            </div>
                            <br>
                            <thead class="btn-costume">
                                <tr>
                                    <th class="sticky-top th majoo">#</th>
                                    <th class="sticky-top th majoo">PRODUK</th>
                                    <th class="sticky-top th majoo">SKU</th>
                                    <th class="sticky-top th majoo">KATEGORI</th>
                                    <th class="sticky-top th majoo">SATUAN</th>
                                    <th class="sticky-top th majoo">STOK</th>
                                    <th class="sticky-top th majoo">HARGA</th>
                                    <th class="sticky-top th majoo">
                                        <!--<i class="fas fa-check-square"></i>-->
                                        <input type="checkbox" id="checkAll" value="">
                                    </th>
                                </tr>
                            </thead>
                        </div>
                        <tbody id="myTable">
                            <?php
                            $n = 1;
                            foreach ($produk as $p) : ?>
                            <tr>
                                <td>
                                    <?= $n++ ?>
                                </td>
                                <td>
                                    <?= $p->nm_produk ?>
                                </td>
                                <td>
                                    <?= $p->sku ?>
                                </td>
                                <td>
                                    <?= $p->nm_kategori ?>
                                </td>
                                <td>
                                    <?= $p->satuan ?>
                                </td>
                                <td>
                                    <?= $p->debit - ($p->kredit + $p->kredit_penjualan) ?>
                                </td>
                                <td>
                                    <?= number_format($p->harga, 0) ?>
                                </td>
                                <td>
                                    <center><input class="form-check-input" type="checkbox" name="id_produk_opname[]"
                                            value="<?= $p->id_produk ?>"></center>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-costume">Pilih</button>

                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>
</form>

    
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function filterTable() {
        // Variables
        let dropdown, table, rows, cells, country, filter;
        dropdown = document.getElementById("countriesDropdown");
        table = document.getElementById("myTable");
        rows = table.getElementsByTagName("tr");
        filter = dropdown.value;

        // Loops through rows and hides those with countries that don't match the filter
        for (let row of rows) { // `for...of` loops through the NodeList
            cells = row.getElementsByTagName("td");
            country = cells[3] || null; // gets the 2nd `td` or nothing
            // if the filter is set to 'All', or this is the header row, or 2nd `td` text matches filter
            // alert(country.textContent);
            if (filter === "All" || !country || (filter === country.textContent)) {
                row.style.display = ""; // shows this row
            } else {
                row.style.display = "none"; // hides this row
            }
        }
    }
</script>
<script>
    $("#checkAll").click(function() {
        $('input:checkbox:visible').not(this).prop('checked', this.checked);
    });
</script>

@endsection