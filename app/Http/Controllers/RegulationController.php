<?php

namespace App\Http\Controllers;

use App\Models\Regulation;
use App\Http\Requests\StoreRegulationRequest;
use App\Http\Requests\UpdateRegulationRequest;
use Illuminate\Support\Facades\Log;

class RegulationController extends Controller
{
    public function index()
    {
        $regulation = Regulation::first();
        Log::info('Regulation index page visited' . $regulation);
        return response()->json($regulation);
    }

    public function edit()
    {
        $regulation = Regulation::first();
        return view('regulation.edit', compact('regulation'));
    }

    public function update(UpdateRegulationRequest $request)
    {
        $regulation = Regulation::first();
        $regulation->update($request->validated());
        return redirect()->route('regulation.index');
    }
}
