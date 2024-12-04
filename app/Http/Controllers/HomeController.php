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

    // Initialize the query for Riskregister, considering user's allowed divisions
    $query = Riskregister::query();

    // Filter by allowed divisions if applicable
    if (!empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

    $resikos = $query->with(['resikos', 'tindakan.user'])->get()->flatMap(function ($riskregister) {
        return $riskregister->resikos->map(function ($resiko) use ($riskregister) {
            $resiko->nama_resiko = $riskregister->issue;

            // Ambil nama_user dari relasi targetpicUser di model Tindakan
            $resiko->user = $resiko->tindakan->user->nama_user ?? 'N/A'; // Pastikan ada pengecekan null
            return $resiko;
        });
    });

    // Aggregate counts for the pie charts based on status and tingkatan
    $statusCounts = $resikos->groupBy('status')->map->count();
    $tingkatanCounts = $resikos->groupBy('tingkatan')->map->count();
    $differentStatusCounts = $resikos->groupBy('status')->map->count(); // Same as statusCounts

    // Get detailed data for each status, tingkatan, and different status
    $statusDetails = $resikos->groupBy('status');
    $tingkatanDetails = $resikos->groupBy('tingkatan');
    $differentStatusDetails = $resikos->groupBy('status'); // Same as statusDetails

    // Pass the data to the view
    return view('home', compact('statusCounts', 'tingkatanCounts', 'differentStatusCounts', 'statusDetails', 'tingkatanDetails', 'differentStatusDetails'));
}


public function getFilteredData(Request $request)
{
    $user = Auth::user();
    $allowedDivisi = json_decode($user->type, true);
    $filterType = $request->input('filterType');
    $filterValue = $request->input('filterValue');

    $query = Riskregister::query();

    if (!empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

    // Filter resikos based on filter type and value
    $resikos = $query->with('resikos')->get()->flatMap->resikos;
    if ($filterType === 'status') {
        $resikos = $resikos->where('status', $filterValue);
    } elseif ($filterType === 'tingkatan') {
        $resikos = $resikos->where('tingkatan', $filterValue);
    }

    return response()->json($resikos);
}

    public function riskregister(Request $request)
    {
        return view('riskregister.index');
    }
}
