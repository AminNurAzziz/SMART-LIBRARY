<?php

namespace App\Http\Services;

use App\Models\Buku;

class BookService
{
    public function findBookByCode($bookCode)
    {
        return Buku::where('kode_buku', $bookCode)->firstOrFail();
    }
}
