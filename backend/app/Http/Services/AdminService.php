<?php

namespace App\Http\Services;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\BukuPeminjaman;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class AdminService
{
    public function totalDashboard()
    {
        Log::info('Starting totalDashboard function');

        // Total student borrow
        $totalStudentPinjam = Peminjaman::distinct('nim')->count();

        // Total book back
        $totalBukuKembali = BukuPeminjaman::where('status', 'dikembalikan')->count();

        // Total borrowing
        $totalBukuDipinjam = BukuPeminjaman::where('status', 'dipinjam')->count();

        // Total book available
        $totalBukuTersedia = Buku::where('stok', '>', 0)->count();

        // Total fine
        $totalDenda = BukuPeminjaman::where('denda', '>', 0)->sum('denda');
        $totalDendaString = 'Rp.' . number_format($totalDenda, 2);

        // Monthly borrowing trend
        $monthlyBorrowing = BukuPeminjaman::select(DB::raw('MONTH(tgl_pinjam) as month, COUNT(*) as total'))
            ->whereYear('tgl_pinjam', '=', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

        // Initialize monthly borrowing trend data for all months of the year
        $monthlyBorrowingTrend = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyBorrowingTrend[] = [
                'month' => date('M', mktime(0, 0, 0, $i, 1)),
                'borrow_count' => 0 // Default value is 0
            ];
        }

        // Fill in the actual monthly borrowing data
        foreach ($monthlyBorrowing as $data) {
            $monthIndex = $data['month'] - 1; // Months in PHP are 1-indexed, but array keys are 0-indexed
            $monthlyBorrowingTrend[$monthIndex]['borrow_count'] = $data['total'];
        }

        $fourBooksMostBorrowed = BukuPeminjaman::select('kode_buku', DB::raw('COUNT(*) as total'))
            ->groupBy('kode_buku')
            ->orderBy('total', 'desc')
            ->limit(4)
            ->get()
            ->toArray();

        $fourBooksMostBorrowedWithTitles = [];
        foreach ($fourBooksMostBorrowed as $data) {
            $book = Buku::where('kode_buku', $data['kode_buku'])->first();
            $fourBooksMostBorrowedWithTitles[] = [
                'book_code' => $data['kode_buku'],
                'book_title' => $book->judul_buku,
                'borrow_count' => $data['total']
            ];
        }

        return [
            'total_student_borrow' => $totalStudentPinjam,
            'total_book_back' => $totalBukuKembali,
            'total_borrowing' => $totalBukuDipinjam,
            'total_book_available' => $totalBukuTersedia,
            'total_fine' => $totalDendaString,
            'monthly_borrowing_trend' => $monthlyBorrowingTrend,
            'four_books_most_borrowed' => $fourBooksMostBorrowedWithTitles

        ];
    }
}
