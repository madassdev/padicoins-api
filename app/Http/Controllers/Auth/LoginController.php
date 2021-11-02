<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // return $request;
        $auth_token = env('APP_AUTH_TOKEN') ?? "PADICOINS_AUTH_TOKEN";
        $request->validate(
            [
                "email" => "required|email",
                "password" => "required"
            ]
        );

        auth()->attempt($request->only(['email', 'password']));
        $user = auth()->user();

        if (!$user) {
            return response()->json(["success" => false, "message" => "Invalid credentials! Unauthenticated"], 401);
        }

        // return $user;

        $token =  $user->createToken($auth_token)->plainTextToken;
        $roles = $user->roles->pluck('name')->toArray();

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => $user,
                'user_roles' => $roles
            ]
        ]);
    }

    public function logout()
    {
        auth()->guard('api')->user()->token()->revoke();
        return response()->json(['success' => true, 'data' => ['message' => 'Successfully logged out']]);
    }
}
