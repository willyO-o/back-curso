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
            return response()->json(['error' => 'Usuario o contraseña incorrectos'], 422);
        }

        $user = auth('api')->user();
        $user->load('rol');

        // Guardar TTL original del access token y generar refresh token con TTL de 7 días
        $originalTTL = JWTAuth::factory()->getTTL();
        JWTAuth::factory()->setTTL(60 * 24 * 7); // 7 días en minutos
        $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);
        JWTAuth::factory()->setTTL($originalTTL); // Restaurar TTL original

        // eliminar el id del usuario del la variable user para no enviarlo al cliente
        unset($user->id);

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
            ->json(['message' => 'Sesión cerrada exitosamente'])
            ->cookie('refresh_token', null, -1);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required|string',
            'password_nueva' => 'required|string|min:6|confirmed',
        ]);

        $user = auth('api')->user();

        // Verificar que la contraseña actual sea correcta
        if (!password_verify($request->password_actual, $user->password)) {
            return response()->json(['error' => 'La contraseña actual es incorrecta'], 422);
        }

        // Actualizar la contraseña
        $user->update([
            'password' => bcrypt($request->password_nueva)
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada correctamente'
        ], 200);
    }
}
