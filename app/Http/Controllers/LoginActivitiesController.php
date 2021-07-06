<?php

namespace App\Http\Controllers;

use App\LoginActivities;
use App\User;
use Auth;
use DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LoginActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('login_logs');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LoginActivities  $loginActivities
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

        if (Auth::user()->role != "Super admin") {
            // get all login activites of client, BU, UT.
            $masterUser = Auth::user();
           
          //  if(!$contains){
                $loginActivities = LoginActivities::where('login_count', '>', 0)->orderBy('id', 'DESC')
                ->join('users', 'login_activities.email', '=', 'users.email')
                ->where('users.dashboard_id', $masterUser->dashboard_id)
                ->orWhere('users.owner_id', $masterUser->id)
                ->select(['login_activities.*',
                ])
                ->get(); 
				foreach ($loginActivities as $key => $value) {
				   $contains = Str::contains($value->email, 'susejgroup.net');
				   if($contains) {
						unset($loginActivities[$key]);
					}
				}
                if (request()->ajax()) {
                    return DataTables::of($loginActivities)
                        ->make(true);
                }
          //  }

        } else {
            //get all logins activites to Super admin inclusing super logins
            $loginActivities = LoginActivities::where('login_count', '>', 0)->orderBy('id', 'DESC')
                ->latest()->get();
            // dd($loginActivities);
            if (request()->ajax()) {
                return DataTables::of($loginActivities)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }
    }

}