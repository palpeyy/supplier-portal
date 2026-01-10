<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {

        return view('purchase-invoice.purchaseInvoice', [
            'tittle' => 'Purchase Invoice | Portal Supplier',
        ]);
    }

    public function pembayaran()
    {

        return view('purchase-invoice.daftarPembayaran', [
            'tittle' => 'Purchase Invoice | Portal Supplier',
        ]);
    }
}
