<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExtendBookController;
use App\Http\Controllers\RegulationController;
use App\Http\Controllers\ReturnBookController;
use App\Http\Controllers\BorrowingBookController;
use App\Http\Controllers\BorrowingHistoryController;
use App\Http\Controllers\ReserveBookController;

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
    Route::get('/allhistory-peminjaman', [BorrowingHistoryController::class, 'getAllHistory']);
    Route::delete('/history-peminjaman/{id}', [BorrowingHistoryController::class, 'deleteHistory']);
});


Route::get('/buku', [BookController::class, 'findBook']);

Route::post('/peminjaman-buku', [BorrowingBookController::class, 'pinjamBuku']);

Route::get('/pengembalian-buku/{id_detail_pinjam}', [ReturnBookController::class, 'getPengembalian']);
Route::patch('/pengembalian-buku/{id_detail_pinjam}', [ReturnBookController::class, 'kembaliBuku']);
Route::get('/regulation', [RegulationController::class, 'index']);
Route::patch('/regulation', [RegulationController::class, 'update']);
Route::get('/student', [StudentController::class, 'getStudentStatuses']);
Route::patch('/perpanjangan-buku/{id_detail_pinjamm}', [ExtendBookController::class, 'createPerpanjangan']);

Route::get('/reservasi-buku/{id_detail_reservasi}', [ReserveBookController::class, 'getReservasi']);
Route::post('/reservasi-buku', [ReserveBookController::class, 'reserveBook']);
Route::patch('/konfirmasi-reservasi/{id_detail_reservasi}', [ReserveBookController::class, 'createKonfirmasiReservasi']);

Route::get('/history-peminjaman', [BorrowingHistoryController::class, 'getHistoryByNIM']);
