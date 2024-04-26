<?php

namespace App\Http\Controllers;

use App\Http\Services\RegulationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegulationController extends Controller
{
    protected $regulationService;

    public function __construct(RegulationService $regulationService)
    {
        $this->regulationService = $regulationService;
    }

    public function index()
    {
        $regulation = $this->regulationService->getRegulation();

        if (!$regulation) {
            return response()->json(['message' => 'No regulation found'], 404);
        }

        return response()->json($regulation);
    }

    public function update(Request $request)
    {
        // Validation logic
        $validator = Validator::make($request->all(), [
            'max_loan_days' => 'integer',
            'max_loan_books' => 'integer',
            'max_reserve_books' => 'integer',
            'max_reserve_days' => 'integer',
            'fine_per_day' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the regulation
        $regulation = $this->regulationService->updateRegulation($validator->validated());

        if (!$regulation) {
            return response()->json(['message' => 'No regulation found to update'], 404);
        }

        return response()->json([
            'message' => 'Regulation updated successfully',
        ], 200);
    }
}
