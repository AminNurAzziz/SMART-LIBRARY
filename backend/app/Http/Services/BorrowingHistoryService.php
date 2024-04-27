<?php

namespace App\Http\Services;

use App\Models\BukuPeminjaman;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BorrowingHistoryService
{
    public function getHistoryByNIM($nim)
    {
        $user = Auth::user();

        $historyQuery = Peminjaman::where('nim', $nim)
            ->where('status', 'dikembalikan');

        if (!is_null($user) && $user->role === 'students') {
            Log::info('User role is students, filtering by user id', ['user_id' => $user->id]);
            $historyQuery->where('user_id', $user->id);
        }

        $history = $historyQuery->get();

        if ($history->isEmpty()) {
            return collect();
        }

        $result = $history->map(function ($h) {
            return [
                'nim' => $h->nim,
                'borrowing_code' => $h->kode_pinjam,
                'borrowing_date' => $h->tgl_pinjam,
                'return_date' => $h->tgl_kembali,
                'status' => $h->status,
            ];
        });

        return $result;
    }

    public function getAllHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $pageSize = min($request->input('page_size', 1), 50);
            $currentPage = $request->input('page', 1);

            if (!is_numeric($currentPage) || $currentPage < 1) {
                return response()->json([
                    'statusCode' => 400,
                    'status' => false,
                    'message' => 'Invalid page size number. Page size number must be a > 1 positive integer.'
                ], 400);
            }

            if ($pageSize == -1 || !is_numeric($pageSize) || $pageSize < 1) {
                return response()->json([
                    'statusCode' => 400,
                    'status' => false,
                    'message' => 'Invalid page size number. Page size number must be a > 1 positive integer.'
                ], 400);
            }

            $allHistoryQuery = BukuPeminjaman::with('peminjaman.student')->whereHas('peminjaman', function ($query) {
                $query->where('status', 'dikembalikan');
            });

            // Filter berdasarkan role admin
            if (!is_null($user) && $user->role === 'admin') {
                Log::info('User role is admin, fetching all history');
            } else {
                // Jika bukan admin, filter berdasarkan user_id pengguna yang login
                $allHistoryQuery->where('user_id', $user->id);
            }
            $allHistory = $allHistoryQuery->paginate($pageSize, ['*'], 'page', $currentPage);

            $response = collect($allHistory->items())->map(function ($ah) {
                $nim = $ah->peminjaman->student ? $ah->peminjaman->student->nim : null;

                return [
                    'nim' => $nim,
                    'borrowed_code' => $ah->id_detail_pinjam,
                    'borrowing_date' => $ah->tgl_pinjam,
                    'return_date' => $ah->tgl_kembali,
                    'status' => $ah->status,
                ];
            });

            $paginationData = [
                'rows_total' => $allHistory->total(),
                'page_total' => $allHistory->lastPage(),
                'current_page' => $allHistory->currentPage(),
                'page_size' => $allHistory->perPage(),
            ];

            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $response,
                'message' => 'Success Fetching History',
                'pagination' => $paginationData
            ]);
        } catch (\Exception $e) {
            Log::error('History Fetch Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function deleteHistory($peminjaman)
    {
        // $user = Auth::user();

        // if ($user->role !== 'admin') {
        //     return response()->json([
        //         'statusCode' => 403,
        //         'status' => false,
        //         'message' => 'You are not authorized to perform this action.'
        //     ], 403);
        // }

        try {
            // $history = BukuPeminjaman::where('status', 'dikembalikan')->find($peminjaman);
            $history = BukuPeminjaman::where('id_detail_pinjam', $peminjaman)
                ->whereHas('peminjaman', function ($query) {
                    $query->where('status', 'dikembalikan');
                })
                ->first();

            Log::info('History found', ['history' => $history]);

            if (!$history) {
                return response()->json([
                    'statusCode' => 400,
                    'status' => false,
                    'message' => 'History not found.'
                ], 400);
            }

            // $bukuPeminjaman = $history->bukuPeminjaman;

            // Delete related records from buku_peminjaman table
            // foreach ($bukuPeminjaman as $buku) {
            //     $buku->delete();
            // }

            // Now delete the history
            $history->delete();

            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'message' => 'History deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete History Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
}
