<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\Auth\MfaService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class MfaController extends Controller
{
    private MfaService $service;

    public function __construct(MfaService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $secretKey = $this->service->generateSecretKey();

        $qrCode = $this->service->getQrCode($user, $secretKey);

        return response()->json(
            [
                'secretKey' => $secretKey,
                'qrCode' => $qrCode,
            ],
            200
        );
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required'],
            'secretKey' => ['required'],
        ]);

        $user = $request->user();
        assert($user instanceof User);

        DB::beginTransaction();
        try {
            $this->service->verify($validated, $user);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => $e->getMessage()
                ],
                400
            );
        }

        return response()->json(
            [
                'message' => '認証に成功しました'
            ],
            200
        );
    }
}
