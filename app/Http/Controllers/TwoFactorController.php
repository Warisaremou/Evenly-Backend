<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function generate2FASecret(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        $google2fa = new Google2FA();

        // Generate a new secret key
        $secretKey = $google2fa->generateSecretKey();

        // Store the secret key in the user's record
        $user->google2fa_secret = $secretKey;
        $user->two_factor_enabled = true;
        $user->save();

        // Generate the QR code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('APP_NAME'),
            $user->email,
            $secretKey
        );

        return response()->json([
            'secret' => $secretKey,
            'qr_code_url' => $qrCodeUrl,
            'message' => '2FA setup successful'
        ]);
    }

    public function verify2FA(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|string',
        ]);

        $user = Auth::guard('sanctum')->user();
        $google2fa = new Google2FA();

        // Verify the OTP
        $valid = $google2fa->verifyKey($user->google2fa_secret, $validated['otp']);

        if ($valid) {
            return response()->json([
                'message' => '2FA verification successful'
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 401);
        }
    }
}
