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
        GREATEST(
    sum(a.total_orderan) - 
    sum(a.voucher) - 
    round(( (a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) *  (a.discount / 100)), 0),
    0
) AS penjualanKurangVoucherDiskon,
        sum(a.service) as service, 
        sum(a.tax) as  tax, 
        sum(a.dp) as dp,
        round(( (a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) *  (a.discount / 100)),0) as discount,
        round((a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) - ( (a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) *  (a.discount / 100)),0 ) as ttl_sebelum_round,
        sum(a.total_bayar) as total_bayar,
        sum(round((a.total_bayar + a.dp) - ((a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) - ( (a.total_orderan + if(b.bayar is null,0,b.bayar) - a.voucher + a.service + a.tax) *  (a.discount / 100))),0)) as rounding,
        sum(a.cash) as cash, sum(a.d_bca) as d_bca, sum(a.k_bca) as k_bca, sum(a.d_mandiri) as d_mandiri, sum(a.k_mandiri) as k_mandiri, sum(a.d_bri) as d_bri, sum(a.k_bri) as k_bri, d.id_akun_pembayaran, d.nm_klasifikasi,
d.nm_akun, if(d.nominal is null ,0,d.nominal) as nominal , if(d.diskon_bank is null ,0,d.diskon_bank) as diskon_bank
        FROM tb_transaksi as a 
        left join tb_invoice as b on b.no_nota = a.no_order 
        left join ( 
            SELECT c.id_distribusi, c.no_order2 
            FROM tb_order2 as c 
        where c.id_lokasi = ? and c.tgl BETWEEN ? and ?
            group by c.no_order2 ) as c on c.no_order2 = a.no_order 
            left join (
                SELECT d.no_nota, e.id_akun_pembayaran, e.nm_akun, f.nm_klasifikasi, sum(d.nominal) as nominal, sum(d.diskon_bank) as diskon_bank
                FROM pembayaran as d 
                left join akun_pembayaran as e on e.id_akun_pembayaran = d.id_akun_pembayaran
                left join klasifikasi_pembayaran as f on f.id_klasifikasi_pembayaran = e.id_klasifikasi
                GROUP by d.no_nota
            ) as d on d.no_nota = a.no_order
            where a.tgl_transaksi BETWEEN ? and ? and a.id_lokasi = ? and c.id_distribusi = ? and a.total_orderan  != 0
            GROUP by a.no_order; 
        ;", [$id_lokasi, $tgl1, $tgl2, $tgl1, $tgl2, $id_lokasi, $id_distribusi]);

        return $result;
    }
    public static function dataMenu($id_lokasi, $tgl1, $tgl2)
    {
        $result = DB::select("SELECT b.id_menu, b.nm_menu, sum(a.qty) as qty
        FROM tb_order as a 
        left join tb_harga as c on c.id_harga = a.id_harga
        left join tb_menu as b on b.id_menu = c.id_menu
        where a.tgl between ? and ? and a.id_lokasi = ? and a.id_harga != 0
        group by b.id_menu;
        
        ", [$tgl1, $tgl2, $id_lokasi]);

        return $result;
    }
}
