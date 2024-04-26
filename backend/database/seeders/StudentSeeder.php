<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', '=', 'students')->get();

        foreach ($users as $user) {
            Student::factory()->create([
                // 'user_id' => $user->user_id,
                'nama_mhs' => $user->name
            ]);
        }
    }
}
