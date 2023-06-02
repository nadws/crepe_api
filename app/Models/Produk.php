<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'tb_produk';
    protected $fillable = [
        'id_produk', 'id_kategori', 'id_satuan', 'sku', 'nm_produk', 'harga_modal', 'harga', 'stok', 'terjual', 'diskon', 'komisi', 'monitoring', 'id_lokasi'
    ];
}
