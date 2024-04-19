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
    ];
}
