<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;
    protected $table = 'tb_jurnal';
    protected $fillable = [
        'id_jurnal', 'id_buku', 'id_post','id_akun', 'id_post_center',
'kd_gabungan', 'no_nota', 'id_lokasi', 'jenis', 'no_urutan', 'urutan',
'debit', 'kredit', 'no_bkin', 'id_produk', 'qty', 'id_satuan', 'rp_beli',
'ttl_rp', 'rp_pajak', 'tgl', 'tgl_inputa', 'ket', 'ket2', 'admin', 'status'
    ];
}
