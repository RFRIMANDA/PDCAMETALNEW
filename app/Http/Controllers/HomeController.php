<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }


    public function riskregister(Request $request)
    {
        return view('riskregister.index');
    }
}
