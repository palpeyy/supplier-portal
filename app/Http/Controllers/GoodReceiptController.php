<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoodReceiptController extends Controller
{
    public function index()
    {

        return view('good-receipt.goodReceipt', [
            'tittle' => 'Good Receipt | Portal Supplier',
        ]);
    }

    public function history()
    {

        return view('good-receipt.goodReceiptHistory', [
            'tittle' => 'Good Receipt | Portal Supplier',
        ]);
    }
}
