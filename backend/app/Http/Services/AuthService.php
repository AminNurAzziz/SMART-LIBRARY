<?php

namespace App\Services;

use App\Models\User;
use App\Models\Superadmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function login(array $credentials)
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        return $token;
    }

    public function register(array $userData)
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);

        $role = Superadmin::create([
            'name' => $userData['name'],
            'user_id' => $user->id,
        ]);

        return $user;
    }

    public function logout()
    {
        Auth::logout();
    }
}
