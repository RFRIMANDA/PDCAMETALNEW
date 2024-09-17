<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\ListForm;
use App\Models\ListKecil;
use App\Models\Tindakan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ListController extends Controller
{
    // Menampilkan halaman utama dengan data divisi
    public function index(Request $request)
    {
        $divisi = Divisi::all();
        $forms = ListForm::all(); // Atau dengan filter sesuai request
        return view('list.listregister', compact('forms', 'divisi'));
    }

    // Menampilkan halaman create dengan data divisi
    public function create($id)
    {
        $enchan = $id;
        $divisi = Divisi::all();
        return view('list.create', compact('enchan', 'divisi','id'));
    }

    // Menyimpan data dari form create
    public function store(Request $request)
{
    // Validasi input
    // dd($request->all());
    $validated = $request->validate([
        'id_divisi' => 'required|exists:divisi,id',
        'issue' => 'required|string',
        'peluang' => 'nullable|string',
        'tingkatan' => 'nullable|string',
        'status' => 'nullable|in:OPEN,ON PROGRESS,CLOSE',
        'risk' => 'nullable|in:HIGH,MEDIUM,LOW',
        'pic.*' => 'required', // Menggunakan array untuk validasi multiple
        'resiko.*' => 'required', // Menggunakan array untuk validasi multiple
        'pihak.*' => 'required', // Menggunakan array untuk validasi multiple
        'before' => 'nullable', 
        'after' => 'nullable', 
    ]);

    // Simpan data ListForm
    $listForm = ListForm::create([
        'id_divisi' => $request->input('id_divisi'),
        'issue' => $request->input('issue'),
        'peluang' => $request->input('peluang'),
        'tingkatan' => $request->input('tingkatan'),
        'status' => $request->input('status'),
        'risk' => $request->input('risk'),
        'before' => $request->input('before'),
        'after' => $request->input('after'),
    ]);

    // Looping untuk simpan data dinamis Tindakan
    foreach ($request->input('tindakan', []) as $key => $tindakan) {
        if (!empty($tindakan) && !empty($request->input('pihak')[$key]) && !empty($request->input('pic')[$key])) {
            // Insert ke tabel Tindakan
            $newTindakan = Tindakan::create([
                'id_listform' => $listForm->id,  // Menghubungkan dengan ListForm
                'nama_tindakan' => $tindakan,
                'pic' => $request->input('pic')[$key],
                'resiko' => $request->input('resiko')[$key] ?? null,  // Jika ada resiko, simpan, jika tidak null
                'pihak' => $request->input('pihak')[$key],
            ]);

            // Setelah insert ke Tindakan, simpan id_tindakan ke ListKecil
            ListKecil::create([
                'id_tindakan' => $newTindakan->id, // Hubungkan dengan tindakan baru
            ]);
        }
    }

    return redirect()->route('list.tablelistawal', $listForm->id_divisi)->with('success', 'Data berhasil disimpan! ✅');
}


    public function edit($id, $index = null)
    {
        // Ambil data berdasarkan id
        $tindakanList = Tindakan::where('id_listform',$id)->get();
        $form = ListForm::findOrFail($id);
        $divisi = Divisi::all(); // Data divisi untuk dropdown atau checkbox

        // Ambil nilai tindakan dari ListForm berdasarkan id
        $same = $form->tindakan;

        // Pisahkan data tindakan berdasarkan koma
        $tindakanArray = explode(",", $same);

        // Cek apakah indeks yang diminta ada di array
        if ($index !== null && isset($tindakanArray[$index])) {
            $dataarray = $tindakanArray[$index];
        } else {
            $dataarray = null; // Jika tidak ada, set sebagai null atau pesan error
        }

        return view('list.edit', compact('form', 'divisi', 'dataarray', 'tindakanArray','tindakanList'));
    }

    // Memperbarui data yang sudah ada
    public function update(Request $request, $id)
{
    // Validasi data
    // dd($request->all());
    $request->validate([
    
        'issue' => 'required|string',
        'tingkatan' => 'required',
        'status' => 'required',
        'risk' => 'required',
        'before' => 'required',
        'after' => 'required',
    ]);

    // Cari data yang akan diupdate
    $form = ListForm::findOrFail($id);

    // Update field biasa
    $form->issue = $request->input('issue');
    $form->tingkatan = $request->input('tingkatan');
    $form->status = $request->input('status');
    $form->risk = $request->input('risk');
    $form->peluang = $request->input('peluang');
    $form->before = $request->input('before');
    $form->after = $request->input('after');

    // Simpan perubahan di ListForm
    $form->save();

    // Hapus data Tindakan lama yang terkait dengan ListForm ini
    Tindakan::where('id_listform', $form->id)->delete();

    // Loop untuk meng-insert setiap tindakan, pihak, resiko, dan pic yang diinputkan
    foreach ($request->input('tindakan', []) as $key => $tindakan) {
        if (!empty($tindakan) && !empty($request->input('pihak')[$key]) && !empty($request->input('pic')[$key])) {
            // Insert ke tabel Tindakan
            $newTindakan = Tindakan::create([
                'id_listform' => $form->id,  // Menghubungkan dengan ListForm
                'nama_tindakan' => $tindakan,
                'pic' => $request->input('pic')[$key],
                'resiko' => $request->input('resiko')[$key] ?? null,  // Jika ada resiko, simpan, jika tidak null
                'pihak' => $request->input('pihak')[$key],
            ]);

            // Setelah insert ke Tindakan, simpan id_tindakan ke ListKecil
            ListKecil::updateOrCreate(
                ['id_tindakan' => $newTindakan->id] // Field yang diupdate atau diinsert
            );
        }
    }
    // dd($form);
    $back = ListForm::where('id', $id)->value('id_divisi');
    // dd($back);

    return redirect()->route('list.tablelistawal', $back)->with('success', 'Data berhasil diupdate! ✅');
}

public function tablelistawal($id)
{
    // Mengambil semua form berdasarkan id divisi
    $forms = ListForm::where('id_divisi', $id)->get();

    // Loop setiap form untuk mendapatkan tindakan yang terkait
    $data = [];  // Array untuk menampung data tindakan
    $divisi = [];  // Array untuk menampung pihak

    foreach ($forms as $form) {
        // Mengambil tindakan berdasarkan id_listform
        $tindakanList = Tindakan::where('id_listform', $form->id)->get();

        // Menyimpan tindakan yang sesuai
        $data[$form->id] = $tindakanList;

        // Mengambil 'pihak' dari setiap tindakan
        $pihak = Tindakan::where('id_listform', $form->id)->pluck('pihak');

        // Menambahkan 'pihak' ke dalam array divisi
        $divisi[$form->id] = Divisi::whereIn('id', $pihak)->get();
    }

    // Kirim data ke view
    return view('list.tablelist', compact('forms', 'data', 'divisi'));
}



    // Menampilkan daftar data yang sudah disimpan
    public function tablelist(Request $request)
{
    $divisi = Divisi::all();
    $selectedStatus = $request->get('status');
    
    $query = ListForm::with('divisi');

    if ($selectedStatus) {
        $query->where('status', $selectedStatus);
    }

    $forms = $query->get();

    return view('list.tablelist', compact('forms', 'divisi', 'selectedStatus'));
}

public function biglist()
{
    // Fetch all data from ListForm
    $Alldata = ListForm::all();
    $Tindak = Tindakan::all();
    $Divisi = Divisi::all();  // Get all Divisi data

    // Pass $Alldata, $Tindak, and $Divisi to the view
    return view('list.biglist', compact('Alldata', 'Tindak', 'Divisi'));
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
        $form = ListForm::findOrFail($id);
        $form->delete();
        return redirect()->route('list.tablelist')->with('success', 'Data berhasil dihapus! ✅');
    }

}
