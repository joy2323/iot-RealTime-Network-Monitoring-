<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class BUAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role == 'Super admin') {
            return redirect()->route('superadmin');
        }

        if (Auth::user()->role == 'INJ admin') {
            return redirect()->route('injadmin');
        }

        if (Auth::user()->role == 'SiteUser admin') {
            return redirect()->route('siteadmin');
        }

        if (Auth::user()->role == 'BU admin') {
            return $next($request);
        }

        if (Auth::user()->role == 'Client admin') {
            return redirect()->route('clientadmin');
        }

        if (Auth::user()->role == 'UT admin') {
            return redirect()->route('utadmin');
        }
        // return $next($request);
    }
}