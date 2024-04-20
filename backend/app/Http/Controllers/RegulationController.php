<?php

namespace App\Http\Controllers;

use App\Models\Regulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegulationController extends Controller
{
    /**
     * Display a listing of the regulation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $regulation = Regulation::first();
        Log::info('Regulation accessed', ['regulation' => $regulation]);

        if (!$regulation) {
            return response()->json(['message' => 'No regulation found'], 404);
        }

        return response()->json($regulation);
    }

    /**
     * Update the specified regulation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $regulation = Regulation::first();

        if (!$regulation) {
            return response()->json(['message' => 'No regulation found to update'], 404);
        }

        // Validation logic
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            // Add other fields as necessary
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the regulation
        $regulation->update($validator->validated());

        return response()->json([
            'message' => 'Regulation updated successfully',
            'regulation' => $regulation
        ], 200);
    }
}
