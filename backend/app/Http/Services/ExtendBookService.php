<?php

namespace App\Http\Services;

use App\Models\BukuPeminjaman;
use App\Models\Regulation;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ExtendBookService
{

    public function createPerpanjangan(string $kodePeminjaman)
    {
        $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $kodePeminjaman)->firstOrFail();
        $peminjaman = $detail_peminjaman->peminjaman;
        $durasi_peminjaman = Regulation::value('max_loan_days');
        $tgl_kembali = Carbon::parse($peminjaman->tgl_kembali)->addDays($durasi_peminjaman);
        $peminjaman->tgl_kembali = $tgl_kembali;
        $peminjaman->save();

        $buku = $detail_peminjaman->buku;
        $id_sebelumnya = $detail_peminjaman->id_detail_pinjam;
        $detail_peminjaman->id_detail_pinjam = 'KD-P' . $buku->kode_buku . Str::random(3);
        // $detail_peminjaman->save();
        return [$peminjaman, $detail_peminjaman, $id_sebelumnya, $buku->judul_buku];
    }
}
