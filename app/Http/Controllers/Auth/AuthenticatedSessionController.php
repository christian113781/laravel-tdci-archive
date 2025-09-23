<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
        $request->authenticate();
        $user = $request->user();


    if ($user->status !== 'verified') {
        Auth::logout();

        return back()->withErrors([
            'email' => 'Your account is not yet verified by the admin.',
        ]);
    }

    $request->session()->regenerate();
    
        if ($user->role === 'admin') {
          return redirect()->intended('/admin');
        } elseif ($user->role === 'staff') {
           return redirect()->intended('/staff');
        } else {
        return redirect()->intended('/patron');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
