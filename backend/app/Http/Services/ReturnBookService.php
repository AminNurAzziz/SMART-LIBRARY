<?php

namespace App\Http\Services;

use App\Models\Regulation;
use Illuminate\Support\Facades\Log;
use App\Models\BukuPeminjaman;
use App\Models\Peminjaman;
use App\Models\Student;

class ReturnBookService
{
    public function getPengembalian(string $id_detail_pinjam)
    {
        $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->firstOrFail();
        $peminjaman = $detail_peminjaman;
        $denda = 0;
        $total_keterlambatan = $peminjaman->tgl_kembali < now() ? now()->diffInDays($peminjaman->tgl_kembali) : 0;
        $denda_perhari = Regulation::value('fine_per_day');

        // Pastikan nilai fine_per_day valid sebelum menggunakannya
        if ($denda_perhari !== null) {
            $denda = $total_keterlambatan * $denda_perhari;
        }
        // Ambil semua buku yang dipinjam melalui relasi many-to-many
        $buku_dipinjam = $detail_peminjaman->buku;
        $student = Student::where('nim', $peminjaman->peminjaman->nim)->firstOrFail();


        $response = [
            'message' => 'Return found',
            'borrow_data' => [
                'borrow_data' => [
                    'borrow_code' => $peminjaman->id_detail_pinjam,
                    'borrow_date' => $peminjaman->tgl_pinjam,
                    'return_date' => $peminjaman->tgl_kembali,
                    'status' => $peminjaman->status,
                    'fine' => number_format($denda, 2),
                    'late_days' => $total_keterlambatan,
                    'created_at' => $peminjaman->created_at,
                    'updated_at' => $peminjaman->updated_at,
                ],
                'books' => [
                    'book_code' => $buku_dipinjam->kode_buku,
                    'isbn' => $buku_dipinjam->isbn,
                    'book_title' => $buku_dipinjam->judul_buku,
                    'stock' => $buku_dipinjam->stok,
                    'qty_borrowed' => $buku_dipinjam->jumlah_peminjam,

                ],
                'borrower' => [
                    'student_id' => $student->nim,
                    'name' => $student->nama_mhs,
                ],
                'lateness_info' => [
                    'total_days' => $total_keterlambatan,
                    'fine' => number_format($denda, 2),
                ],
            ],
        ];

        return $response;
    }

    public function createPengembalian(string $id_detail_pinjam)
    {
        // Ambil detail peminjaman berdasarkan id_detail_pinjam
        $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->firstOrFail();

        // Ubah status peminjaman menjadi 'dikembalikan'
        $peminjaman = $detail_peminjaman;
        $peminjaman->status = 'dikembalikan';
        $denda = 0;
        $total_keterlambatan = $peminjaman->tgl_kembali < now() ? now()->diffInDays($peminjaman->tgl_kembali) : 0;
        $denda_perhari = Regulation::value('fine_per_day');

        // Pastikan nilai fine_per_day valid sebelum menggunakannya
        if ($denda_perhari !== null) {
            $denda = $total_keterlambatan * $denda_perhari;
        }
        $peminjaman->denda = $denda;
        $peminjaman->terlambat = $total_keterlambatan;

        // Ambil semua buku yang dipinjam melalui relasi many-to-many
        $buku_dipinjam = $detail_peminjaman->buku;

        // Tingkatkan stok untuk setiap buku yang dipinjam
        $buku_dipinjam->each(function ($buku) {
            $buku->update(['stok' => $buku->stok + 1]);
        });


        $peminjaman->save();

        Log::info('Return of book processed successfully');

        return $peminjaman;
    }
}
