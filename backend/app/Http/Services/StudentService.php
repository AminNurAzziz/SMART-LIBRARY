<?php

namespace App\Http\Services;

use App\Models\Student;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Log;

class StudentService
{
    public function getStudentWithBorrowingData($nim)
    {
        if (!$nim) {
            return null;
        }

        $student = Student::where('nim', $nim)->first();
        if (!$student) {
            Log::warning("Student not found for NIM: {$nim}");
            return null;
        }

        $data_peminjaman = Peminjaman::select('peminjaman.id', 'peminjaman.tgl_pinjam', 'peminjaman.tgl_kembali', 'bukus.judul_buku', 'bukus.kode_buku', 'peminjaman.status')
            ->join('buku_peminjaman', 'peminjaman.kode_pinjam', '=', 'buku_peminjaman.kode_pinjam')
            ->join('bukus', 'buku_peminjaman.kode_buku', '=', 'bukus.kode_buku')
            ->where('peminjaman.nim', $nim)
            ->where('peminjaman.status', 'Dipinjam')
            ->limit(2)
            ->get();

        return [
            'student' => $student,
            'borrowing_data' => $data_peminjaman
        ];
    }
}
