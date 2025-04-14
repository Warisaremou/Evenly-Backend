<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $user->save();

        // Generate the QR code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config(env('APP_NAME')),
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
            $user->two_factor_enabled = true;
            $user->save();
            return response()->json([
                'message' => '2FA verification set successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 401);
        }
    }

    public function validate2FA(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|string',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $google2fa = new Google2FA();

        // Verify the OTP
        $valid = $google2fa->verifyKey($user->google2fa_secret, $validated['otp']);

        if ($valid) {
            $user->two_factor_enabled = true;
            $user->save();
            return response()->json([
                'message' => '2FA verification set successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 401);
        }
    }

    public function validate2FA(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|string',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $google2fa = new Google2FA();

        // Verify the OTP
        $valid = $google2fa->verifyKey($user->google2fa_secret, $validated['otp']);

        if ($valid) {

            $user->update([
                'two_factor_enabled' => true
            ]);

            $token = $user->createToken('auth_token');

            return response()->json([
                'message' => '2FA vérifiée avec succès',
                'token' => $token->plainTextToken
            ]);
        } else {
            return response()->json([
                'message' => 'OTP invalide'
            ], 401);
        }
    }
    public function disable2FA(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        // Disable 2FA for the user
        $user->google2fa_secret = null;
        $user->two_factor_enabled = false;
        $user->save();

        return response()->json([
            'message' => '2FA disabled successfully'
        ]);
    }
}
