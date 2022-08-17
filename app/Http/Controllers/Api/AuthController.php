<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Service\Auth\AuthService;


class AuthController extends Controller
{
        /**
     * @var AuthService
     */
    private AuthService $service;

    /**
     * @param AuthService $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function token(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['nullable'],
            'secret_key' => ['nullable'],
        ]);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ID/Password認証失敗時、401
        if (!Auth::attempt($credentials)) {
            return response()->json(['access_token' => null], 401);
        }

        $user = $request->user();
        assert($user instanceof User);

        // 2要素認証の設定がない場合はトークン発行
        if (is_null($user->google2fa_secret)) {
            $token = $user->createToken('access_token');
            return response()->json(['access_token' => $token->plainTextToken], 200);
        }

        // 以下、2要素認証の設定がある場合の処理
        try {
            $this->service->verifyOtp($validated, $user);
            $token = $user->createToken('access_token');
            return response()->json(['access_token' => $token->plainTextToken], 200);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }
}
