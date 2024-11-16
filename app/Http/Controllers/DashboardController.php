<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Riskregister;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $allowedDivisi = json_decode($user->type, true); // Memastikan type adalah JSON array

        // Inisialisasi query Riskregister
        $query = Riskregister::query();

        // Filter berdasarkan divisi yang diizinkan
        if (!empty($allowedDivisi)) {
            $query->whereHas('divisi', function ($q) use ($allowedDivisi) {
                $q->whereIn('id', $allowedDivisi);
            });
        }

        // Mengambil data Riskregister dan resikos terkait
        $resikos = $query->with(['resikos', 'tindakan.user', 'divisi'])->get()->flatMap(function ($riskregister) {
            return $riskregister->resikos->map(function ($resiko) use ($riskregister) {
                $resiko->nama_issue = $riskregister->issue; // Issue berasal dari model Riskregister
                $resiko->peluang = $riskregister->peluang; // Peluang berasal dari model Riskregister
                $resiko->id_divisi = $riskregister->id_divisi; // ID divisi untuk navigasi
                $resiko->nama_divisi = $riskregister->divisi->nama_divisi ?? 'Unknown'; // Nama divisi dari model Divisi
                return $resiko;
            });
        });

        // Mengelompokkan data untuk pie chart dan modal
        $statusCounts = $resikos->groupBy('status')->map->count(); // Data untuk chart status
        $tingkatanCounts = $resikos->groupBy('tingkatan')->map->count(); // Data untuk chart tingkatan

        // Data detail untuk modal
        $statusDetails = $resikos->groupBy('status');
        $tingkatanDetails = $resikos->groupBy('tingkatan');

        // Passing data ke view
        return view('dashboard.index', compact(
            'statusCounts',
            'tingkatanCounts',
            'statusDetails',
            'tingkatanDetails'
        ));
    }
}
