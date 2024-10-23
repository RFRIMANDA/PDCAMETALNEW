<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserDivisiSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(1); // User dengan ID 1
        $user->divisi()->attach([1, 2, 3]); // Beri akses ke divisi dengan ID 1, 2, dan 3

        // Tambahkan relasi untuk user lain jika perlu
        $user2 = User::find(2); // User dengan ID 2
        $user2->divisi()->attach([2, 4]); // Beri akses ke divisi dengan ID 2 dan 4
    }
}
