<?php

namespace App\Http\Services;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\BukuPeminjaman;
use Illuminate\Support\Facades\Log;


class AdminService
{
    public function totalDashboard()
    {
        Log::info('Starting totalDashboard function');
        $totalStudentPinjam = Peminjaman::distinct('nim')->count();
        Log::info('Total students in Peminjaman table is ' . $totalStudentPinjam);

        $totalBukuKembali = BukuPeminjaman::where('status', 'dikembalikan')->count();
        Log::info('Total books returned in BukuPeminjaman table is ' . $totalBukuKembali);

        $totalBukuDipinjam = BukuPeminjaman::where('status', 'dipinjam')->count();
        Log::info('Total books borrowed in BukuPeminjaman table is ' . $totalBukuDipinjam);

        $totalBukuTersedia = Buku::where('stok', '>', 0)->count();
        Log::info('Total available books in Buku table is ' . $totalBukuTersedia);

        $totalDenda = BukuPeminjaman::where('denda', '>', 0)->get()->sum('denda');
        $totalDendaString = 'Rp.' . number_format($totalDenda, 2);
        Log::info('Total fines in BukuPeminjaman table is ' . $totalDendaString);

        Log::info('Ending totalDashboard function');

        return [
            'total_student_borrow' => $totalStudentPinjam,
            'total_book_back' => $totalBukuKembali,
            'total_borrowing' => $totalBukuDipinjam,
            'total_book_available' => $totalBukuTersedia,
            'total_fine' => $totalDendaString
        ];
    }
}
