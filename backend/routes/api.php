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

Route::get('/get-buku', [BukuController::class, 'getBuku']);
Route::post('/pinjam-buku', [PeminjamanBuku::class, 'pinjamBuku']);
Route::get('/get-regulation', [RegulationController::class, 'index']);
Route::post('/update-regulation', [RegulationController::class, 'update']);
Route::get('/get-student', [StudentController::class, 'getStudentStatuses']);
