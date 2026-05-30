<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, remember: false)) {
            ActivityLogger::log('admin.login.failed', 'Failed admin login attempt for: '.$credentials['email'], null);

            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        if (! Auth::user()->is_admin) {
            ActivityLogger::log('admin.login.failed', 'Non-admin login attempt: '.Auth::user()->email);
            Auth::logout();

            return back()->withErrors(['email' => 'You do not have admin access.'])->onlyInput('email');
        }

        ActivityLogger::log('admin.login.success', 'Admin logged in: '.Auth::user()->email, Auth::id());

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}
