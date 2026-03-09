<?php

namespace App\Http\Controllers\Api;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();
        $user->load('rol');

        $refreshToken = JWTAuth::fromUser($user, ['type' => 'refresh'], auth('api')->factory()->getTTL() * 60 * 24 * 7); // 7 días

        return response()
            ->json([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ])
            ->cookie(
                'refresh_token',  // nombre
                $refreshToken,    // valor
                60 * 24 * 7,      // 7 días
                '/',              // path
                null,             // domain
                false,            // secure (true en producción con HTTPS)
                true,             // httpOnly
                false,            // raw
                'Lax'             // sameSite (Lax permite envío en navegación normal)
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

            if ($payload->get('type') !== 'refresh') {
                return response()->json(['error' => 'Invalid token type'], 401);
            }

            // Obtener usuario desde el 'sub' (ID) del refresh token
            $user = User::findOrFail($payload->get('sub'));
            $newAccessToken = JWTAuth::fromUser($user);

            return response()->json([
                'access_token' => $newAccessToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }
    }

    public function logout()
    {
        auth('api')->logout();

        return response()
            ->json(['message' => 'logout'])
            ->cookie('refresh_token', null, -1);
    }
}
