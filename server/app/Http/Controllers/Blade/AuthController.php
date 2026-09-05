<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\User;
use App\Models\WhatsappBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function otpForm()
    {
        $userId = session('otp_user_id');
        abort_unless($userId, 403);
        $user = User::findOrFail($userId);

        return view('auth.verify-otp', compact('user'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:30', 'unique:users,phone_number'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => Hash::make($data['password']),
        ]);

        $this->sendOtp(
            $user,
            'register'
        );
        session([
            'otp_user_id' => $user->id,
            'otp_purpose' => 'register',
        ]);

        return redirect()->route('otp.form')->with('success', 'OTP verifikasi telah dikirim ke Whatsapp anda.');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => 'required',
            'string',
        ]);

        $user = User::query()
            ->where('email', $data['login'])
            ->orWhere('phone_number', $data['login'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'login' => 'Email atau no Whatsapp tidak ditemukan',
            ]);
        }

        if (! $user->phone_verified_at) {
            throw ValidationException::withMessages([
                'login' => 'No whatsapp belum diverifikasi',
            ]);
        }

        $this->sendOtp(
            $user,
            'login'
        );

        session([
            'otp_user_id' => $user->id,
            'otp_purpose' => 'login',
        ]);

        return redirect()->route('otp.form')->with('success', 'Kode OTP login telah dikirim ke Whatsapp anda');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);
        $userId = session('otp_user_id');
        $purpose = session('otp_purpose');

        if (! $userId || ! $purpose) {
            return redirect()->route('login')->withErrors([
                'login' => 'Sesi login telah berakhir',
            ]);
        }
        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('login')->withErrors([
                'login' => 'User tidak ditemukan.',
            ]);
        }

        $otp = OtpVerification::query()->where('user_id', $user->id)->where('purpose', $purpose)->whereNull('verified_at')->latest()->first();

        if (! $otp) {
            return back()->withErrors([
                'otp' => 'OTP tidak ditemukan.',
            ]);
        }

        if (now()->greaterThan($otp->expires_at)) {
            return back()->withErrors([
                'otp' => 'OTP sudah expired.',
            ]);
        }

        if ($otp->attempts >= 5) {
            return back()->withErrors([
                'otp' => 'Terlalu banyak percobaan',
            ]);
        }

        if (! Hash::check($request->otp, $otp->otp_hash)) {
            $otp->increment('attempts');

            return back()->withErrors([
                'otp' => 'OTP salah',
            ]);
        }

        $otp->update([
            'verified_at' => now(),
        ]);

        if ($purpose === 'register') {
            $user->update([
                'phone_verified_at',
            ]);

            WhatsappBot::firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'phone_number' => $user->phone_number,
                    'provider' => 'fonnte',
                    'name' => $user->name.'AI',
                    'system_prompt' => 'Anda adalah Customer Service yang ramah, membantu, singkat, dan profesional.',
                    'is_active' => true,
                ]
            );
        }

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget([
            'otp_user_id',
            'otp_purpose',
        ]);

        return redirect()->route('dashboard')->with('success', 'Login berhasil');
    }

    private function sendOtp(User $user, string $purpose): void
    {
        OtpVerification::query()
            ->where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->delete();

        $otp = (string) random_int(100000, 999999);

        OtpVerification::create([
            'user_id' => $user->id,
            'phone_number' => $user->phone_number,
            'purpose' => $purpose,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempt' => 0,
        ]);

        $token = config('services.fonnte.token');
        if (! $token) {
            throw ValidationException::withMessages([
                'login' => 'Fonnte token belum dikonfigurasi.',
            ]);
        }

        $target = preg_replace('/\D+/', '', $user->phone_number);

        if (str_starts_with($target, '0')) {
            $target = '62'.substr($target, 1);
        } elseif (str_starts_with($target, '8')) {
            $target = '62'.$target;
        }

        $response = Http::connectTimeout(5)
            ->timeout(15)
            ->withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => "Kode OTP anda adalah: {$otp}. ".
                    'Kode berlaku selama 5 menit. '.
                    'Jangan bagikan kode ini kepada siapapun.',
            ]);

        logger()->info('Fonnte response', [
            'http_status' => $response->status(),
            'target' => $target,
            'message_id' => $response->json('id'),
            'request_id' => $response->json('requestid'),
            'process' => $response->json('process'),
            'provider_status' => $response->json('status'),
            'body' => $response->body(),
            'json' => $response->json(),
        ]);

        if ($response->failed() || $response->json('status') !== true) {
            throw ValidationException::withMessages([
                'login' => 'Gagal mengirim OTP WhatsApp.',
            ]);
        }

    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
