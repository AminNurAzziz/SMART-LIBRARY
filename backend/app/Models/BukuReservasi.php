<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuReservasi extends Model
{
    use HasFactory;

    protected $table = 'buku_reservasi';
    protected $fillable = [
        'kode_reservasi',
        'kode_buku',
        'tanggal_reservasi',
        'tanggal_ambil',
        'status',
    ];

    public function reservasi()
    {
        return $this->belongsTo(ReservasiModel::class, 'kode_reservasi', 'kode_reservasi');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'kode_buku', 'kode_buku');
    }
}
