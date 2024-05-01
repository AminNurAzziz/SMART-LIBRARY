<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationModel extends Model
{
    use HasFactory;

    protected $table = 'reservation';
    protected $fillable = [
        'code_reservation',
        'nim',
        // 'tanggal_reservasi',
        // 'tanggal_ambil',
        // 'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }

    public function bukuReservasi()
    {
        return $this->hasMany(BookReservation::class, 'code_reservation', 'code_reservation');
    }

    public function buku()
    {
        return $this->belongsToMany(Book::class, 'book_reservation', 'code_reservation', 'code_book');
    }
}
