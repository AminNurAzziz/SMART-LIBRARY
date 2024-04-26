<?php

namespace App\Http\Services;

use App\Models\Regulation;
use Illuminate\Support\Facades\Log;

class RegulationService
{
    public function getRegulation()
    {
        $regulation = Regulation::first();
        Log::info('Regulation accessed', ['regulation' => $regulation]);

        return $regulation;
    }

    public function updateRegulation(array $data)
    {
        $regulation = Regulation::first();

        if (!$regulation) {
            return null; // or throw an exception as per your application's logic
        }

        $regulation->update($data);

        return $regulation;
    }
}
