<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Log;
use App\Models\UserLoginSession;
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

    // Record login session
    UserLoginSession::create([
        'user_id' => $user->id,
        'login_at' => now(),
    ]);

    Log::create([
        'user_id' => $user->id,
        'event_type' => 'user_login',
        'description' => '[' . strtoupper($user->role) . '] ' . $user->email . ' logged in.',
    ]);

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
        $user = Auth::user();

        // Record logout session if user exists
        if ($user) {
            UserLoginSession::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest('id')
                ->first()
                ?->update(['logout_at' => now()]);

            Log::create([
                'user_id' => $user->id,
                'event_type' => 'user_logout',
                'description' => '[' . strtoupper($user->role) . '] ' . $user->email . ' logged out.',
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
