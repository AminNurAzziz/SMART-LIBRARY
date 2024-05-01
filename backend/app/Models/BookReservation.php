<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookReservation extends Model
{
    use HasFactory;

    protected $table = 'book_reservation';
    protected $fillable = [
        'code_reservation',
        'code_book',
        'reservation_date',
        'taken_date',
        'status',
    ];

    public function reservasi()
    {
        return $this->belongsTo(ReservationModel::class, 'code_reservation', 'code_reservation');
    }

    public function buku()
    {
        return $this->belongsTo(Book::class, 'code_book', 'code_book');
    }
}
