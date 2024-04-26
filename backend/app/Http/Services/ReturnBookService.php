<?php

namespace App\Http\Services;

use App\Models\Regulation;
use Illuminate\Support\Facades\Log;
use App\Models\BukuPeminjaman;
use App\Models\Student;

class ReturnBookService
{
    public function getPengembalian(string $id_detail_pinjam)
    {
        $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->firstOrFail();
        $peminjaman = $detail_peminjaman->peminjaman;
        $denda = 0;
        $total_keterlambatan = $peminjaman->tgl_kembali < now() ? now()->diffInDays($peminjaman->tgl_kembali) : 0;
        $denda_perhari = Regulation::value('fine_per_day');

        // Pastikan nilai fine_per_day valid sebelum menggunakannya
        if ($denda_perhari !== null) {
            $denda = $total_keterlambatan * $denda_perhari;
        }
        // Ambil semua buku yang dipinjam melalui relasi many-to-many
        $buku_dipinjam = $detail_peminjaman->buku;
        $student = Student::where('nim', $peminjaman->nim)->first();

        $response = [
            'data_peminjaman' => $peminjaman,
            'buku_dipinjam' => $buku_dipinjam->judul_buku,
            'peminjam' => [
                'nim' => $student->nim,
                'nama' => $student->nama_mhs,
            ],
            'keterlambatan' => [
                'total_hari' => $total_keterlambatan,
                'denda' => $denda,
            ]
        ];

        return $response;
    }
    public function createPengembalian(string $id_detail_pinjam)
    {
        // Ambil detail peminjaman berdasarkan id_detail_pinjam
        $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->firstOrFail();
        Log::info('Detail pinjam ditemukan: ' . $detail_peminjaman);

        // Ubah status peminjaman menjadi 'dikembalikan'
        $peminjaman = $detail_peminjaman->peminjaman;
        Log::info('Peminjaman found: ' . $peminjaman);
        $peminjaman->status = 'dikembalikan';
        $denda = 0;
        Log::info('Tanggal kembali: ' . $peminjaman->tgl_kembali);
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
        Log::info('Books borrowed: ' . $buku_dipinjam);

        // Tingkatkan stok untuk setiap buku yang dipinjam
        $buku_dipinjam->each(function ($buku) {
            Log::info('Increasing stock of book: ' . $buku->kode_buku);
            $buku->update(['stok' => $buku->stok + 1]);
        });

        // Simpan perubahan pada status peminjaman
        Log::info('Saving changes to peminjaman');
        $peminjaman->save();

        Log::info('Return of book processed successfully');

        return $peminjaman;
    }
}
