<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'book';

    protected $fillable = [
        'code_buku',
        'isbn',
        'title_book',
        'publisher',
        'code_category',
        'code_author',
        'code_rack',
        'stok',
        'loan_amount',
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
