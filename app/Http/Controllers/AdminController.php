<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all(); // Ambil semua data user
        return view('admin.index', compact('users')); // Kirim data user ke view
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_user' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user,email',
            'role' => 'required|in:admin,user',
            // 'password' validation removed as we set a default password
        ]);

        User::create([
            'nama_user' => $request->nama_user,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make('password123'), // Set a default hashed password
        ]);

        return redirect()->route('admin.kelolaakun')->with('success', 'User berhasil ditambahkan!  ✅');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'nama_user' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:user,email,' . $id,
        'role' => 'required|in:admin,user',
        'password' => 'nullable|string|min:8|confirmed', // Validasi password, jika ada
    ]);

    $user = User::findOrFail($id);

    $userData = [
        'nama_user' => $validated['nama_user'],
        'email' => $validated['email'],
        'role' => $validated['role'],
    ];

    // Jika password diinputkan, tambahkan ke data yang akan diupdate
    if ($request->filled('password')) {
        $userData['password'] = Hash::make($validated['password']);
    }

    $user->update($userData);

    return redirect()->route('admin.kelolaakun')->with('success', 'Data berhasil diperbarui! ✅');
}


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.kelolaakun')->with('danger', 'User berhasil dihapus! ❌');
    }
}
