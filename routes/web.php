<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cabor;
use App\Http\Controllers\Internal;
use App\Http\Controllers\Kontingen as KontingenCtrl;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Publik;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest / Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::get('/daftar-kontingen', [AuthController::class, 'showRegisterKontingen'])->name('register.kontingen');
    Route::post('/daftar-kontingen', [AuthController::class, 'registerKontingen'])->name('register.kontingen.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ganti-password', [AuthController::class, 'showGantiPassword'])->name('ganti-password');
    Route::post('/ganti-password', [AuthController::class, 'gantiPassword'])->name('ganti-password.post');
});

/*
|--------------------------------------------------------------------------
| Publik Routes (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [Publik\BerandaController::class, 'index'])->name('publik.beranda');
Route::get('/jadwal', [Publik\JadwalController::class, 'index'])->name('publik.jadwal');
Route::get('/rundown', [Publik\RundownController::class, 'index'])->name('publik.rundown');
Route::get('/hasil', [Publik\HasilController::class, 'index'])->name('publik.hasil');
Route::get('/klasemen', [Publik\KlasemenController::class, 'index'])->name('publik.klasemen');
Route::get('/pengumuman', [Publik\PengumumanController::class, 'index'])->name('publik.pengumuman');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Event
    Route::resource('event', Admin\EventController::class);
    Route::post('event/{event}/aktivasi', [Admin\EventController::class, 'aktivasi'])->name('event.aktivasi');

    // Cabor & Nomor Lomba
    Route::resource('cabor', Admin\CaborController::class);
    Route::resource('cabor.nomor-lomba', Admin\NomorLombaController::class)->shallow();

    // Venue & Lapangan
    Route::resource('venue', Admin\VenueController::class);
    Route::resource('venue.lapangan', Admin\LapanganController::class)->shallow();

    // Kontingen & Atlet
    Route::resource('kontingen', Admin\KontingenController::class);
    Route::resource('atlet', Admin\AtletController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    // Verifikasi Pendaftaran
    Route::get('/verifikasi', [Admin\VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{pendaftaran}', [Admin\VerifikasiController::class, 'show'])->name('verifikasi.show');
    Route::post('/verifikasi/{pendaftaran}/proses', [Admin\VerifikasiController::class, 'proses'])->name('verifikasi.proses');
    Route::post('/verifikasi/massal', [Admin\VerifikasiController::class, 'massal'])->name('verifikasi.massal');

    // Drawing & Bracket
    Route::get('/drawing', [Admin\DrawingController::class, 'index'])->name('drawing.index');
    Route::get('/bracket/{nomorLomba}', [Admin\DrawingController::class, 'show'])->name('bracket.show');
    Route::post('/bracket/{nomorLomba}/preview', [Admin\DrawingController::class, 'preview'])->name('bracket.preview');
    Route::post('/bracket/{nomorLomba}/simpan', [Admin\DrawingController::class, 'simpan'])->name('bracket.simpan');
    Route::post('/bracket/{nomorLomba}/reset', [Admin\DrawingController::class, 'reset'])->name('bracket.reset');

    // Papan Jadwal
    Route::get('/papan-jadwal', [Admin\PapanJadwalController::class, 'index'])->name('papan-jadwal.index');
    Route::post('/papan-jadwal/alokasi', [Admin\PapanJadwalController::class, 'alokasi'])->name('papan-jadwal.alokasi');
    Route::post('/papan-jadwal/pindah', [Admin\PapanJadwalController::class, 'pindah'])->name('papan-jadwal.pindah');
    Route::post('/papan-jadwal/{pertandingan}/publikasi', [Admin\PapanJadwalController::class, 'publikasi'])->name('papan-jadwal.publikasi');
    Route::post('/papan-jadwal/{pertandingan}/tunda', [Admin\PapanJadwalController::class, 'tunda'])->name('papan-jadwal.tunda');
    Route::get('/papan-jadwal/export-pdf', [Admin\PapanJadwalController::class, 'exportPdf'])->name('papan-jadwal.export-pdf');

    // Panitia
    Route::resource('panitia', Admin\PanitiaController::class);
    Route::resource('penugasan', Admin\PenugasanPanitiaController::class)->only(['store', 'destroy']);

    // Rundown
    Route::get('/rundown', [Admin\RundownController::class, 'index'])->name('rundown.index');
    Route::get('/rundown/{tanggal}', [Admin\RundownController::class, 'show'])->name('rundown.show');
    Route::resource('acara-rundown', Admin\AcaraRundownController::class)->except(['show']);
    Route::get('/rundown/{tanggal}/export-pdf', [Admin\RundownController::class, 'exportPdf'])->name('rundown.export-pdf');

    // Hasil & Medali
    Route::get('/hasil', [Admin\HasilController::class, 'index'])->name('hasil.index');
    Route::get('/hasil/{pertandingan}', [Admin\HasilController::class, 'show'])->name('hasil.show');
    Route::post('/hasil/{pertandingan}', [Admin\HasilController::class, 'simpan'])->name('hasil.simpan');
    Route::post('/hasil/{pertandingan}/batal', [Admin\HasilController::class, 'batalkan'])->name('hasil.batalkan');

    // Klasemen
    Route::get('/klasemen', [Admin\KlasemenController::class, 'index'])->name('klasemen.index');
    Route::get('/klasemen/export-pdf', [Admin\KlasemenController::class, 'exportPdf'])->name('klasemen.export-pdf');

    // Pengumuman
    Route::resource('pengumuman', Admin\PengumumanController::class);

    // Laporan & Audit
    Route::get('/laporan', [Admin\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [Admin\LaporanController::class, 'export'])->name('laporan.export');
    Route::get('/audit-log', [Admin\AuditLogController::class, 'index'])->name('audit-log.index');
});

/*
|--------------------------------------------------------------------------
| PJ Cabor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,pj_cabor'])->prefix('cabor')->name('cabor.')->group(function () {
    Route::get('/dashboard', [Cabor\DashboardCaborController::class, 'index'])->name('dashboard');
    Route::get('/peserta', [Cabor\PesertaController::class, 'index'])->name('peserta.index');
    Route::get('/peserta/{pendaftaran}/verifikasi', [Cabor\PesertaController::class, 'verifikasiShow'])->name('peserta.verifikasi');
    Route::post('/peserta/{pendaftaran}/verifikasi', [Cabor\PesertaController::class, 'verifikasiProses'])->name('peserta.verifikasi.proses');
    Route::get('/jadwal', [Cabor\JadwalCaborController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal/pindah', [Cabor\JadwalCaborController::class, 'pindah'])->name('jadwal.pindah');
    Route::get('/hasil/{pertandingan}', [Cabor\HasilCaborController::class, 'show'])->name('hasil.show');
    Route::post('/hasil/{pertandingan}', [Cabor\HasilCaborController::class, 'simpan'])->name('hasil.simpan');
    Route::get('/bracket/{nomorLomba}', [Cabor\DrawingCaborController::class, 'show'])->name('bracket.show');
    Route::post('/bracket/{nomorLomba}/preview', [Cabor\DrawingCaborController::class, 'preview'])->name('bracket.preview');
    Route::post('/bracket/{nomorLomba}/simpan', [Cabor\DrawingCaborController::class, 'simpan'])->name('bracket.simpan');
});

/*
|--------------------------------------------------------------------------
| Kontingen Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kontingen'])->prefix('kontingen')->name('kontingen.')->group(function () {
    Route::get('/dashboard', [KontingenCtrl\DashboardKontingenController::class, 'index'])->name('dashboard');

    // Atlet
    Route::get('/atlet', [KontingenCtrl\AtletController::class, 'index'])->name('atlet.index');
    Route::get('/atlet/create', [KontingenCtrl\AtletController::class, 'create'])->name('atlet.create');
    Route::post('/atlet', [KontingenCtrl\AtletController::class, 'store'])->name('atlet.store');
    Route::get('/atlet/{atlet}/edit', [KontingenCtrl\AtletController::class, 'edit'])->name('atlet.edit');
    Route::put('/atlet/{atlet}', [KontingenCtrl\AtletController::class, 'update'])->name('atlet.update');
    Route::delete('/atlet/{atlet}', [KontingenCtrl\AtletController::class, 'destroy'])->name('atlet.destroy');
    Route::post('/atlet/{atlet}/berkas', [KontingenCtrl\AtletController::class, 'uploadBerkas'])->name('atlet.berkas.upload');
    Route::delete('/atlet/berkas/{berkas}', [KontingenCtrl\AtletController::class, 'deleteBerkas'])->name('atlet.berkas.delete');

    // Pendaftaran
    Route::get('/pendaftaran', [KontingenCtrl\PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/create', [KontingenCtrl\PendaftaranController::class, 'create'])->name('pendaftaran.create');
    Route::post('/pendaftaran', [KontingenCtrl\PendaftaranController::class, 'store'])->name('pendaftaran.store');
    Route::delete('/pendaftaran/{pendaftaran}', [KontingenCtrl\PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

    // Jadwal Kontingen
    Route::get('/jadwal', [KontingenCtrl\JadwalKontingenController::class, 'index'])->name('jadwal.index');

    // Berkas (download terproteksi)
    Route::get('/berkas/{berkas}', [KontingenCtrl\BerkasController::class, 'download'])->name('berkas.download');
});

// Profil Kontingen Publik (diletakkan setelah prefix kontingen agar tidak menimpa /kontingen/dashboard)
Route::get('/kontingen/{slug}', [Publik\ProfilKontingenController::class, 'show'])->name('publik.kontingen');

/*
|--------------------------------------------------------------------------
| Notifikasi Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
});

/*
|--------------------------------------------------------------------------
| Internal JSON Endpoints (for Alpine.js fetch)
|--------------------------------------------------------------------------
*/
Route::prefix('internal')->name('internal.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/papan-jadwal', [Internal\PapanJadwalApiController::class, 'index'])->name('papan-jadwal');
        Route::get('/atlet-eligible', [Internal\AtletEligibleController::class, 'index'])->name('atlet-eligible');
        Route::get('/notifikasi', [Internal\NotifikasiApiController::class, 'index'])->name('notifikasi');
    });

    // Hasil public - throttled (HSL-06 polling publik)
    Route::get('/hasil-terbaru', [Internal\HasilTerbaruController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('hasil-terbaru');
});
