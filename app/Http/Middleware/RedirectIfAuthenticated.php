<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $role = Auth::guard($guard)->user()->role;
                switch ($role) {
                    case 'admin':
                        return redirect('/admin');
                    case 'staff':
                        return redirect('/staff');
                    case 'patron':
                        return redirect('/patron');
                    default:
                        return redirect('/welcome');
                }
            }
        }

        return $next($request);
    }
}
