<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservasiModel extends Model
{
    use HasFactory;

    protected $table = 'reservasi';
    protected $fillable = [
        'kode_reservasi',
        'nim',
        'tanggal_reservasi',
        'tanggal_ambil',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }

    public function bukuReservasi()
    {
        return $this->hasMany(BukuReservasi::class, 'kode_reservasi', 'kode_reservasi');
    }

    public function buku()
    {
        return $this->belongsToMany(Buku::class, 'buku_reservasi', 'kode_reservasi', 'kode_buku');
    }
}
