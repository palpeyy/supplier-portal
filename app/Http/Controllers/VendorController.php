<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {

        return view('master-data.vendor', [
            'tittle' => 'Vendor | Portal Supplier',
        ]);
    }
}
