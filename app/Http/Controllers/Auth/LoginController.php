<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Sistem SPK hanya mengizinkan 2 role: Admin dan Kalab
            if (!in_array($user->role, ['admin', 'kalab'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akses ditolak. Sistem ini hanya diperuntukkan bagi Administrator dan Kepala Laboratorium.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            session()->forget('selected_semester_id');

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'kalab') {
                return redirect()->route('kalab.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan tidak cocok.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
