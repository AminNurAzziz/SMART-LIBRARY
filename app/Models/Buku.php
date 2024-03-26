<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'kode_buku',
        'isbn',
        'judul_buku',
        'penerbit',
        'kode_kategori',
        'kode_penulis',
        'kode_rak',
        'stok',
        'jumlah_peminjam',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $hidden = [
        //
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
        //
    ];

    // public function kategori()
    // {
    //     return $this->belongsTo(Kategori::class, 'kode_kategori', 'kode_kategori');
    // }
}
