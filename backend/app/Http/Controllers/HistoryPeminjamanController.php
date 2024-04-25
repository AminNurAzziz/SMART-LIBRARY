<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Services\HistoryPeminjamanService;
use App\Http\Services\PaginationValidationService;
use App\Models\Peminjaman;

class HistoryPeminjamanController extends Controller
{
    public function __construct(protected HistoryPeminjamanService $historyPeminjamanService)
    {
    }

    public function getHistoryByNIM(Request $request)
    {
        $nim = $request->query('nim');
        Log::info("Mencari riwayat peminjaman berdasarkan NIM: $nim");

        $result = $this->historyPeminjamanService->getHistoryByNIM($nim);
        Log::info("Data riwayat peminjaman ditemukan: " . ($result->count() > 0 ? "ya" : "tidak"));

        if ($result->count() > 0) {
            Log::info('Mengembalikan data riwayat peminjaman');
            return response()->json([
                'success' => true,
                'message' => 'Riwayat peminjaman berhasil ditemukan',
                'data' => $result,
            ], 200);
        }

        Log::info('Data riwayat peminjaman tidak ditemukan');
        return response()->json([
            'success' => false,
            'message' => 'Riwayat peminjaman tidak ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAllHistory(Request $request)
    {
        try {
            return $this->historyPeminjamanService->getAllHistory($request);
        } catch (\Exception $e) {
            Log::error('Failed to get all history', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function deleteHistory($peminjaman)
    {
        // dd($peminjaman);
        try {
            return $this->historyPeminjamanService->deleteHistory($peminjaman);
        } catch (\Exception $e) {
            Log::error('Failed to delete history', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
}

// $peminjaman  = Peminjaman::where('id', $peminjaman->id)->first();
