<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $fillable = [
        'kode_pinjam',
        'nim',
        // 'user_id',
        // 'tgl_pinjam',
        // 'tgl_kembali',
        // 'status',
    ];


    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }

    public function bukuPeminjaman()
    {
        return $this->hasMany(BukuPeminjaman::class, 'kode_pinjam', 'kode_pinjam');
    }

    public function buku()
    {
        return $this->belongsToMany(Buku::class, 'buku_peminjaman', 'kode_pinjam', 'kode_buku');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
