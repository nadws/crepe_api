<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <div class="container ">
        <br>
        <div class="row justify-content-center mt-2">
            <div class="col-lg-12">
                <center>
                    <h4>
                        <u>
                            Formulir Stok Opname
                        </u>
                    </h4>
                </center>
            </div>
            <br>
            <br>
            <div class="col-lg-8">
                <table id="" class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stok Aktual</th>
                            <th>Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($opname as $op) : 
                        
                        if($op->stok_program < 1){
                            continue;
                        }
                        ?>
                        <tr>
                            <td>
                                <?= $op->nm_produk ?>
                            </td>
                            <td>

                            </td>
                            <td>
                                <?= number_format($op->harga, 0) ?>
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