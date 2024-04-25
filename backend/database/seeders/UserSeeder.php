<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Buat Superadmin
        DB::table('user')->insert([
            'user_id' => \Ramsey\Uuid\Uuid::uuid4(),
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superAdmin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat Admin
        DB::table('user')->insert([
            'user_id' => \Ramsey\Uuid\Uuid::uuid4(),
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user')->insert([
            'user_id' => \Ramsey\Uuid\Uuid::uuid4(),
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'students',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
