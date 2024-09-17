<?php

namespace App\Http\Controllers;

use App\Models\ListKecil;
use App\Models\ListForm;
use App\Models\Tindakan;
use Illuminate\Http\Request;

class ListKecilController extends Controller
{
    public function index($id, $index)
    {
        $same = ListForm::where('id', $id)->value('tindakan');
        $tindakanArray = explode(",", $same);
        $dataarray = isset($tindakanArray[$index]) ? $index : null;

        $data = ListForm::where('id', $id)->value('tindakan');
        $listKecil = ListKecil::where('index', $index)->get();

        return view('listkecil.index', compact('listKecil', 'data', 'dataarray'));
    }

    public function show($id)
    {
        $form = ListForm::findOrFail($id);
        $listKecil = ListKecil::where('id_tindakan', $id)->first();
        // dd($listKecil);
    
        // Tambahkan debug untuk melihat apakah $listKecil berisi data
        if (!$listKecil) {
            // dd("ListKecil not found for id_tindakan: " . $id);
            return redirect()->route('listkecil.index')->with('error', 'ListKecil not found.');
        }
    
        $selectedTindakan = Tindakan::get()->value('nama_tindakan');
    
        return view('listkecil.show', compact('form', 'listKecil', 'selectedTindakan'));
    }
    

    public function edit($id, $index)
{
    $form = ListForm::findOrFail($id);
    $tindakanList = explode(',', $form->tindakan);
    $selectedTindakan = isset($tindakanList[$index]) ? trim($tindakanList[$index]) : null;

    $listKecil = ListKecil::where('id_tindakan', $id)->firstOrFail();

    return view('listkecil.edit', compact('form', 'listKecil', 'selectedTindakan'));
}

public function update(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'realisasi' => 'required|string|max:255',
        'date' => 'nullable|date',
        'responsible' => 'nullable|string|max:255',
        'accountable' => 'nullable|string|max:255',
        'consulted' => 'nullable|string|max:255',
        'informed' => 'nullable|string|max:255',
        'anumgoal' => 'nullable|string|max:255',
        'anumbudget' => 'nullable|string|max:255',
        'desc' => 'nullable|string',
    ]);

    // Cari ListKecil berdasarkan ID
    $listKecil = ListKecil::findOrFail($id);

    // Update data
    $listKecil->update([
        'realisasi' => $request->input('realisasi'),
        'date' => $request->input('date'),
        'responsible' => $request->input('responsible'),
        'accountable' => $request->input('accountable'),
        'consulted' => $request->input('consulted'),
        'informed' => $request->input('informed'),
        'anumgoal' => $request->input('anumgoal'),
        'anumbudget' => $request->input('anumbudget'),
        'desc' => $request->input('desc'),
    ]);

    // Redirect ke route listkecil.detail dengan ID yang benar
    return redirect()->route('listkecil.detail', ['id' => $listKecil->id])
        ->with('success', 'Data updated successfully. ✅');
}

//storenya ini
public function updateDetail(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'realisasi' => 'required|string|max:255',
        'date' => 'nullable|date',
        'responsible' => 'nullable|string|max:255',
        'accountable' => 'nullable|string|max:255',
        'consulted' => 'nullable|string|max:255',
        'informed' => 'nullable|string|max:255',
        'anumgoal' => 'nullable|string|max:255',
        'anumbudget' => 'nullable|string|max:255',
        'desc' => 'nullable|string',
    ]);

    // Cari ListKecil berdasarkan ID
    $listKecil = ListKecil::findOrFail($id);

    // Update data ListKecil dengan input dari form
    $listKecil->update([
        'realisasi' => $request->input('realisasi'),
        'date' => $request->input('date'),
        'responsible' => $request->input('responsible'),
        'accountable' => $request->input('accountable'),
        'consulted' => $request->input('consulted'),
        'informed' => $request->input('informed'),
        'anumgoal' => $request->input('anumgoal'),
        'anumbudget' => $request->input('anumbudget'),
        'desc' => $request->input('desc'),
    ]);
    $listKecil = ListKecil::findOrFail($id);
    

    // Redirect ke halaman detail dengan pesan sukses
    return redirect()->route('listkecil.detail', $listKecil->id)
        ->with('success', 'Data updated successfully. ✅');
}
    //manggilnya disini kaya update
    public function detail($id)
    {
        // Ambil data ListKecil berdasarkan ID dan index
        // $listKecil = ListKecil::where('id_tindakan', $id)->where('index', $index)->firstOrFail();
        
        $listKecil = ListKecil::where('id',$id)->get();
        $same = Tindakan::where('id',$id)->get();

        // Mengembalikan data dalam format JSON
        return view('listkecil.detail', compact('listKecil','same'));
    }

    public function getDetail($id, $index)
{
    $kecil = ListKecil::find($id); // Cek apakah data form ditemukan
    if (!$kecil) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    $details = ListKecil::where('id', $id)->get(); // Ambil semua data terkait ID
    if (isset($details[$index])) {
        $detail = $details[$index];
        return response()->json([
            'realisasi' => $detail->realisasi,
            'date' => $detail->date,
        ]);
    } else {
        return response()->json(['message' => 'Detail tidak ditemukan'], 404);
    }
}

}

