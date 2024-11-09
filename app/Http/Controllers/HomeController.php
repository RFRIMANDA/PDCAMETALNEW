<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Riskregister;
use App\Models\Divisi;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Dapatkan divisi_id dari parameter request atau berdasarkan user yang sedang login
        $divisiId = Auth::user()->divisi; // Misalnya, mengambil divisi_id dari pengguna yang login

        // Hitung doneCount berdasarkan divisi dan status 'close'
        $doneCount = Riskregister::whereHas('resikos', function ($query) {
            $query->where('tingkatan', 'High'); // Status 'close' menunjukkan selesai
        })
        ->where('id_divisi', $divisiId)  // Filter berdasarkan divisi
        ->count();

        // Hitung notDoneCount berdasarkan divisi
        $notDoneCount = Riskregister::where('id_divisi', $divisiId)->count() - $doneCount;

        // Mengirimkan data ke view
        return view('home', compact('doneCount', 'notDoneCount'));
    }



    public function riskregister(Request $request)
    {
        return view('riskregister.index');
    }
}
