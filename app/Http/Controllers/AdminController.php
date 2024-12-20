<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
{
    $query = User::query();
    $divisi = Divisi::orderBy('nama_divisi', 'asc')->get(); // Urutkan divisi A-Z

    // Filter berdasarkan nama_user
    if ($request->filled('nama_user')) {
        $query->where('nama_user', 'like', '%' . $request->nama_user . '%');
    }

    // Filter berdasarkan role
    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    // Filter berdasarkan divisi
    if ($request->filled('divisi')) {
        $query->where('divisi', 'like', '%' . $request->divisi . '%');
    }

    // Urutkan nama_user dan divisi A-Z
    $query->orderBy('nama_user', 'asc')->orderBy('divisi', 'asc');

    // Ambil semua data user setelah difilter
    $users = $query->get();

    return view('admin.index', compact('users', 'divisi'));
}

    public function create()
    {
        // $divisi = Divisi::all();
        $divisi = Divisi::orderBy('nama_divisi', 'asc')->get();

        return view('admin.create',compact('divisi'));
    }

    public function store(Request $request)
{
    // Validasi permintaan
    $request->validate([
        'nama_user' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:user,email',
        'role' => 'required|in:admin,staff,manajemen,manager,supervisor',
        'type' => 'required|array', // Validasi 'type' sebagai array dari id divisi
        'type.*' => 'string|exists:divisi,id', // Pastikan setiap ID di 'type' ada di divisi
        'divisi' => 'required|string', // divisi diharuskan berupa string
    ]);

    // Ambil nama divisi berdasarkan ID yang dipilih
    $divisiNama = Divisi::find($request->divisi)->nama_divisi;

    // Create the user
    User::create([
        'nama_user' => $request->nama_user,
        'email' => $request->email,
        'role' => $request->role,
        'divisi' => $divisiNama,  // Simpan nama divisi, bukan ID
        'password' => Hash::make('password123'), // Password default
        'type' => json_encode($request->type), // Simpan ID divisi yang dipilih dalam format JSON
    ]);

    return redirect()->route('admin.kelolaakun')->with('success', 'User berhasil ditambahkan!  ✅');
}


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $divisi = Divisi::all();
        $selectedDivisi = json_decode($user->type, true);
        return view('admin.edit', compact('user','divisi','selectedDivisi'));
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'nama_user' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:user,email,' . $id,
        'role' => 'required|in:admin,staff,manajemen,manager,supervisor',
        'divisi' => 'nullable|string|max:255', // Menyimpan nama divisi
        'password' => 'nullable|string|min:8|confirmed', // Validasi password, jika ada
        'type' => 'required|array', // Validasi untuk type (divisi)
        'type.*' => 'integer|exists:divisi,id', // Validasi setiap ID divisi yang dipilih
    ]);

    $user = User::findOrFail($id);

    // Ambil nama divisi berdasarkan ID divisi yang dipilih
    $divisiNama = Divisi::find($validated['divisi'])->nama_divisi ?? null; // Mencari nama divisi berdasarkan id yang dipilih

    $userData = [
        'nama_user' => $validated['nama_user'],
        'email' => $validated['email'],
        'divisi' => $divisiNama, // Simpan nama divisi, bukan ID
        'role' => $validated['role'],
        'type' => json_encode($validated['type']), // Simpan ID divisi sebagai JSON
    ];

    // Jika password diinputkan, tambahkan ke data yang akan diupdate
    if ($request->filled('password')) {
        $userData['password'] = Hash::make($validated['password']);
    }

    // Update data user
    $user->update($userData);

    return redirect()->route('admin.kelolaakun')->with('success', 'Data berhasil diperbarui! ✅');
}


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.kelolaakun')->with('danger', 'User berhasil dihapus! ❌');
    }

    public function divisi(Request $request)
    {
        $query = Divisi::query();

        if ($request->filled('nama_divisi')) {
            $query->where('nama_divisi', 'like', '%' . $request->nama_divisi . '%');
        }

        $divisis = $query->orderBy('nama_divisi', 'asc')->get();
        return view('admin.divisi', compact('divisis'));
    }

    public function createDivisi()
    {
        return view('admin.create_divisi'); // Menampilkan form untuk membuat divisi baru
    }

    public function storeDivisi(Request $request)
    {
        $request->validate([
        'nama_divisi' => 'required|string|max:255|unique:divisi,nama_divisi',
        ]);

        Divisi::create([
        'nama_divisi' => $request->nama_divisi,
    ]);

    return redirect()->route('admin.divisi')->with('success', 'Divisi berhasil ditambahkan! ✅');
    }

    public function editDivisi($id)
    {
        $divisi = Divisi::findOrFail($id);
        return view('admin.edit_divisi', compact('divisi'));
    }

    public function updateDivisi(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_divisi' => 'required|string|max:255',
        ]);

        $divisi = Divisi::findOrFail($id);

        $divisiData = [
            'nama_divisi' => $validated['nama_divisi'],
        ];

        $divisi->update($divisiData);

        return redirect()->route('admin.divisi')->with('success', 'Data berhasil diperbarui! ✅');
    }

    public function destroyDivisi($id)
    {
        $divisi = Divisi::findOrFail($id);
        $divisi->delete();

        return redirect()->route('admin.divisi')->with('danger', 'User berhasil dihapus! ❌');
    }

}
