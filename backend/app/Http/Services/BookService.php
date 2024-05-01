<?php

namespace App\Http\Services;

use App\Models\Book;

class BookService
{
    public function findBookByCode($bookCode)
    {
        return Book::where('code_book', $bookCode)->firstOrFail();
    }
}
