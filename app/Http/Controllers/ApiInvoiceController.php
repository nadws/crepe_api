<?php

namespace App\Http\Controllers;

use App\Models\ApiInvoiceModel;
use Illuminate\Http\Request;

class ApiInvoiceController extends Controller
{
    function invoice(Request $r)
    {
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $id_distribusi = $r->id_distribusi;
        $id_lokasi = $r->id_lokasi;
        $invoice = ApiInvoiceModel::dataInvoice($id_lokasi, $id_distribusi, $tgl1, $tgl2);

        $response = [
            'status' => 'success',
            'message' => 'Data Invoice berhasil diambil',
            'data' => [
                'invoice' => $invoice,
            ],
        ];
        return response()->json($response);
    }
    function menu(Request $r)
    {
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $id_lokasi = $r->id_lokasi;
        $menu = ApiInvoiceModel::dataMenu($id_lokasi, $tgl1, $tgl2);

        $response = [
            'status' => 'success',
            'message' => 'Data Invoice berhasil diambil',
            'data' => [
                'menu' => $menu,
            ],
        ];
        return response()->json($response);
    }
}
