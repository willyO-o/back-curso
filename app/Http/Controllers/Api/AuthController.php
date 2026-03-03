<?php

namespace App\Http\Controllers\Api;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $refreshToken = JWTAuth::fromUser(auth()->user(), [
            'type' => 'refresh'
        ]);

        return response()
            ->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ])
            ->cookie(
                'refresh_token',
                $refreshToken,
                60 * 24 * 7,
                '/',
                null,
                true,
                true,
                false,
                'Strict'
            );
    }

    public function refresh(Request $request)
    {

        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return response()->json(['error' => 'No refresh token'], 401);
        }

        try {

            $payload = JWTAuth::setToken($refreshToken)->getPayload();

            if ($payload['type'] !== 'refresh') {
                return response()->json(['error' => 'Invalid token'], 401);
            }

            $user = auth()->user();

            $newAccessToken = JWTAuth::fromUser($user);

            return response()->json([
                'access_token' => $newAccessToken
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }
    }


    public function logout()
    {

        auth()->logout();

        return response()
            ->json(['message' => 'logout'])
            ->cookie('refresh_token', null, -1);
    }
}
