<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $kode_stok_produk ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

<div class="container">
<div class="row">
    <div class="col-6">
        <h3>Laporan Stok Masuk Produk</h3>
    </div>
    <div class="col-6">
        <p class="float-right">Waktu Cetak</p>
        <br><br>
        <p class="float-right"><?= date('d M Y, H:i') ?></p>
    </div>
    <div class="col-12">
    <table class="table">
        <thead class="thead-light">
            <tr>
                <th>Kode Stok Masuk</th>
                <th>Waktu Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?= $cek_status->kode_stok_produk ?>    
                </td>
                <?php if($cek_status->status == 'Selesai'): ?>
                <td>
                    <?= date('d-M-Y, H:i', strtotime($cek_status->tgl)) ?>
                </td>
                <?php else: ?>
                <td> - </td>    
                <?php endif; ?>
                <td>
                    <?= $cek_status->status ?>
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
                <th>Kategori</th>					
                <th>Satuan</th>    
                <th>Harga Jual</th>
                <th>Stok Program</th>
                <th>Stok Masuk</th>
           
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
            <?php foreach($stok as $d): ?>
                
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $d->sku ?></td>
                <td><?= $d->nm_produk ?></td>
                <td><?= $d->nm_kategori ?></td>
                <td><?= $d->satuan ?></td>
                <td><?= number_format($d->harga,0) ?></td>
                <td><?= $d->stok_program ?></td>
                <td><?= $d->debit ?></td>
               
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
</div>
    

</body>
</html>
