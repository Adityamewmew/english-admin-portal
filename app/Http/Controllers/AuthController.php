<?php

namespace App\Http\Controllers;

use App\Constants\UserConst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('_admin.auth.login');
    }

    public function doLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->access_type !== UserConst::SUPERADMIN && $user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();

                return back()->withErrors([
                    'login_error' => 'Akses ditolak. Hanya Administrator yang dapat mengakses panel ini.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'login_error' => 'Email atau Password tidak sesuai, periksa kembali',
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
