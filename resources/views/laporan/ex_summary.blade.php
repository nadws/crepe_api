<?php

$file = 'LAPORAN SUMMARY RESTO.xls';
header('Content-type: application/vnd-ms-excel');
header("Content-Disposition: attachment; filename=$file");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <!-- CSS only -->
    <?php
    $pb1_gojek = ($total_gojek->total + $majo_gojek->bayar_majo * 0.8) / 11;
    
    ?>
    <?php
    $service_charge = $total_not_gojek->total * 0.07;
    $pb1_not_gojek = ($total_not_gojek->total + $service_charge) * 0.1;
    
    $total_total = $total_gojek->total + $total_not_gojek->total + $service_charge + $pb1_not_gojek + $transaksi->rounding;
    
    ?>

    <div class="card">
        <div class="card-body">
            <h5 style="font-weight: bold; text-align: center;">== {{ $lokasi == '1' ? 'TAKEMORI' : 'SOONDOBU' }} ==</h5>
            <table width="100%">
                <tr>
                    <td width="20%">From</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right; white-space: nowrap;"><?= date('M d, Y', strtotime($tgl1)) ?></td>
                </tr>
                <tr>
                    <td>To</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= date('M d, Y', strtotime($tgl2)) ?></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <hr style="border-top: 2px dashed black">
                    </td>
                </tr>
                <tr>
                    <td>Total Invoice</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= $transaksi->ttl_invoice ?></td>
                </tr>
                <tr>
                    <td>rp/invoice</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($total_total / $transaksi->ttl_invoice, 0) ?></td>
                </tr>
                <tr>
                    <td>unit food</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= $transaksi->unit ?></td>
                </tr>
                <tr>
                    <td>rp/unit</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($total_total / $transaksi->unit, 0) ?></td>
                </tr>

                <tr>
                    <td>Jumlah pesanan telat masak telat masak 25 Menit</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($jml_telat->jml_telat, 0) ?> /
                        <?= $jml_telat->jml_telat > 0 ? number_format(($jml_telat->jml_telat * 100) / ($jml_telat->jml_telat + $jml_ontime->jml_ontime), 0) : 0 ?>%
                    </td>
                </tr>
                <tr>
                    <td>Jumlah pesanan telat masak 20 Menit</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($jml_telat20->jml_telat, 0) ?> /
                        <?= $jml_telat->jml_telat > 0 ? number_format(($jml_telat20->jml_telat * 100) / ($jml_telat20->jml_telat + $jml_ontime->jml_ontime), 0) : 0 ?>%
                    </td>
                </tr>
                <tr>
                    <td>Jumlah pesanan ontime masak</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($jml_ontime->jml_ontime, 0) ?> /
                        <?= $jml_ontime->jml_ontime > 0 ? number_format(($jml_ontime->jml_ontime * 100) / ($jml_telat->jml_telat + $jml_ontime->jml_ontime), 0) : 0 ?>%
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <hr style="border-top: 2px dashed black">
                    </td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">revenue</td>
                    <td width="1%"></td>
                    <td></td>
                    <td style="text-align: right;"></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">subtotal dinein, take away & delivery</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;"><?= number_format($total_not_gojek->total, 0) ?>
                    </td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">service charge</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;"><?= number_format($service_charge, 0) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">pb1 dinein, take away & delivery</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;"><?= number_format($pb1_not_gojek, 0) ?></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <hr style="border-top: 2px dashed black">
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">subtotal stk</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;"><?= number_format($majo->bayar_majo, 0) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">pb1 stk</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;"><?= number_format($majo->bayar_majo * 0.1, 0) ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" style="text-align: center;">
                        <hr style="border-top: 2px dashed black">
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">subtotal gojek</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;">
                        <?= number_format($total_gojek->total + $majo_gojek->bayar_majo - $pb1_gojek, 0) ?></td>
                </tr>



                <tr>
                    <td style="font-weight: bold;">pb1 gojek dine in & stk (80% dari subtotal / 11)</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;"><?= number_format($pb1_gojek, 0) ?></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <hr style="border-top: 2px dashed black">
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">total pb1</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;">
                        <?= number_format($pb1_gojek + $pb1_not_gojek + $majo->bayar_majo * 0.1, 0) ?>
                    </td>
                </tr>

                <!--<tr>-->
                <!--    <td style="font-weight: bold;">total majoo</td>-->
                <!--    <td style="font-weight: bold;" width="1%">:</td>-->
                <!--    <td></td>-->
                <!--    <td style="text-align: right;font-weight: bold;">-->
                <!--        <?= number_format($majo->bayar_majo + $majo_gojek->bayar_majo, 0) ?></td>-->
                <!--</tr>-->

                <tr>
                    <td style="font-weight: bold;">total subtotal</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;">
                        <?= number_format($total_gojek->total + $majo_gojek->bayar_majo - $pb1_gojek + ($total_not_gojek->total + $majo->bayar_majo), 0) ?>
                    </td>
                </tr>


                <?php
                $total_transaksi = $transaksi->rp + $transaksi->tax + $transaksi->ser + $transaksi->rounding - $transaksi->dp;
                $kembalian = $transaksi->total_bayar - $total_transaksi;
                $kurangan = $transaksi->tax + $transaksi->ser + $transaksi->rounding - $transaksi->dp;
                
                ?>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <hr style="border-top: 2px dashed black">
                    </td>
                </tr>
                @if ($transaksi->cash == 0)
                @else
                    <tr>
                        <td>Cash</td>
                        <td width="1%">:</td>
                        <td></td>
                        <td style="text-align: right;"><?= number_format($transaksi->cash, 0) ?>
                            <!--/ <?= number_format($transaksi->cash - $transaksi->total_bayar, 0) ?>-->
                        </td>
                    </tr>
                @endif
                @if ($transaksi->d_bca == 0)
                @else
                    <tr>
                        <td>BCA Debit</td>
                        <td width="1%">:</td>
                        <td></td>
                        <td style="text-align: right;"><?= number_format($transaksi->d_bca, 0) ?></td>
                    </tr>
                @endif

                @if ($transaksi->k_bca)
                @else
                    <tr>
                        <td>BCA Kredit</td>
                        <td width="1%">:</td>
                        <td></td>
                        <td style="text-align: right;"><?= number_format($transaksi->k_bca, 0) ?></td>
                    </tr>
                @endif

                @if ($transaksi->d_mandiri == 0)
                @else
                    <tr>
                        <td>Mandiri Debit</td>
                        <td width="1%">:</td>
                        <td></td>
                        <td style="text-align: right;"><?= number_format($transaksi->d_mandiri, 0) ?></td>
                    </tr>
                @endif

                @if ($transaksi->k_mandiri == 0)
                @else
                    <tr>
                        <td>Mandiri Kredit</td>
                        <td width="1%">:</td>
                        <td></td>
                        <td style="text-align: right;"><?= number_format($transaksi->k_mandiri, 0) ?></td>
                    </tr>
                @endif
                @if ($transaksi->d_bri == 0)
                @else
                    <tr>
                        <td>Bri Debit</td>
                        <td width="1%">:</td>
                        <td></td>
                        <td style="text-align: right;"><?= number_format($transaksi->d_bri, 0) ?></td>
                    </tr>
                @endif
                @if ($transaksi->k_bri == 0)
                @else
                    <tr>
                        <td>Bri Kredit</td>
                        <td width="1%">:</td>
                        <td></td>
                        <td style="text-align: right;"><?= number_format($transaksi->k_bri, 0) ?></td>
                    </tr>
                @endif
                @foreach ($pembayaran as $p)
                    <tr>
                        <td>{{ $p->nm_akun }} {{ $p->nm_klasifikasi }}</td>
                        <td width="1%">:</td>
                        <td></td>
                        <td style="text-align: right;">{{ number_format($p->nominal, 0) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="font-weight: bold;">Total total</td>
                    <td style="font-weight: bold;" width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;font-weight: bold;"><?= number_format($transaksi->total_bayar, 0) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <hr style="border-top: 2px dashed black">
                    </td>
                </tr>


                <tr>
                    <td>discount</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($transaksi->discount, 0) ?></td>
                </tr>
                <tr>
                    <td>voucher</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($transaksi->voucher, 0) ?></td>
                </tr>
                <!-- <tr>
                <td>service charge</td>
                <td width="1%">:</td>
                <td></td>
                <td style="text-align: right;"><?= number_format($transaksi->ser, 0) ?></td>
            </tr>

            <tr>
                <td>pb1</td>
                <td width="1%">:</td>
                <td></td>
                <td style="text-align: right;"><?= number_format($transaksi->tax, 0) ?></td>
            </tr> -->
                @php
                    $pb1_all = $pb1_gojek + $pb1_not_gojek + $majo->bayar_majo * 0.1;
                    $total_all = $total_gojek->total + $majo_gojek->bayar_majo - $pb1_gojek + ($total_not_gojek->total + $majo->bayar_majo);

                    $sub_all = $pb1_all + $total_all + $service_charge;
                @endphp
                <tr>
                    <td>rounding</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;">
                        <?= number_format($transaksi->dp + $transaksi->total_bayar - $sub_all, 0) ?></td>
                </tr>
                <tr>
                    <td>dp</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($transaksi->dp, 0) ?></td>
                </tr>
                <tr>
                    <td>total total + dp</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($transaksi->dp + $transaksi->total_bayar, 0) ?>
                    </td>
                </tr>
                <tr>
                    <td>gosend</td>
                    <td width="1%">:</td>
                    <td></td>
                    <td style="text-align: right;"><?= number_format($transaksi->gosend, 0) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;" colspan="3">- - - - - - - - - - - - - ++</td>
                </tr>
                <!-- <tr>
                <?php $total = $transaksi->rp + $transaksi->ser + $transaksi->tax + $transaksi->rounding - $transaksi->dp; ?>
                <td style="font-weight: bold;">TOTAL</td>
                <td style="font-weight: bold;" width="1%">:</td>
                <td></td>
                <td style="text-align: right;font-weight: bold;"><?= number_format($total, 0) ?></td>
            </tr> -->
                <tr>
                    <td style="font-weight: bold;">Void</td>
                    <td width="1%"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: right;"></td>
                </tr>
                @foreach ($void as $v)
                    <tr>
                        <td style="white-space: nowrap;">{{ $v->kategori }}</td>
                        <td width="1%">:</td>
                        <td style="text-align: center;" width="50%"><?= $v->void ?></td>
                        <td style="text-align: right;"><?= number_format($v->harga, 0) ?></td>
                    </tr>
                @endforeach
                <tr>
                    <td style="font-weight: bold;">Dine In / Takeaway</td>
                    <td width="1%"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: right;"></td>
                </tr>
                <?php foreach ($kategori as $k) : ?>
                <tr>
                    <td style="white-space: nowrap;"><?= $k->kategori ?></td>
                    <td width="1%">:</td>
                    <td style="text-align: center;" width="50%"><?= $k->qty ?></td>
                    <td style="text-align: right;"><?= number_format($k->hargaT, 0) ?></td>
                </tr>
                <?php endforeach ?>
                <tr>
                    <td style="font-weight: bold;">Gojek</td>
                    <td width="1%"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: right;"></td>
                </tr>
                <?php foreach ($gojek as $k) : ?>
                <tr>
                    <td style="white-space: nowrap;"><?= $k->kategori ?></td>
                    <td width="1%">:</td>
                    <td style="text-align: center;" width="50%"><?= $k->qty ?></td>
                    <td style="text-align: right;"><?= number_format($k->harga, 0) ?></td>
                </tr>
                <?php endforeach ?>
            </table>
        </div>

    </div>

</body>

</html>
