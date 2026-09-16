<?php

namespace Tests\Feature;

use App\Models\Kontingen;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_beranda_page_can_be_rendered(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('PEKAN OLAHRAGA PROVINSI');
    }

    public function test_jadwal_page_can_be_rendered(): void
    {
        $response = $this->get('/jadwal');
        $response->assertStatus(200);
    }

    public function test_klasemen_page_can_be_rendered(): void
    {
        $response = $this->get('/klasemen');
        $response->assertStatus(200);
        $response->assertSee('Klasemen Medali');
    }

    public function test_hasil_page_can_be_rendered(): void
    {
        $response = $this->get('/hasil');
        $response->assertStatus(200);
    }

    public function test_pengumuman_page_can_be_rendered(): void
    {
        $response = $this->get('/pengumuman');
        $response->assertStatus(200);
    }

    public function test_profil_kontingen_page_can_be_rendered(): void
    {
        $kontingen = Kontingen::first();
        $this->assertNotNull($kontingen);

        $response = $this->get('/kontingen/'.$kontingen->slug);
        $response->assertStatus(200);
    }

    public function test_internal_hasil_terbaru_api(): void
    {
        $response = $this->getJson('/internal/hasil-terbaru');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }
}
