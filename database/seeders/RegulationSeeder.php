<?php

namespace Database\Seeders;

use App\Models\Regulation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RegulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tambahkan logika untuk membuat entri data baru
        Regulation::create([
            'max_loan_days' => 7,
            'max_loan_books' => 2,
            'max_reserve_books' => 2,
            'max_reserve_days' => 1,
            'fine_per_day' => 500.00,
        ]);
    }
}
