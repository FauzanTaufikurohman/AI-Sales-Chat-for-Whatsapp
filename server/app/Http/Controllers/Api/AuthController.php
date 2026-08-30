<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:30', 'unique:users,phone_number'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create($data + ['status' => 'pending']);

        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(5);
        $otpHash = Hash::make($otp);

        OtpVerification::query()->where('phone_number', $data['phone_number'])->delete();
        OtpVerification::create([
            'user_id' => $user->id,
            'phone_number' => $data['phone_number'],
            'otp_hash' => $otpHash,
            'expires_at' => $expiresAt,
            'attempts' => 0,
        ]);
        $fonnteToken = config('services.fonnte.token');

        if ($fonnteToken) {
            Http::withHeaders([
                'Authorization' => $fonnteToken,
            ])->post('https://api.fonnte.com/send', [
                'target' => $data['phone_number'],
                'message' => "Kode OTP WhatsApp Anda adalah: {$otp}. Kode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapapun.",
            ]);
        }

        if (config('app.debug')) {
            logger()->info("OTP for phone number {$data['phone_number']}: {$otp}");
        }
        return response()->json([
            'message' => 'Registration successful. OTP sent for phone verification.',
            'user' => $user,
            'otp' => $otp,
            'expires_at' => $expiresAt->toISOString(),
        ], 201);
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone_number' => ['required', 'string', 'max:30'],
        ]);

        $phoneNumber = $data['phone_number'];
        $otp = (string) random_int(100000, 999999);
        $otpHash = Hash::make($otp);
        $expiresAt = now()->addMinutes(5);

        OtpVerification::query()->where('phone_number', $phoneNumber)->delete();

        OtpVerification::create([
            'phone_number' => $phoneNumber,
            'otp_hash' => $otpHash,
            'expires_at' => $expiresAt,
            'attempts' => 0,
        ]);

        return response()->json([
            'message' => 'OTP sent successfully.',
            'otp' => $otp,
            'expires_at' => $expiresAt->toISOString(),
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone_number' => ['required', 'string', 'max:30'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $verification = OtpVerification::query()
            ->where('phone_number', $data['phone_number'])
            ->latest('created_at')
            ->first();

        abort_if(! $verification, 404, 'OTP has not been requested.');
        abort_if($verification->expires_at->isPast(), 410, 'OTP has expired.');
        abort_if($verification->verified_at !== null, 409, 'OTP has already been used.');

        $verification->increment('attempts');

        if ($verification->attempts > 5 || ! Hash::check($data['otp'], $verification->otp_hash)) {
            if ($verification->attempts >= 5) {
                $verification->update(['verified_at' => now()]);
            }

            abort(422, 'Invalid or expired OTP.');
        }

        $user = User::query()->where('phone_number', $data['phone_number'])->first();
        if ($user) {
            $user->forceFill([
                'phone_verified_at' => now(),
                'status' => 'active',
            ])->save();
        }

        $verification->update(['verified_at' => now()]);

        return response()->json([
            'message' => 'OTP verified successfully.',
            'verified_at' => now()->toISOString(),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['login' => ['required', 'string'], 'password' => ['required', 'string']]);
        $user = User::query()->where('email', $data['login'])->orWhere('phone_number', $data['login'])->first();
        abort_unless($user && Hash::check($data['password'], $user->password), 422, 'Invalid credentials.');
        abort_if($user->status !== 'active', 403, 'Account is inactive or phone number has not been verified.');
        abort_if($user->phone_verified_at === null, 403, 'Phone number must be verified before login.');

        return response()->json(['user' => $user, 'token' => $user->createToken('mobile')->plainTextToken]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
