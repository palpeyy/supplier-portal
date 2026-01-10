<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
    public function index()
    {

        return view('surat-jalan.suratJalan', [
            'tittle' => 'Surat Jalan | Portal Supplier',
        ]);
    }

    public function approval()
    {

        return view('surat-jalan.suratJalanApproval', [
            'tittle' => 'Purchase Order | Portal Supplier',
        ]);
    }
}
