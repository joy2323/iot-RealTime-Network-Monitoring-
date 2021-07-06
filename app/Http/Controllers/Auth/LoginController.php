<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    public function redirectTo()
    {
        //dd(Auth::user()->role);
        switch (Auth::user()->role) {
            case 'Super admin';
                $this->redirectTo = '/admin';
                return $this->redirectTo;
                break;

            case 'SiteUser admin';
                $this->redirectTo = '/dashboard';
                return $this->redirectTo;
                break;

            case 'Client admin';
                $this->redirectTo = '/client';
                return $this->redirectTo;
                break;

            case 'INJ admin';
                $this->redirectTo = '/injection-station';

                return $this->redirectTo;
                break;

            case 'BU admin';
                $this->redirectTo = '/bu';
                return $this->redirectTo;
                break;

            case 'UT admin';
                $this->redirectTo = '/ut';
                return $this->redirectTo;
                break;

            default:
                $this->redirectTo = '/login';
                return $this->redirectTo;
        }
    }

  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }
}