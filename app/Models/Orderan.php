<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderan extends Model
{
    use HasFactory;
    protected $table = 'tb_order';
    protected $fillable = [
        'no_order', 'id_harga', 'qty', 'harga', 'request', 'tambahan', 'page', 'id_meja', 'selesai',
        'id_lokasi', 'pengantar', 'tgl', 'admin', 'void', 'round', 'alasan', 'nm_void', 'j_mulai', 'j_selesai',
        'diskon', 'wait', 'aktif', 'id_koki1', 'id_koki2', 'id_koki3', 'ongkikr', 'id_distribusi', 'orang', 'no_checker', 'print', 'copy_print', 'voucher'
    ];
}
