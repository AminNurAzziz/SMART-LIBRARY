<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'buku_peminjaman';
    protected $fillable = [
        'kode_pinjam',
        'kode_buku',
        'tgl_pinjam',
        'tgl_kembali',
        'status',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'kode_pinjam', 'kode_pinjam');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'kode_buku', 'kode_buku');
    }
}
