<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsappBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function verificationNotice()
    {
        $user = User::findOrFail(session('verification_user_id'));

        return view('auth.verify-email', compact('user'));
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

        $this->sendVerificationEmail($user);
        session(['verification_user_id' => $user->id]);

        return redirect()->route('verification.notice')->with('success', 'Tautan verifikasi telah dikirim ke email Anda.');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $data['login'])
            ->orWhere('phone_number', $data['login'])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Email, nomor WhatsApp, atau password salah.',
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            $this->sendVerificationEmail($user);
            session(['verification_user_id' => $user->id]);

            return redirect()->route('verification.notice')->with('success', 'Email Anda belum diverifikasi. Tautan baru telah dikirim.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function verifyEmail(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            WhatsappBot::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone_number' => $user->phone_number,
                    'provider' => 'fonnte',
                    'name' => $user->name.' AI',
                    'system_prompt' => 'Anda adalah Customer Service yang ramah, membantu, singkat, dan profesional.',
                    'is_active' => false,
                ]
            );
        }

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget('verification_user_id');

        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi.');
    }

    public function resendVerification(Request $request)
    {
        $user = User::findOrFail(session('verification_user_id'));
        $this->sendVerificationEmail($user);

        return back()->with('success', 'Tautan verifikasi telah dikirim ulang.');
    }

    private function sendVerificationEmail(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
