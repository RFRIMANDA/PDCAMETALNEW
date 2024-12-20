<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use App\Models\Riskregister;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    $allowedDivisi = json_decode($user->type, true); // Memastikan type adalah JSON array

    // Ambil filter departemen dari request
    $selectedDepartemen = $request->input('departemen');

    // Inisialisasi query Riskregister
    $query = Riskregister::query();

    // Filter berdasarkan divisi yang diizinkan
    if (!empty($allowedDivisi)) {
        $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
            $q->whereIn('id', $allowedDivisi);
        });
    }

    // Filter berdasarkan departemen jika ada
    if ($selectedDepartemen) {
        $query->whereHas('divisi', function ($q) use ($selectedDepartemen) {
            $q->where('nama_divisi', $selectedDepartemen);
        });
    }

    // Mengambil data Riskregister dan resikos terkait
    $resikos = $query->with(['resikos', 'tindakan.user', 'divisi'])->get()->flatMap(function ($riskregister) {
        return $riskregister->resikos->map(function ($resiko) use ($riskregister) {
            $resiko->nama_issue = $riskregister->issue;
            $resiko->peluang = $riskregister->peluang;
            $resiko->id_divisi = $riskregister->id_divisi;
            $resiko->nama_divisi = $riskregister->divisi->nama_divisi ?? 'Unknown';
            return $resiko;
        });
    });

    // Mengelompokkan data untuk pie chart dan modal
    $statusCounts = $resikos->groupBy('status')->map->count();
    $tingkatanCounts = $resikos->groupBy('tingkatan')->map->count();

    // Data detail untuk modal
    $statusDetails = $resikos->groupBy('status');
    $tingkatanDetails = $resikos->groupBy('tingkatan');

    // Ambil daftar departemen untuk dropdown filter
    $departemenList = Divisi::orderBy('nama_divisi', 'asc')->pluck('nama_divisi');


    // Passing data ke view
    return view('dashboard.index', compact(
        'statusCounts',
        'tingkatanCounts',
        'statusDetails',
        'tingkatanDetails',
        'departemenList',
        'selectedDepartemen'
    ));
}

}
