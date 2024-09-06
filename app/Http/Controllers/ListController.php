<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\ListForm;
use App\Models\ListKecil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ListController extends Controller
{
    // Menampilkan halaman utama dengan data divisi
    public function index(Request $request)
    {
        $divisi = Divisi::all();
        $forms = ListForm::all(); // Atau dengan filter sesuai request
        return view('list.listregister', compact('forms','divisi'));
    }

    // Menampilkan halaman create dengan data divisi
    public function create($id)
    {
        $enchan = $id;
        $divisi = Divisi::all();
        return view('list.create', compact('enchan', 'divisi'));
    }

    // Menyimpan data dari form create
    public function store(Request $request)
{
    // Validasi input
    $validated = $request->validate([
        'id_divisi' => 'required|exists:divisi,id',
        'issue' => 'required|string',
        'pihak.*' => 'required|exists:divisi,id',
        'resiko.*' => 'nullable|string|max:255',
        'tindakan.*' => 'nullable|string|max:255',
        'pic.*' => 'nullable|string|max:255',
        'peluang' => 'nullable|string',
        'tingkatan' => 'nullable|string',
        'status' => 'nullable|in:OPEN,ON PROGRESS,CLOSE',
        'risk' => 'nullable|in:HIGH,MEDIUM,LOW',
    ]);

    // Simpan data ListForm
    $listForm = ListForm::create([
        'id_divisi' => $request->input('id_divisi'),
        'issue' => $request->input('issue'),
        'pihak' => json_encode($request->input('pihak')),
        'resiko' => json_encode($request->input('resiko')),
        'tindakan' => json_encode($request->input('tindakan')),
        'pic' => json_encode($request->input('pic')),
        'peluang' => $request->input('peluang'),
        'tingkatan' => $request->input('tingkatan'),
        'status' => $request->input('status'),
        'risk' => $request->input('risk'),
    ]);

    // Simpan data ListKecil dengan ID yang sama tetapi kolom lainnya diset ke null
    ListKecil::create([
        'id' => $listForm->id,  // Set ID yang sama
        'target' => null,
        'realisasi' => null,
        'responsible' => null,
        'accountable' => null,
        'consulted' => null,
        'informed' => null,
        'anumgoal' => null,
        'anumbudget' => null,
        'desc' => null,
    ]);

    return redirect()->route('list.tablelist')->with('success', 'Data berhasil disimpan! ✅');
}


public function edit($id)
{
    // Ambil data berdasarkan id
    $form = ListForm::findOrFail($id);
    $divisi = Divisi::all(); // Data divisi untuk dropdown atau checkbox

    // Kirim data ke view edit
    return view('list.edit', compact('form', 'divisi'));
}


    // Memperbarui data yang sudah ada
    public function update(Request $request, $id)
{
    // Validasi data
    $request->validate([
        'issue' => 'required|string',
        'tingkatan' => 'required',
        'status' => 'required',
        'risk' => 'required',
        // Jika data multiple untuk resiko, tindakan, dan pihak berkepentingan
        'pihak' => 'nullable|array',
        'resiko' => 'nullable|array',
        'tindakan' => 'nullable|array',
        'pic' => 'nullable|array',
    ]);

    // Cari data yang akan diupdate
    $form = ListForm::findOrFail($id);

    // Update field biasa
    $form->issue = $request->input('issue');
    $form->tingkatan = $request->input('tingkatan');
    $form->status = $request->input('status');
    $form->risk = $request->input('risk');
    $form->peluang = $request->input('peluang');

    // Simpan array dalam format JSON
    $form->pihak = json_encode($request->input('pihak', []));
    $form->resiko = json_encode($request->input('resiko', []));
    $form->tindakan = json_encode($request->input('tindakan', []));
    $form->pic = json_encode($request->input('pic', []));

    // Simpan perubahan
    $form->save();

    return redirect()->route('list.tablelist')->with('success', 'Data berhasil diupdate! ✅');
}


    // Menampilkan daftar data yang sudah disimpan
    public function tablelist(Request $request)
{
    $divisi = Divisi::all();
    $selectedDivisi = $request->get('id_divisi');
    $selectedStatus = $request->get('status');

    $query = ListForm::with('divisi');

    if ($selectedDivisi) {
        $query->where('id_divisi', $selectedDivisi);
    }

    if ($selectedStatus) {
        $query->where('status', $selectedStatus);
    }

    $forms = $query->get();

    return view('list.tablelist', compact('forms', 'divisi', 'selectedDivisi', 'selectedStatus'));
}


    // Logout dan invalidate session
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
    

    // Fungsi untuk menghapus data (jika diperlukan)
    public function destroy($id)
    {
        // Implementasikan fungsi ini jika diperlukan
    }
}
