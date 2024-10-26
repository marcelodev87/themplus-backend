<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['message' => 'Credenciais inválidas'], 401);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        return response(['user' => $user, 'token' => $token], 200);
    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('my-app-token')->plainTextToken;

        return response(['user' => $user, 'token' => $token], 201);
    }
}
