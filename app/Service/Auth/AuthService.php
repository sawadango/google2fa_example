<?php

namespace App\Service\Auth;

use PragmaRX\Google2FA\Google2FA;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Arr;

class AuthService
{
    /**
     * @var Google2FA
     */
    private Google2FA $google2fa;

    /**
     * @var PngWriter
     */
    private PngWriter $writer;

    /**
     * @param Google2FA $google2fa
     */
    public function __construct(Google2FA $google2fa, PngWriter $writer)
    {
        $this->google2fa = $google2fa;
        $this->writer = $writer;
    }

    /**
     * @param array $conditions
     * @param User $user
     * 
     * @return void
     */
    public function verifyOtp(array $conditions, User $user): void
    {
        $secretKey = Arr::get($conditions, 'secret_key');
        $otp = Arr::get($conditions, 'otp');

        if (is_null($secretKey) || is_null($otp)) {
            throw new \Exception('認証できませんでした');
        }

        assert(is_string($secretKey));
        assert(is_string($otp));

        if ($user->google2fa_secret !== $secretKey) {
            throw new \Exception('認証できませんでした');
        }

        $result = $this->google2fa->verifyKeyNewer($secretKey, $otp, $user->google2fa_timestamp);

        dd($result);
        if (!$result) {
            throw new \Exception('認証できませんでした');
        }

        $user->update([
            'google2fa_timestamp' => $result,
        ]);
    }
}
