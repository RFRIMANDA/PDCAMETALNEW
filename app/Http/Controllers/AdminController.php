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
            $query->where('divisi', $request->divisi);
        }

        // Ambil semua data user setelah difilter
        $users = $query->get();

        return view('admin.index', compact('users'));
    }

    public function create()
    {
        $divisi = Divisi::all();

        return view('admin.create',compact('divisi'));
    }

    public function store(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'nama_user' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:user,email',
        'role' => 'required|in:admin,staff,manajemen,manager,supervisor',
        'type' => 'required|array', // Validate 'type' as an array of divisi IDs
        'type.*' => 'string|exists:divisi,id', // Ensure each ID in 'type' exists in divisi
        'divisi' => 'required|string', // Assuming divisi is a string, change if it's a relation
    ]);

    // Create the user
    User::create([
        'nama_user' => $request->nama_user,
        'email' => $request->email,
        'role' => $request->role,
        'divisi' => $request->divisi,
        'password' => Hash::make('password123'), // Default password
        'type' => json_encode($request->type), // Store selected divisi IDs as JSON
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
            'divisi' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed', // Validasi password, jika ada
            'type' => 'required|array', // Validasi untuk type (divisi)
            'type.*' => 'integer|exists:divisi,id', // Validasi setiap ID divisi yang dipilih
        ]);

        $user = User::findOrFail($id);

        $userData = [
            'nama_user' => $validated['nama_user'],
            'email' => $validated['email'],
            'divisi' => $validated['divisi'],
            'role' => $validated['role'],
            'type' => $validated['type'],
            // 'type' => $validated['type'],
        ];

        // Jika password diinputkan, tambahkan ke data yang akan diupdate
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Update data user
        $user->update($userData);

        // Simpan atau update divisi yang dipilih
        // $user->divisi()->sync($validated['type']); // Menyimpan relasi many-to-many, jika ada

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

        $divisis = $query->get();
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
