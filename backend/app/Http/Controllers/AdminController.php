<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AdminService;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function totalDashboard()
    {
        try {
            $dashboardData = $this->adminService->totalDashboard();

            return response()->json([
                'status' => 'success',
                'message' => 'Dashboard data retrieved successfully',
                'data' => $dashboardData
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in totalDashboard function: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve dashboard data'
            ], 500);
        }
    }
}
