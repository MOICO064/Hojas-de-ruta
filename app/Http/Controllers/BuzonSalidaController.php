<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuzonSalidaController extends Controller
{
    public function index()
    {
        $buzon = "Salida";
        return view('admin.buzon.index',compact('buzon'));
    }
    public function data()
    {
    }
}
