<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'cf-turnstile-response' => 'required',
        ], [
            'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification to proceed.'
        ]);

        if (!$this->validateTurnstile($request->input('cf-turnstile-response'), $request->ip())) {
            return back()->withErrors([
                'email' => 'Turnstile verification failed. Please try again.',
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');

        $throttleKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($request->input('email')).'|'.$request->ip());

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Log audit trail
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => 'User logged in successfully.',
                'ip_address' => $request->ip()
            ]);

            // If the user is a super admin, send a login notification email
            if ($user->isSuperAdmin()) {
                try {
                    $ipAddress = $request->ip();
                    $userAgent = $request->header('User-Agent') ?? 'Unknown Browser';
                    $time = now()->format('Y-m-d H:i:s T');
                    
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(
                        new \App\Mail\LoginNotificationMail($user, $ipAddress, $userAgent, $time)
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send login notification mail: ' . $e->getMessage());
                }
            }

            return $this->redirectBasedOnRole($user);
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Log audit trail
        if (Auth::check()) {
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => 'User logged out.',
                'ip_address' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'cf-turnstile-response' => 'required',
        ], [
            'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification to proceed.'
        ]);

        if (!$this->validateTurnstile($request->input('cf-turnstile-response'), $request->ip())) {
            return back()->withErrors([
                'email' => 'Turnstile verification failed. Please try again.',
            ])->onlyInput('email');
        }
        
        $user = User::where('email', $request->email)->first();
        if ($user) {
            // Generate a secure reset token and URL
            $token = \Illuminate\Support\Str::random(64);
            $resetUrl = route('password.reset', ['token' => $token]) . '?email=' . urlencode($user->email);
            
            // Send the reset instructions email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($user, $resetUrl));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Password reset mail sending failed: ' . $e->getMessage());
            }

            return back()->with('success', 'Password reset instructions have been sent to your email.');
        }

        return back()->withErrors(['email' => 'No user found with this email address.']);
    }

    public function showResetPassword($token = null)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'cf-turnstile-response' => 'required',
        ], [
            'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification to proceed.'
        ]);

        if (!$this->validateTurnstile($request->input('cf-turnstile-response'), $request->ip())) {
            return back()->withErrors([
                'email' => 'Turnstile verification failed. Please try again.',
            ])->onlyInput('email');
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'action' => 'password_reset',
                'description' => 'User password was reset.',
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('login')->with('success', 'Your password has been successfully reset! Please login.');
        }

        return back()->withErrors(['email' => 'Could not reset password. Email not found.']);
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->with('error', 'Unrecognized role.');
     }

    protected function validateTurnstile($responseToken, $ip)
    {
        $secret = config('services.turnstile.secret_key');
        
        // If we are in local/testing environment and using a dummy secret key, short-circuit validation to succeed
        if (app()->environment('local', 'testing') && str_starts_with($secret, '1x00000000000000000000')) {
            return true;
        }

        try {
            $http = \Illuminate\Support\Facades\Http::asForm();
            
            if (app()->environment('local')) {
                $http = $http->withOptions([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ],
                ]);
            }

            $response = $http->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $responseToken,
                'remoteip' => $ip,
            ]);

            return $response->json('success') === true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Turnstile verification failed with exception: ' . $e->getMessage());
            
            // In local or testing environments, fail-open to allow developers to work
            if (app()->environment('local', 'testing')) {
                \Illuminate\Support\Facades\Log::info('Allowing verification in local/testing environment despite Turnstile connection exception.');
                return true;
            }
            return false;
        }
    }
}
