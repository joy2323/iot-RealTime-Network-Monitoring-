<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class InjAdmin
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

        if (Auth::user()->role == 'SiteUser admin') {
            return redirect()->route('siteadmin');
        }

        if (Auth::user()->role == 'BU admin') {
            return redirect()->route('buadmin');
        }

        if (Auth::user()->role == 'Client admin') {
            return redirect()->route('clientadmin');
        }

        if (Auth::user()->role == 'UT admin') {
            return redirect()->route('utadmin');
        }
        if (Auth::user()->role == 'INJ admin') {

            return $next($request);
        }
    }
}