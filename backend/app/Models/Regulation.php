<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    use HasFactory;

    protected $table = 'regulation';

    protected $fillable = [
        'max_loan_days',
        'max_loan_books',
        'max_reserve_books',
        'max_reserve_days',
        'fine_per_day',
    ];
}
