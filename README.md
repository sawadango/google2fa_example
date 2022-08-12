# このレポジトリについて

これは PHP カンファレンス沖縄 2022 の LT 枠でトークすることになった「Laravel で二要素認証をサクッと実装してみた」の内容のサンプルレポジトリです。
Laravel は API として使用する想定です。

# 各種バージョン

```
Laravel: 9.24.0
PHP: 8.1.8
pragmarx/google2fa: ^8.0
endroid/qr-code: ^4.4


https://github.com/antonioribeiro/google2fa

https://github.com/endroid/qr-code
```

なお、お手元のスマホ・タブレットに Google 認証システムをインストールしてください。

```
Android: https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2

iOS: https://apps.apple.com/jp/app/google-authenticator/id388497605
```

# 環境構築

## 諸々セットアップ

```
./vendor/bin/sail up
```

## DB 用意

```
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan migrate --env=testing
```

# 動作確認

## テストで動作確認

```
./vendor/bin/sail artisan test --testsuite=Feature
```

## MFA アカウント登録 API の動作確認について

MFA のアカウント登録 API(`App\Http\Controllers\Api\MfaController@store`)のテストはモック化するのが面倒だったので手動でシークレットキーと OTP を入力して確認してください。

### シークレットキー発行

```
1.
Tests\Feature\Auth\Mfa\GetIndexTest.phpの$responseをddあたりで出力し、シークレットキーとQRコードのURL(base64)を取得

2.
QRコードのURLを適当なサイトのimgタグとかに入れてQRコード表示

3.
Google認証システムでQRコード読み取り

4.
Tests\Feature\Auth\Mfa\PostTest.phpの$secretKeyと$otpにそれぞれ値をコピペ
$secretKey ... 1で出力したシークレットキー
$otp ... Google認証システムで表示されているOTP(6桁の数字)

5.
テスト実行
```
