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
                    <form action="{{ route('editStokMasuk') }}" method="post">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h3 class="float-left">Daftar Produk</h3>
                                @if ($cek_status->status != 'Selesai')
                                <button type="button" id="atur_barang" class="btn btn-costume float-right ml-2"
                                    data-toggle="modal" data-target="#exampleModal">
                                    <i class="text-light fas fa-plus"></i> Atur Barang
                                </button>
                                @endif
                                <a target="_blank"
                                    href="<?= route('printStokMasuk', ['kode_stok_produk' => Request::get('kode')]) ?>"
                                    class="btn btn-costume float-right"><i class="fas fa-print text-light"></i>
                                    Print</a>
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
                                                        <th class="sticky-top th-top">Harga Jual</th>
                                                        <th class="sticky-top th-top" width="10%">Stok Program</th>
                                                        <th class="sticky-top th-top">Stok Masuk</th>
                                                        <th class="sticky-top th-top" width="10%">Total Stok</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $cek_produk = []; 
                                                    foreach ($stok as $op) : 
                                                        $cek_produk [] = $op->id_produk;
                                                    ?>
                                                    <tr>
                                                        <input type="hidden" name="id_stok_produk[]"
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
                                                            <?= number_format($op->harga, 0) ?>
                                                        </td>
                                                        <td>
                                                            <?= $op->stok_program ?>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="debit[]"
                                                                value="<?= $op->debit ?>"
                                                                style="width: 150px; text-align: center;"
                                                                class="form-control fill">
                                                        </td>
                                                        <td>
                                                            <?= number_format($op->stok_program + $op->debit,0)  ?>
                                                        </td>


                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">

                                            <a href="<?= route('stok_masuk') ?>" class="btn btn-secondary">Kembali</a>

                                            <?php if($cek_status->status != 'Selesai') :?>
                                            <a href="<?= route('deleteStok', ['kode_stok_produk' => $kode_stok_produk]) ?>"
                                                class="btn btn-outline-danger"
                                                onclick="return confirm('Yakin ingin menghapus?')"><i
                                                    class="fas fa-trash text-danger"></i></a>

                                            <button type="submit" name="action" value="selesai"
                                                class="btn btn-costume float-right ml-2">Selesai</button>

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
</style>
<form action="<?= route('tambahProdukMasuk') ?>" method="POST">
    @csrf
    <input type="hidden" name="kode_stok_produk" value='<?= $kode_stok_produk ?>'>
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
                            <thead class="bg-costume">
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
                                        <div class="form-check d-inline">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="">
                                        </div>
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
                                    <?= $p->stok ?>
                                </td>
                                <td>
                                    <?= number_format($p->harga, 0) ?>
                                </td>
                                <td>
                                    <?php if(in_array($p->id_produk, $cek_produk)): ?>
                                    <center><input class="form-check-input" type="checkbox" name="id_produk_stok[]"
                                            value="<?= $p->id_produk ?>" checked></center>
                                    <?php else: ?>
                                    <center><input class="form-check-input" type="checkbox" name="id_produk_stok[]"
                                            value="<?= $p->id_produk ?>"></center>
                                    <?php endif; ?>
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