<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main.index');
})->name('main');

Route::view('/register', 'authentication.register')->name('register');
Route::view('/otp', 'authentication.otp')->name('otp');
Route::view('/login', 'authentication.login')->name('login');

Route::view('/forgot-password', 'authentication.forgot-password')->name('forgot-password');
Route::view('/verifikasi-otp', 'authentication.verifikasi-otp')->name('verifikasi-otp');
Route::view('/reset-password', 'authentication.reset-password')->name('reset-password');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::prefix('role')->name('role.')->group(function () {
        Route::view('/', 'admin.role.index')->name('index');
        Route::view('/create', 'admin.role.create')->name('create');
        Route::get('/{id_role}/edit', fn($id_role) => view('admin.role.edit', compact('id_role')))->name('edit');
        Route::get('/{id_role}/show', fn($id_role) => view('admin.role.show', compact('id_role')))->name('show');
    });
    Route::prefix('user')->name('user.')->group(function () {
        Route::view('/', 'admin.user.index')->name('index');
        Route::view('/create', 'admin.user.create')->name('create');
        Route::get('/{id_user}/show', fn($id_user) => view('admin.user.show', compact('id_user')))->name('show');
        Route::get('/{id_user}/edit', fn($id_user) => view('admin.user.edit', compact('id_user')))->name('edit');
    });
    Route::prefix('verifikator')->name('verifikator.')->group(function () {
        Route::view('/', 'admin.verifikator.index')->name('index');
        Route::view('/create', 'admin.verifikator.create')->name('create');
        Route::get('/{id_verifikator}/show', fn($id_verifikator) => view('admin.verifikator.show', compact('id_verifikator')))->name('show');
        Route::get('/{id_verifikator}/edit', fn($id_verifikator) => view('admin.verifikator.edit', compact('id_verifikator')))->name('edit');
    });
    Route::prefix('penulisbuku')->name('penulisbuku.')->group(function () {
        Route::view('/', 'admin.penulisbuku.index')->name('index');
        Route::view('/create', 'admin.penulisbuku.create')->name('create');
        Route::get('/{id_penulis_buku}/show', fn($id_penulis_buku) => view('admin.penulisbuku.show', compact('id_penulis_buku')))->name('show');
        Route::get('/{id_penulis_buku}/edit', fn($id_penulis_buku) => view('admin.penulisbuku.edit', compact('id_penulis_buku')))->name('edit');
    });
    Route::prefix('penerbitbuku')->name('penerbitbuku.')->group(function () {
        Route::view('/', 'admin.penerbitbuku.index')->name('index');
        Route::view('/create', 'admin.penerbitbuku.create')->name('create');
        Route::get('/{id_penerbit_buku}/show', fn($id_penerbit_buku) => view('admin.penerbitbuku.show', compact('id_penerbit_buku')))->name('show');
        Route::get('/{id_penerbit_buku}/edit', fn($id_penerbit_buku) => view('admin.penerbitbuku.edit', compact('id_penerbit_buku')))->name('edit');
    });
    Route::prefix('buku')->name('buku.')->group(function () {
        Route::view('/', 'admin.buku.index')->name('index');
        Route::view('/create', 'admin.buku.create')->name('create');
        Route::get('/{id_buku}/show', fn($id_buku) => view('admin.buku.show', compact('id_buku')))->name('show');
        Route::get('/{id_buku}/edit', fn($id_buku) => view('admin.buku.edit', compact('id_buku')))->name('edit');
    });
    Route::prefix('pengajuanbuku')->name('pengajuanbuku.')->group(function () {
        Route::view('/', 'admin.pengajuanbuku.index')->name('index');
        Route::view('/create', 'admin.pengajuanbuku.create')->name('create');
        Route::get('/{id_pengajuan_buku}/show', fn($id_pengajuan_buku) => view('admin.pengajuanbuku.show', compact('id_pengajuan_buku')))->name('show');
        Route::get('/{id_pengajuan_buku}/edit', fn($id_pengajuan_buku) => view('admin.pengajuanbuku.edit', compact('id_pengajuan_buku')))->name('edit');
    });
    Route::prefix('peminjamanbuku')->name('peminjamanbuku.')->group(function () {
        Route::view('/', 'admin.peminjamanbuku.index')->name('index');
        Route::get('/{id_peminjaman_buku}/show', fn($id_peminjaman_buku) => view('admin.peminjamanbuku.show', compact('id_peminjaman_buku')))->name('show');
    });
    Route::prefix('fasilitas')->name('fasilitas.')->group(function () {
        Route::view('/', 'admin.fasilitas.index')->name('index');
        Route::view('/create', 'admin.fasilitas.create')->name('create');
        Route::get('/{id_fasilitas}/show', fn($id_fasilitas) => view('admin.fasilitas.show', compact('id_fasilitas')))->name('show');
        Route::get('/{id_fasilitas}/edit', fn($id_fasilitas) => view('admin.fasilitas.edit', compact('id_fasilitas')))->name('edit');
    });
    Route::prefix('reservasifasilitas')->name('reservasifasilitas.')->group(function () {
        Route::view('/', 'admin.reservasifasilitas.index')->name('index');
        Route::view('/create', 'admin.reservasifasilitas.create')->name('create');
        Route::get('/{id_reservasi}/edit', fn($id_reservasi) => view('admin.reservasifasilitas.edit', compact('id_reservasi')))->name('edit');
        Route::get('/{id_reservasi}/show', fn($id_reservasi) => view('admin.reservasifasilitas.show', compact('id_reservasi')))->name('show');
    });
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::view('/logapproval', 'admin.logs.logapproval')->name('logapproval');
        Route::view('/logapprovalbuku', 'admin.logs.logapprovalbuku')->name('logapprovalbuku');
        Route::view('/logactivity', 'admin.logs.logactivity')->name('logactivity');
        Route::view('/logerror', 'admin.logs.logerror')->name('logerror');
        Route::view('/logdatabase', 'admin.logs.logdatabase')->name('logdatabase');
    });
});

