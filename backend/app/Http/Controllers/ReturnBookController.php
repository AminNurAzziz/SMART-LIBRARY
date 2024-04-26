<?php

namespace App\Http\Controllers;


use App\Http\Services\ReturnBookService;
use Illuminate\Http\Request;

class ReturnBookController extends Controller
{
    protected $ReturnBookService;

    public function __construct(ReturnBookService $ReturnBookService)
    {
        $this->ReturnBookService = $ReturnBookService;
    }


    public function kembaliBuku($id_detail_pinjam)
    {
        $peminjaman = $this->ReturnBookService->createPengembalian($id_detail_pinjam);

        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }

        return response()->json([
            'message' => 'Peminjaman berhasil dikembalikan',
        ]);
    }

    public function getPengembalian($id_detail_pinjam)
    {
        $pengembalian = $this->ReturnBookService->getPengembalian($id_detail_pinjam);


        return response()->json([
            'message' => 'Pengembalian found',
            'data_pengembalian' => $pengembalian,
        ]);
    }
}
