<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Verifikasi Cloudflare Turnstile.
     *
     * Dicek eksplisit (bukan sebagai aturan closure) agar SELALU dijalankan —
     * aturan closure bersifat non-implisit, sehingga bila field turnstile tidak
     * dikirim sama sekali (POST manual) aturannya akan dilewati → celah bypass.
     *
     * Bila TURNSTILE_SECRET_KEY belum diisi (mis. lokal/dev), verifikasi dilewati
     * agar tidak memblokir login sebelum kunci dikonfigurasi.
     */
    public function ensureTurnstileVerified(): void
    {
        $secret = config('services.turnstile.secret_key');

        if (empty($secret)) {
            return;
        }

        $token = $this->input('cf-turnstile-response');
        $verified = false;

        if (! empty($token)) {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $this->ip(),
            ]);

            $verified = (bool) ($response->json('success') ?? false);
        }

        if (! $verified) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Verifikasi keamanan gagal. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Attempt to authenticate the request's credentials.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $this->ensureTurnstileVerified();

        $login = $this->input('login');
        
        // Cari user berdasarkan email ATAU username
        $user = \App\Models\User::where('email', $login)
            ->orWhere('username', $login)
            ->first();

        // Jika user tidak ditemukan, set default credentials (ini akan gagal di Auth::attempt)
        $credentials = [
            'email' => $user ? $user->email : $login, 
            'password' => $this->input('password')
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Username atau password salah.',
            ]);
        }

        // Check if user is active
        if (! Auth::user()->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'login' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     * Lockout: 5 attempts per 5 minutes for brute force protection.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     * Uses login + IP to prevent brute force per account.
     * Decay time: 5 minutes (300 seconds).
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}
