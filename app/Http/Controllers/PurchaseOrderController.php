<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {

        return view('purchasing.purchaseOrder', [
            'tittle' => 'Purchase Order | Portal Supplier',
        ]);
    }

    public function approval()
    {

        return view('purchasing.purchaseOrderApproval', [
            'tittle' => 'Purchase Order | Portal Supplier',
        ]);
    }
}
