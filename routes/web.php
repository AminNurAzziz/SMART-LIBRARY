<?php

use App\Models\Buku;
use App\Models\Regulation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegulationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('portal-peminjaman');
    // return view('home-page');
});

Route::get('/students', [StudentController::class, 'getStudentStatuses']);

Route::get('/getBuku', [BukuController::class, 'getBuku']);

Route::post('/pinjam', [BukuController::class, 'pinjamBuku']);

Route::post('/sendStruk', [BukuController::class, 'sendStruk']);

Route::get('/getRegulation', [RegulationController::class, 'index']);
