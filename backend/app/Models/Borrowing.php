<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $table = 'borrowing';
    protected $fillable = [
        'code_borrow',
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

    public function borrowingBook()
    {
        return $this->hasMany(BorrowingBook::class, 'code_borrow', 'code_borrow');
    }

    public function book()
    {
        return $this->belongsToMany(Book::class, 'borrowing_book', 'code_borrow', 'code_book');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id', 'user_id');
    // }
}
