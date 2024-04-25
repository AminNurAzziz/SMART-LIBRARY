<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanBuku;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegulationController;
use App\Http\Controllers\HistoryPeminjamanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Rute di bawah ini memerlukan autentikasi
    Route::middleware('auth.jwt')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth.jwt')->group(function () {
    Route::get('/allhistory-peminjaman', [HistoryPeminjamanController::class, 'getAllHistory']);
    Route::delete('/history-peminjaman/{id}', [HistoryPeminjamanController::class, 'deleteHistory']);
});


Route::get('/buku', [BukuController::class, 'getBuku']);
Route::post('/peminjaman-buku', [PeminjamanBuku::class, 'pinjamBuku']);
Route::patch('/pengembalian-buku/{id_detail_pinjam}', [PeminjamanBuku::class, 'kembaliBuku']);
Route::get('/regulation', [RegulationController::class, 'index']);
Route::get('/student', [StudentController::class, 'getStudentStatuses']);
Route::patch('/perpanjangan-buku/{id_detail_pinjamm}', [PeminjamanBuku::class, 'createPerpanjangan']);
Route::post('/reservasi-buku', [PeminjamanBuku::class, 'reserveBook']);
Route::patch('/konfirmasi-reservasi/{id_detail_reservasi}', [PeminjamanBuku::class, 'createKonfirmasiReservasi']);
Route::get('/history-peminjaman', [HistoryPeminjamanController::class, 'getHistoryByNIM']);
