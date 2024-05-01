<?php

namespace App\Http\Services;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingBook;
use Illuminate\Support\Facades\Log;


class AdminService
{
    public function totalDashboard()
    {
        Log::info('Starting totalDashboard function');
        $totalStudentPinjam = Borrowing::distinct('nim')->count();
        Log::info('Total students in Peminjaman table is ' . $totalStudentPinjam);

        $totalBukuKembali = BorrowingBook::where('status', 'dikembalikan')->count();
        Log::info('Total books returned in BorrowingBook table is ' . $totalBukuKembali);

        $totalBukuDipinjam = BorrowingBook::where('status', 'dipinjam')->count();
        Log::info('Total books borrowed in BukuPeminjaman table is ' . $totalBukuDipinjam);

        $totalBukuTersedia = Book::where('stok', '>', 0)->count();
        Log::info('Total available books in Buku table is ' . $totalBukuTersedia);

        $totalDenda = BorrowingBook::where('denda', '>', 0)->get()->sum('denda');
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
