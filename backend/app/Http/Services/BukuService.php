<?php

namespace App\Http\Services;

use App\Models\Buku;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BukuService
{
    /**
     * Retrieve a book by its code.
     *
     * @param string $kodeBuku
     * @return Buku
     * @throws ModelNotFoundException
     */
    public function getBukuByKode($kodeBuku)
    {
        return Buku::where('kode_buku', $kodeBuku)->firstOrFail();
    }
}
