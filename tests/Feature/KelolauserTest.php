<?php

namespace Tests\Unit;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KelolauserTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        User::factory()->create(['nama_user' => 'John Doe', 'role' => 'admin']);
        $response = $this->get('/admin'); // Sesuaikan dengan route yang tepat

        $response->assertStatus(200);
        $this->assertEquals('John Doe', User::first()->nama_user);
    }

    public function testStoreUser()
    {
        $response = $this->post('/admin/store', [
            'nama_user' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'role' => 'user',
            'type' => [1, 2]
        ]);

        $response->assertRedirect('/admin/kelolaakun');
        $this->assertEquals('Jane Doe', User::first()->nama_user);
        $this->assertEqualsCanonicalizing(['1', '2'], json_decode(User::first()->type));
    }

    public function testUpdateUserRoleIgnoringCase()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->patch("/admin/{$user->id}/update", [
            'role' => 'USER',
            'nama_user' => $user->nama_user,
            'email' => $user->email,
            'type' => []
        ]);

        $response->assertRedirect('/admin/kelolaakun');
        $this->assertEqualsIgnoringCase('user', User::first()->role);
    }

    public function testUpdateUserWithDelta()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->patch("/admin/{$user->id}/update", [
            'nama_user' => $user->nama_user,
            'email' => $user->email,
            'role' => 'admin',
            'type' => []
        ]);

        $response->assertRedirect('/admin/kelolaakun');
        $originalId = 1000;  // Assuming a prior known value to compare
        $this->assertEqualsWithDelta($originalId, $user->id, 10);
    }

    public function testDivisiIsNumeric()
    {
        $divisi = Divisi::factory()->create(['nama_divisi' => 'IT']);
        $this->assertIsNumeric($divisi->id);
    }

    public function testDivisiIsInt()
    {
        $divisi = Divisi::factory()->create();
        $this->assertIsInt($divisi->id);
    }

    public function testDivisiIsFloat()
    {
        $price = 10.50;
        $this->assertIsFloat($price);
    }

    public function testDivisiNan()
    {
        $nanValue = log(-1); // Log dari nilai negatif menghasilkan NaN
        $this->assertNan($nanValue);
    }
}
