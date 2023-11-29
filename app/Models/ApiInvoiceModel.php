<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiInvoiceModel extends Model
{
    use HasFactory;
    public static function dataInvoice($id_lokasi, $id_distribusi, $tgl1, $tgl2)
    {
        $result = DB::select("SELECT a.tgl_transaksi, a.no_order, sum(a.total_orderan ) as sub_total , 
        sum(if(b.bayar is null ,0, b.bayar)) as stk,
        sum(a.voucher) as voucher , 
        sum(a.service) as service, 
        sum(a.tax) as  tax, 
        round(( (a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) *  (a.discount / 100)),0) as discount,
        round((a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) - ( (a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) *  (a.discount / 100)),0 ) as ttl_sebelum_round,
        sum(a.total_bayar) as total_bayar,
        sum(round((a.total_bayar + a.dp) - ((a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) - ( (a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) *  (a.discount / 100))),0)) as rounding
        FROM tb_transaksi as a 
        left join tb_invoice as b on b.no_nota = a.no_order 
        left join ( 
            SELECT c.id_distribusi, c.no_order2 
            FROM tb_order2 as c 
        where c.id_lokasi = ? and c.tgl BETWEEN ? and ?
            group by c.no_order2 ) as c on c.no_order2 = a.no_order 
            where a.tgl_transaksi BETWEEN ? and ? and a.id_lokasi = ? and c.id_distribusi = ?
            GROUP by a.no_order; 
        ;", [$id_lokasi, $tgl1, $tgl2, $tgl1, $tgl2, $id_lokasi, $id_distribusi]);

        return $result;
    }
}