Route::prefix('verifikator')->name('verifikator.')->group(function () {
    Route::view('/dashboard', 'verifikator.dashboard')->name('dashboard');

    Route::prefix('pengajuanbuku')->name('pengajuanbuku.')->group(function () {
        Route::view('/', 'verifikator.pengajuanbuku.index')->name('index');
        Route::get('/{id_pengajuan_buku}/show', fn($id_pengajuan_buku) => view('verifikator.pengajuanbuku.show', compact('id_pengajuan_buku')))->name('show');
    });
    Route::prefix('reservasifasilitas')->name('reservasifasilitas.')->group(function () {
        Route::view('/', 'verifikator.reservasifasilitas.index')->name('index');
        Route::get('/{id_reservasi}/show', fn($id_reservasi) => view('verifikator.reservasifasilitas.show', compact('id_reservasi')))->name('show');
    });
});

Route::prefix('anggota')->name('anggota.')->group(function () {
    Route::view('/dashboard', 'anggota.dashboard')->name('dashboard');

    Route::prefix('buku')->name('buku.')->group(function () {
        Route::view('/', 'anggota.buku.index')->name('index');
        Route::get('/{id_buku}/show', fn($id_buku) => view('anggota.buku.show', compact('id_buku')))->name('show');
    });
    Route::prefix('pengajuanbuku')->name('pengajuanbuku.')->group(function () {
        Route::view('/', 'anggota.pengajuanbuku.index')->name('index');
        Route::view('/create', 'anggota.pengajuanbuku.create')->name('create');
        Route::get('/{id_pengajuan_buku}/show', fn($id_pengajuan_buku) => view('anggota.pengajuanbuku.show', compact('id_pengajuan_buku')))->name('show');
    });
    Route::prefix('peminjamanbuku')->name('peminjamanbuku.')->group(function () {
        Route::view('/', 'anggota.peminjamanbuku.index')->name('index');
        Route::get('/{id_peminjaman_buku}/show', fn($id_peminjaman_buku) => view('anggota.peminjamanbuku.show', compact('id_peminjaman_buku')))->name('show');
    });

    Route::prefix('fasilitas')->name('fasilitas.')->group(function () {
        Route::view('/', 'anggota.fasilitas.index')->name('index');
    });
    Route::prefix('reservasifasilitas')->name('reservasifasilitas.')->group(function () {
        Route::view('/', 'anggota.reservasifasilitas.index')->name('index');
        Route::view('/create', 'anggota.reservasifasilitas.create')->name('create');
        Route::get('/{id_reservasi}/show', fn($id_reservasi) => view('anggota.reservasifasilitas.show', compact('id_reservasi')))->name('show');
    });
});
