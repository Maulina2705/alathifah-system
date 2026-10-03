<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $loginInput = $request->input('login', $request->input('email'));
        $request->merge(['login' => $loginInput]);

        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (! $user->is_active) {
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'BLOCKED_LOGIN',
                    'description' => "Pengguna non-aktif ({$user->name} / {$user->email}) mencoba login ke sistem.",
                    'ip_address' => $request->ip(),
                ]);

                Auth::logout();

                return back()->withErrors([
                    'login' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
                    'email' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
                ]);
            }

            AuditLog::log('LOGIN', "Pengguna {$user->name} berhasil login ke dalam sistem.");

            return redirect()->intended(route('dashboard'));
        }

        AuditLog::create([
            'user_id' => null,
            'action' => 'FAILED_LOGIN',
            'description' => "Percobaan login gagal untuk akun: {$loginInput} (IP: {$request->ip()})",
            'ip_address' => $request->ip(),
        ]);

        return back()->withErrors([
            'login' => 'Email/Username atau password yang Anda masukkan salah.',
            'email' => 'Email/Username atau password yang Anda masukkan salah.',
        ])->onlyInput('login', 'email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::log('LOGOUT', 'Pengguna '.Auth::user()->name.' telah logout.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
