<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $request->validated();
    
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
    
        $user = User::create($userData);
        $token = $user->createToken('ProInventario');
    
        // Extraer solo el token de texto plano
        $accessToken = $token->plainTextToken;
    
        return response([
            'user' => $user,
            'token' => $accessToken
        ], 201);
    }
    
    public function login(LoginRequest $request)
    {
        $request->validated();
    
        // Buscar al usuario por nombre
        $user = User::where('name', $request->name)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['message' => 'Invalid credentials'], 401);
        }
    
        $token = $user->createToken('ProInventario');
    
        // Extraer solo el token de texto plano
        $accessToken = $token->plainTextToken;
    
        return response([
            'user' => $user,
            'token' => $accessToken
        ], 200);
    }
    
    
}
