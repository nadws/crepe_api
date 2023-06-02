<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $kode_opname ?>
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-6">
                <h3>Laporan Opname Product</h3>
            </div>
            <div class="col-6">
                <p class="float-right">Waktu Cetak</p>
                <br><br>
                <p class="float-right">
                    <?= date('d M Y, H:i') ?>
                </p>
            </div>
            <div class="col-12">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Kode Opname</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?= $opname->kode_stok_produk ?>
                            </td>
                            <?php if($opname->status == 'Selesai'): ?>
                            <td>
                                <?= date('d-M-Y, H:i', strtotime($opname->tgl)) ?>
                            </td>
                            <?php else: ?>
                            <td> - </td>
                            <?php endif; ?>
                            <td>
                                <?= $opname->status ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <table class="table" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Stok Program</th>
                            <th>Stok Aktual</th>
                            <th>Selisih</th>
                            <th>Harga Jual</th>
                            <th>Total Program</th>
                            <th>Total Selisih</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach($detail_opname as $d): ?>
                        <?php 
            $selisih = $d->stok_program - $d->stok_aktual;
            if($selisih < 0 ){
                $selisih = $selisih * -1;
            }
            $ttl_selisih = $selisih * $d->harga;
        ?>
                        <tr>
                            <td>
                                <?= $no++ ?>
                            </td>
                            <td>
                                <?= $d->sku ?>
                            </td>
                            <td>
                                <?= $d->nm_produk ?>
                            </td>
                            <td>
                                <?= $d->stok_program ?>
                            </td>
                            <td>
                                <?= $d->stok_aktual ?>
                            </td>
                            <td>
                                <?= $selisih ?>
                            </td>
                            <td>
                                <?= number_format($d->harga,0) ?>
                            </td>
                            <td>
                                <?= number_format($d->harga * $d->stok_program,0) ?>
                            </td>
                            <td>
                                <?= number_format($ttl_selisih,0) ?>
                            </td>
                            <td>
                                <?= $d->catatan ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</body>

</html>