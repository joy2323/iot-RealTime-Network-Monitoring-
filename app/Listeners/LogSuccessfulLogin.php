<?php

namespace App\Listeners;

use App\LoginActivities;
use Illuminate\Auth\Events\Login;
use Stevebauman\Location\Facades\Location;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        //
       
        $loginact = LoginActivities::where("email", $event->user->email)
			->whereDate('created_at', '>=', date('Y-m-d') . ' 00:00:00')
            ->whereDate('updated_at', '<=', date('Y-m-d') . ' 23:59:59')
			->first();
		 \Log::info( $loginact );
        $ip = \Illuminate\Support\Facades\Request::ip();
       // $location = Location::get('41.190.2.147');
        $location = Location::get($ip);
        if(!$loginact) {
            $logncnt = 1;
			LoginActivities::create([
                    'email' => $event->user->email,
                    'role' => $event->user->role,
                    'user' => $event->user->name,
                    'login_count' => $logncnt,
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'),
                    'ip_address' => $ip,
                    // 'location' => $location->countryName . ", " . $location->cityName,
                ]);
        } else {
			if($ip == $loginact->ip_address){
				$logincnt= $loginact->login_count + 1;
				$loginact->login_count= $logincnt;
				$loginact->save();
				 \Log::info( $loginact );
			}else{
				 $logncnt = 1;
				 LoginActivities::create([
						'email' => $event->user->email,
						'role' => $event->user->role,
						'user' => $event->user->name,
						'login_count' => $logncnt,
						'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'),
						'ip_address' => $ip,
						'location' => $location->countryName . ", " . $location->cityName,
					]);
				
			}
           
        }
                

    }
}
