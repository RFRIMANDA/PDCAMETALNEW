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
    $user = Auth::user();
    $allowedDivisi = json_decode($user->type, true);

    // Query data with filters according to the user's allowed divisions
    $query = Riskregister::query();

    if (!empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

    // Fetch resikos only once and group them
    $resikos = $query->with('resikos')->get()->flatMap->resikos;

    // Aggregate counts for the pie charts
    $statusCounts = $resikos->groupBy('status')->map->count();
    $tingkatanCounts = $resikos->groupBy('tingkatan')->map->count();
    $differentStatusCounts = $resikos->groupBy('status')->map->count();

    // Get detailed data for each status, tingkatan, and different status
    $statusDetails = $resikos->groupBy('status');
    $tingkatanDetails = $resikos->groupBy('tingkatan');
    $differentStatusDetails = $resikos->groupBy('status'); // Same as statusDetails

    // Pass data to the view
    return view('home', compact('statusCounts', 'tingkatanCounts', 'differentStatusCounts', 'statusDetails', 'tingkatanDetails', 'differentStatusDetails'));
}


    public function riskregister(Request $request)
    {
        return view('riskregister.index');
    }
}
