<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanBuku;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegulationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/buku', [BukuController::class, 'getBuku']);
Route::post('/peminjaman-buku', [PeminjamanBuku::class, 'pinjamBuku']);
Route::patch('/pengembalian-buku/{id_detail_pinjam}', [PeminjamanBuku::class, 'kembaliBuku']);
Route::get('/regulation', [RegulationController::class, 'index']);
Route::get('/student', [StudentController::class, 'getStudentStatuses']);
