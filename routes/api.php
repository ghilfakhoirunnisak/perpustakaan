<?php

use App\Http\Controllers\ApprovalBukuController;
use App\Http\Controllers\ApprovalFasilitasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DetailPengajuanBukuController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PenerbitBukuController;
use App\Http\Controllers\PengajuanBukuController;
use App\Http\Controllers\PenulisBukuController;
use App\Http\Controllers\ReservasiFasilitasController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifikatorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/otp/verifikasi', [MailerController::class, 'verifikasi']);
Route::post('/otp/resend', [MailerController::class, 'resendOtp']);

Route::prefix('forgot-password')->group(function () {
    Route::post('/send', [ForgotPasswordController::class, 'send']);
    Route::post('/verifikasi', [ForgotPasswordController::class, 'verifikasi']);
    Route::post('/reset', [ForgotPasswordController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum', 'check.token.expiry'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'getProfile']);

    Route::prefix('role')->middleware('role:admin')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id_role}', [RoleController::class, 'show']);
        Route::put('/{id_role}', [RoleController::class, 'update']);
        Route::delete('/{id_role}', [RoleController::class, 'destroy']);
    });
    Route::prefix('user')->middleware('role:admin')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id_user}', [UserController::class, 'show']);
        Route::put('/{id_user}', [UserController::class, 'update']);
        Route::delete('/{id_user}', [UserController::class, 'destroy']);
    });
    Route::prefix('verifikator')->middleware('role:admin')->group(function () {
        Route::get('/', [VerifikatorController::class, 'index']);
        Route::post('/', [VerifikatorController::class, 'store']);
        Route::get('/{id_verifikator}', [VerifikatorController::class, 'show']);
        Route::put('/{id_verifikator}', [VerifikatorController::class, 'update']);
        Route::delete('/{id_verifikator}', [VerifikatorController::class, 'destroy']);
    });
    Route::prefix('penulisbuku')->middleware('role:admin')->group(function () {
        Route::get('/', [PenulisBukuController::class, 'index']);
        Route::post('/', [PenulisBukuController::class, 'store']);
        Route::get('/{id_penulis_buku}', [PenulisBukuController::class, 'show']);
        Route::put('/{id_penulis_buku}', [PenulisBukuController::class, 'update']);
        Route::delete('/{id_penulis_buku}', [PenulisBukuController::class, 'destroy']);
    });
    Route::prefix('penerbitbuku')->middleware('role:admin')->group(function () {
        Route::get('/', [PenerbitBukuController::class, 'index']);
        Route::post('/', [PenerbitBukuController::class, 'store']);
        Route::get('/{id_penerbit_buku}', [PenerbitBukuController::class, 'show']);
        Route::put('/{id_penerbit_buku}', [PenerbitBukuController::class, 'update']);
        Route::delete('/{id_penerbit_buku}', [PenerbitBukuController::class, 'destroy']);
    });
    Route::prefix('buku')->group(function () {
        Route::get('/', [BukuController::class, 'index']);
        Route::get('/{id_buku}', [BukuController::class, 'show']);

        Route::middleware('role:admin')->group(function () {
            Route::post('/', [BukuController::class, 'store']);
            Route::put('/{id_buku}', [BukuController::class, 'update']);
            Route::delete('/{id_buku}', [BukuController::class, 'destroy']);
        });
    });
    Route::prefix('pengajuanbuku')->group(function () {
        Route::get('/', [PengajuanBukuController::class, 'index']);
        Route::get('/{id_pengajuan_buku}', [PengajuanBukuController::class, 'show']);

        Route::middleware('role:anggota')->group(function () {
            Route::post('/anggota', [PengajuanBukuController::class, 'storeByAnggota']);
            Route::delete('/{id_pengajuan_buku}', [PengajuanBukuController::class, 'destroy']);
        });

        Route::middleware('role:admin')->group(function () {
            Route::post('/', [PengajuanBukuController::class, 'storeByAdmin']);
            Route::put('/{id_pengajuan_buku}', [PengajuanBukuController::class, 'update']);
        });
    });
    Route::prefix('detailpengajuanbuku')->group(function () {
        Route::get('/', [DetailPengajuanBukuController::class, 'index']);
        Route::post('/', [DetailPengajuanBukuController::class, 'store']);
        Route::get('/{id_detail_pengajuan_buku}', [DetailPengajuanBukuController::class, 'show']);
        Route::put('/{id_detail_pengajuan_buku}', [DetailPengajuanBukuController::class, 'update']);
        Route::put('/{id_detail_pengajuan_buku}', [DetailPengajuanBukuController::class, 'destroy']);
    });
    Route::prefix('peminjamanbuku')->group(function () {
        Route::get('/', [PeminjamanController::class, 'index']);
        Route::get('/{id_peminjaman_buku}', [PeminjamanController::class, 'show']);

    });


    Route::prefix('fasilitas')->group(function () {
        Route::get('/', [FasilitasController::class, 'index']);
        Route::get('/{id_fasilitas}', [FasilitasController::class, 'show']);

        Route::middleware('role:admin')->group(function () {
            Route::post('/', [FasilitasController::class, 'store']);
            Route::put('/{id_fasilitas}', [FasilitasController::class, 'update']);
            Route::delete('/{id_fasilitas}', [FasilitasController::class, 'destroy']);
        });
    });
    Route::prefix('reservasifasilitas')->group(function () {
        Route::get('/', [ReservasiFasilitasController::class, 'index']);
        Route::get('/{id_reservasi}', [ReservasiFasilitasController::class, 'show']);
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [ReservasiFasilitasController::class, 'storeByAdmin']);
            Route::put('/{id_reservasi}', [ReservasiFasilitasController::class, 'updateByAdmin']);
        });
        Route::middleware('role:anggota')->group(function () {
            Route::post('/anggota', [ReservasiFasilitasController::class, 'storeByAnggota']);
        });
        Route::middleware('role:admin,anggota')->group(function () {
            Route::delete('/{id_reservasi}', [ReservasiFasilitasController::class, 'destroy']);
        });
    });
    Route::prefix('dokumen')->group(function () {
        Route::get('/', [DokumenController::class, 'index']);
        Route::get('/{id_dokumen}', [DokumenController::class, 'show']);
        Route::put('/{id_dokumen}', [DokumenController::class, 'update']);
        Route::put('/update-by-reservasi/{id_reservasi}', [DokumenController::class, 'updateByReservasi']);

        Route::middleware('role:admin,anggota')->group(function () {
            Route::post('/', [DokumenController::class, 'store']);
            Route::delete('/{id_dokumen}', [DokumenController::class, 'destroy']);
        });
    });

    Route::prefix('approval')->middleware('role:verifikator')->group(function () {
        Route::get('/', [ApprovalFasilitasController::class, 'index']);
        Route::post('/{id_reservasi}/approve', [ApprovalFasilitasController::class, 'approve']);
    });
    Route::prefix('approvalbuku')->middleware('role:verifikator')->group(function () {
        Route::get('/', [ApprovalBukuController::class, 'index']);
        Route::post('/{id_pengajuan_buku}/approve', [ApprovalBukuController::class, 'approve']);
    });

    Route::prefix('logs')->middleware('role:admin')->group(function () {
        Route::get('/activity', [LogController::class, 'activity']);
        Route::get('/error', [LogController::class, 'error']);
        Route::get('/database', [LogController::class, 'database']);
        Route::get('/approval', [LogController::class, 'approval']);
        Route::get('/approvalbuku', [LogController::class, 'approvalbuku']);
    });
});
