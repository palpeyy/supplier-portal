<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {

        return view('purchasing.purchaseRequest', [
            'tittle' => 'Purchase Reuqest | Portal Supplier',
        ]);
    }
}
