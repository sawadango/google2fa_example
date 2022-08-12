<?php

namespace App\Service\Auth;

use PragmaRX\Google2FA\Google2FA;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

class MfaService
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
     * @return string
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * @param User $user
     * @param string $secretKey
     * 
     * @return string
     */
    public function getQrCode(User $user, string $secretKey): string
    {
        $g2faUrl = $this->google2fa->getQRCodeUrl(
            '株式会社Hoge',
            $user->email,
            $secretKey
        );

        $qrCode = QrCode::create($g2faUrl)
            ->setEncoding(new Encoding('UTF-8'))
            ->setSize(300);

        return $this->writer->write($qrCode)->getDataUri();
    }
}
