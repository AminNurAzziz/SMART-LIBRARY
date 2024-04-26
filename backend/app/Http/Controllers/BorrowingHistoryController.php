<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Services\BorrowingHistoryService;

class BorrowingHistoryController extends Controller
{
    public function __construct(protected BorrowingHistoryService $BorrowingHistoryService)
    {
    }

    public function getHistoryByNIM(Request $request)
    {
        $nim = $request->query('nim');

        $result = $this->BorrowingHistoryService->getHistoryByNIM($nim);

        if ($result->count() > 0) {
            return response()->json([
                'success' => true,
                'message' => 'History peminjaman retrieved successfully',
                'data' => $result,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'History peminjaman not found',
            'data' => null,
        ], 404);
    }

    public function getAllHistory(Request $request)
    {
        Log::info('Getting all history');
        try {
            return $this->BorrowingHistoryService->getAllHistory($request);
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
        try {
            return $this->BorrowingHistoryService->deleteHistory($peminjaman);
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
