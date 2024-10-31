<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_action_success()
    {
        // Setup: Buat user dengan password yang telah di-hash
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Melakukan login dengan kredensial yang benar
        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        // Assert bahwa login berhasil
        $response->assertRedirect('/');
        $this->assertTrue(Auth::check());
        $this->assertEquals('Selamat Datang!', session('success'));
    }

    public function test_login_action_failure()
    {
        // Melakukan login dengan kredensial yang salah
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert bahwa login gagal
        $response->assertRedirect('/');
        $this->assertFalse(Auth::check());
        $this->assertEqualsIgnoringCase('Email atau password salah! ❌', session('error'));
    }

    public function test_assert_numeric_values()
    {
        // Membuat data dummy untuk pengecekan numeric
        $result = 10;
        $resultFloat = 10.0;
        $resultNan = acos(2); // Menghasilkan NaN

        // Cek tipe data
        $this->assertIsInt($result);
        $this->assertIsFloat($resultFloat);
        $this->assertNan($resultNan);

        // Cek nilai dengan delta
        $this->assertEqualsWithDelta(10.0, $result, 0.1);
    }

    public function test_assert_canonicalizing_array()
    {
        $expectedRoles = ['admin', 'user'];
        $actualRoles = ['user', 'admin'];

        // Membandingkan dua array tanpa memperhatikan urutannya
        $this->assertEqualsCanonicalizing($expectedRoles, $actualRoles);
    }
}
