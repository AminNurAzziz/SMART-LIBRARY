<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowingBook extends Model
{
    use HasFactory;

    protected $table = 'borrowing_book';
    protected $fillable = [
        'code_borrow',
        'code_book',
        'loan_date',
        'return_date',
        'status',
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class, 'code_borrow', 'code_borrow');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'code_book', 'code_book');
    }
}
