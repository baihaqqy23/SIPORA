<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KontingenPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_kontingen_user_redirects_to_kontingen_dashboard_on_login(): void
    {
        $kontingenUser = User::where('role', 'kontingen')->first();
        $this->assertNotNull($kontingenUser);

        $response = $this->post('/login', [
            'email' => $kontingenUser->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('kontingen.dashboard'));
        $this->assertAuthenticatedAs($kontingenUser);
    }

    public function test_kontingen_dashboard_can_be_rendered(): void
    {
        $kontingenUser = User::where('role', 'kontingen')->first();

        $response = $this->actingAs($kontingenUser)->get('/kontingen/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal Kontingen');
    }

    public function test_kontingen_atlet_pages_can_be_rendered(): void
    {
        $kontingenUser = User::where('role', 'kontingen')->first();

        $response = $this->actingAs($kontingenUser)->get('/kontingen/atlet');
        $response->assertStatus(200);

        $responseCreate = $this->actingAs($kontingenUser)->get('/kontingen/atlet/create');
        $responseCreate->assertStatus(200);
    }

    public function test_kontingen_pendaftaran_pages_can_be_rendered(): void
    {
        $kontingenUser = User::where('role', 'kontingen')->first();

        $response = $this->actingAs($kontingenUser)->get('/kontingen/pendaftaran');
        $response->assertStatus(200);

        $responseCreate = $this->actingAs($kontingenUser)->get('/kontingen/pendaftaran/create');
        $responseCreate->assertStatus(200);
    }

    public function test_kontingen_jadwal_page_can_be_rendered(): void
    {
        $kontingenUser = User::where('role', 'kontingen')->first();

        $response = $this->actingAs($kontingenUser)->get('/kontingen/jadwal');
        $response->assertStatus(200);
    }

    public function test_kontingen_accessing_admin_page_gets_403(): void
    {
        $kontingenUser = User::where('role', 'kontingen')->first();

        $response = $this->actingAs($kontingenUser)->get('/admin/dashboard');
        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak');
    }
}
