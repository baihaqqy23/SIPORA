<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Kontingen;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_register_kontingen_page_can_be_rendered(): void
    {
        $response = $this->get('/daftar-kontingen');
        $response->assertStatus(200);
    }

    public function test_kontingen_can_register_with_surat_mandat_upload(): void
    {
        Storage::fake('private');

        $event = Event::first();
        $this->assertNotNull($event);

        $file = UploadedFile::fake()->create('surat_mandat.pdf', 500, 'application/pdf');

        $response = $this->post('/daftar-kontingen', [
            'nama_kontingen' => 'Kontingen Kabupaten Garut',
            'provinsi' => 'Jawa Barat',
            'kota' => 'Kabupaten Garut',
            'nama_ofisial' => 'Budi Santoso',
            'no_hp_ofisial' => '081234567890',
            'email' => 'garut@kontingen.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'surat_mandat' => $file,
            'setuju_privasi' => '1',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kontingen', [
            'nama' => 'Kontingen Kabupaten Garut',
            'email' => 'garut@kontingen.id',
            'status' => 'menunggu',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'garut@kontingen.id',
            'role' => 'kontingen',
        ]);

        $kontingen = Kontingen::where('email', 'garut@kontingen.id')->first();
        $this->assertNotNull($kontingen->surat_mandat_path);
        Storage::disk('private')->assertExists($kontingen->surat_mandat_path);
    }

    public function test_duplicate_kontingen_provinsi_and_kota_returns_validation_error(): void
    {
        $response = $this->post('/daftar-kontingen', [
            'nama_kontingen' => 'Kontingen Bandung Duplikat',
            'provinsi' => 'Jawa Barat',
            'kota' => 'Kota Bandung', // Already seeded
            'nama_ofisial' => 'Ofisial Baru',
            'no_hp_ofisial' => '081234567899',
            'email' => 'bandung_baru@kontingen.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'setuju_privasi' => '1',
        ]);

        $response->assertSessionHasErrors(['kota']);
    }
}
