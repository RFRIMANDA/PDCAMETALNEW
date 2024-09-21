<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\ListForm;
use App\Models\ListKecil;
use App\Models\Tindakan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ListController extends Controller
{
    // Menampilkan halaman utama dengan data divisi
    public function index(Request $request)
    {
        $divisi = Divisi::all();

        // Hitung jumlah form untuk setiap divisi
        foreach ($divisi as $item) {
            $item->jumlah_data = ListForm::where('id_divisi', $item->id)->count();
        }

        return view('list.listregister', compact('divisi'));
    }

    // Menampilkan halaman create dengan data divisi
    public function create($id)
    {
        $enchan = $id;
        $divisi = Divisi::all();
        return view('list.create', compact('enchan', 'divisi', 'id'));
    }

    // Menyimpan data dari form create
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'id_divisi' => 'required|exists:divisi,id',
            'issue' => 'required|string',
            'peluang' => 'nullable|string',
            'tingkatan' => 'nullable|string',
            'status' => 'nullable|in:OPEN,ON PROGRESS,CLOSE',
            'risk' => 'nullable|in:HIGH,MEDIUM,LOW',
            'pic.*' => 'required',
            'resiko.*' => 'required',
            'pihak.*' => 'required',
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
            'before' => $request->input('before') ?? null,
            'after' => $request->input('after') ?? null,
        ]);

        // Loop untuk simpan data dinamis Tindakan
        foreach ($request->input('tindakan', []) as $key => $tindakan) {
            if (!empty($tindakan) && !empty($request->input('pihak')[$key]) && !empty($request->input('pic')[$key])) {
                $newTindakan = Tindakan::create([
                    'id_listform' => $listForm->id,
                    'nama_tindakan' => $tindakan,
                    'pic' => $request->input('pic')[$key],
                    'resiko' => $request->input('resiko')[$key] ?? null,
                    'pihak' => $request->input('pihak')[$key],
                ]);

                ListKecil::create(['id_tindakan' => $newTindakan->id]);
            }
        }

        return redirect()->route('list.tablelistawal', $listForm->id_divisi)->with('success', 'Data berhasil disimpan! ✅');
    }

    // Menampilkan halaman edit
    public function edit($id, $index = null)
    {
        $tindakanList = Tindakan::where('id_listform', $id)->get();
        $form = ListForm::findOrFail($id);
        $divisi = Divisi::all();
        $same = $form->tindakan;
        $tindakanArray = explode(",", $same);

        if ($index !== null && isset($tindakanArray[$index])) {
            $dataarray = $tindakanArray[$index];
        } else {
            $dataarray = null;
        }

        return view('list.edit', compact('form', 'divisi', 'dataarray', 'tindakanArray', 'tindakanList'));
    }

    // Memperbarui data yang sudah ada
    public function update(Request $request, $id)
    {
        $request->validate([
            'issue' => 'required|string',
            'tingkatan' => 'required|string',
            'status' => 'required|in:OPEN,ON PROGRESS,CLOSE',
            'risk' => 'required|in:HIGH,MEDIUM,LOW',
            'before' => 'nullable|string',
            'after' => 'nullable|string',
        ]);
        

        $form = ListForm::findOrFail($id);

        $form->issue = $request->input('issue');
        $form->tingkatan = $request->input('tingkatan');
        $form->status = $request->input('status');
        $form->risk = $request->input('risk');
        $form->peluang = $request->input('peluang');
        $form->before = $request->input('before') ?? null;
        $form->after = $request->input('after') ?? null;
        $form->save();

        $tindakanLama = Tindakan::where('id_listform', $form->id)->get();

        foreach ($request->input('tindakan', []) as $key => $tindakan) {
            if (!empty($tindakan) && !empty($request->input('pihak')[$key]) && !empty($request->input('pic')[$key])) {
                $existingTindakan = $tindakanLama->get($key);

                if ($existingTindakan) {
                    $existingTindakan->update([
                        'nama_tindakan' => $tindakan,
                        'pic' => $request->input('pic')[$key],
                        'resiko' => $request->input('resiko')[$key] ?? null,
                        'pihak' => $request->input('pihak')[$key],
                    ]);

                    ListKecil::updateOrCreate(['id_tindakan' => $existingTindakan->id], []);
                } else {
                    $newTindakan = Tindakan::create([
                        'id_listform' => $form->id,
                        'nama_tindakan' => $tindakan,
                        'pic' => $request->input('pic')[$key],
                        'resiko' => $request->input('resiko')[$key] ?? null,
                        'pihak' => $request->input('pihak')[$key],
                    ]);

                    ListKecil::create(['id_tindakan' => $newTindakan->id]);
                }
            }
        }

        $back = ListForm::where('id', $id)->value('id_divisi');
        return redirect()->route('list.tablelistawal', $back)->with('success', 'Data berhasil diupdate! ✅');
    }

    // Menampilkan daftar form berdasarkan id divisi
    public function tablelistawal($id)
    {
        $forms = ListForm::where('id_divisi', $id)->get();
        $same = ListForm::where('id_divisi', $id)->value('id');
        $tindakan = Tindakan::where('id_listform', $same)->value('id');
        $liskecil = ListKecil::where('id_tindakan', $tindakan)->value('id');
        $data = [];
        $divisi = [];

        foreach ($forms as $form) {
            $tindakanList = Tindakan::where('id_listform', $form->id)->get();
            $data[$form->id] = $tindakanList;
            $pihak = Tindakan::where('id_listform', $form->id)->pluck('pihak');
            $divisi[$form->id] = Divisi::whereIn('id', $pihak)->get();
        }

        return view('list.tablelist', compact('forms', 'data', 'divisi'));
    }

    // Fungsi untuk generate PDF
    public function print($id)
{
    // Ambil data dari ListForm berdasarkan ID
    $form = ListForm::with('tindakan')->findOrFail($id);
    
    // Ambil data divisi yang terkait
    $divisi = Divisi::find($form->id_divisi);

    // Ambil semua tindakan terkait dari Tindakan model
    $tindakanList = Tindakan::where('id_listform', $id)->get();

    // Render view untuk PDF
    $pdf = Pdf::loadView('list.print', compact('form', 'divisi', 'tindakanList'));

    // Menghasilkan file PDF dengan nama file tertentu
    return $pdf->download('form-detail-'.$form->id.'.pdf');
}



    // Menampilkan daftar form dengan filter status
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

    // Menampilkan semua data dari ListForm
    public function biglist(Request $request)
    {
        // Ambil data divisi dan tindakan
        $Divisi = Divisi::all();
        $Tindak = Tindakan::all();

        // Ambil input filter dari request
        $selectedDivisi = $request->get('nama_divisi');
        $selectedStatus = $request->get('status');

        // Buat query untuk mengambil data dari ListForm
        $query = ListForm::with('divisi');

        // Filter berdasarkan nama divisi jika ada input
        if ($selectedDivisi) {
            $query->whereHas('divisi', function ($query) use ($selectedDivisi) {
                $query->where('nama_divisi', $selectedDivisi);
            });
        }

        // Filter berdasarkan status jika ada input
        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        // Dapatkan semua data yang sesuai dengan filter
        $Alldata = $query->get();

        return view('list.biglist', compact('Alldata', 'Tindak', 'Divisi', 'selectedDivisi', 'selectedStatus'));
    }


    // Fungsi logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    // Menghapus data
    public function destroy($id)
    {
        $form = ListForm::findOrFail($id);
        $form->delete();
        return redirect()->route('list.tablelist')->with('success', 'Data berhasil dihapus! ✅');
    }
}
