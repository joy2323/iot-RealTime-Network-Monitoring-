<?php

namespace App\Http\Controllers;
use Auth;
use App\User;
use App\Site;
use App\SiteStatus;
use App\Device;
use App\Location;
use App\Detail;
use App\Alarmstatus;
use App\Communication;
use Illuminate\Support\Facades\Hash;
use App\PushNotifier;
use Illuminate\Http\Request;
use App\Dashboards;
use DB;


class MobileAppApiController extends Controller
{
    //Mobile API Controller

     public function Overview(Request $request)
    {
        $email = $request->email;
        $getUserInfo = User::whereEmail($request->email)->first();
        $role = $getUserInfo->role;
		if($role == "Super admin"){
			$user_info = User::where('role', 'Client admin')->where('master_role', 1)->get();
			$sitesum = Site::join('devices', 'devices.serial_number', '=', 'sites.serial_number')
				->where('device_category_id', '1')->count();
			$liveSite = SiteStatus::where('DT_status', '1')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
				->where('device_category_id', '1')->count();
			$downSite = SiteStatus::where('DT_status', '0')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
				->where('device_category_id', '1')->count() + SiteStatus::where('DT_status', 'N')
				->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
				->where('device_category_id', '1')->count();
				$userinfo['sum'] = count($user_info);
				$userinfo['site_sum'] = $sitesum;
				$userinfo['livesite'] = $liveSite;
				$userinfo['downsite'] = $downSite;
		}else{
            $dashboard_id = $getUserInfo->dashboard_id;
            $masterid = Dashboards::where("id", $dashboard_id)->value("master_id");
            if ($role == "BU admin") {
                $role_id = "user_id";
                $user_info = User::where('owner_id', $masterid)->get();
            } else if ($role == "Client admin") {
                $role_id = "client_id";
                $user_info = User::where('owner_id', $masterid)->where('role', 'BU admin')->where('master_role', 1)->get();
            } else if ($role == "UT admin") {
                $role_id = "ut_id";
                $user_info = User::where('owner_id', $masterid)->where('role', 'UT admin')->where('master_role', 1)->get();
            } else {
                $role_id = "siteuser_id";
            }
            $siteOwner = Site::where($role_id, $masterid)->value('user_id');
            $sitesum = Site::where('sites.' . $role_id, $masterid)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')->count();
            $liveSite = Site::where('sites.' . $role_id, $masterid)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('DT_status', '1')->count();
            $downSite = Site::where('sites.' . $role_id, $masterid)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('DT_status', '0')->count()
                + Site::where('sites.' . $role_id, $masterid)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('DT_status', 'N')->count();
            $userinfo['sum'] = count($user_info);
            $userinfo['site_sum'] = $sitesum;
            $userinfo['livesite'] = $liveSite;
            $userinfo['downsite'] = $downSite;
		}
        return response()->json([
            'status' => "OK",
            'overview' => $userinfo,
        ]);

    }
    public function mobileLogin(Request $request)
    {
        $mobileUUID = $request->uuid;
        $fcm_token = $request->token;
        $email = $request->email;
        $password = $request->password;

        if ((Auth::attempt(['email' => $email, 'password' => $password]))) {
            $userLogin = User::whereEmail($email)->select(['name', 'role', 'image', 'dashboard_id', 'id'])->first();
            $checknotify = PushNotifier::where("mobile_uid", $mobileUUID)->first();
            // return response()->json($checknotify);
          	if ($checknotify) {
                    $checknotify->fcm_token = $fcm_token;
                    $checknotify->email = $email;
                    $checknotify->user_id =$userLogin->id;
                    $checknotify->save();
            } else {
                $notify = PushNotifier::create([
                    'mobile_uid' => $mobileUUID,
                    'user_id' => $userLogin->id,
                    'email' => $email,
                    'fcm_token' => $fcm_token,
                ]);
                $notify->save();
            }

            return response()->json([
                'status' => "OK",
                'message' => 'User authorized ',
                'userInfo' => $userLogin,
            ]);
        } else {
            return response()->json([
                'status' => "Error",
                'message' => 'Unauthorized User',
            ]);
        }


    }
    public function userDeviceOverview(Request $request)
    {
        
        $userOverview = User::whereEmail($request->email)->first();

        if ($userOverview) {
            $masterid = Dashboards::where("id", $userOverview->dashboard_id)->value("master_id");

            $user_info = User::where('owner_id', $masterid)->count();
            $device = Device::where('user_id', $masterid)->count();
            $sitesum = Site::where('user_id', $masterid)->count();
            $liveSite = SiteStatus::where('user_id', $masterid)->where('DT_status', '1')->count();
            $downSite = SiteStatus::where('user_id', $masterid)
                ->Where('DT_status', '0')
                ->count() + SiteStatus::where('user_id', $masterid)
                ->where('DT_status', 'N')->count();

            $userinfo['sum'] = $user_info;
            $userinfo['device_sum'] = $device;
            $userinfo['site_sum'] = $sitesum;
            $userinfo['livesite'] = $liveSite;
            $userinfo['downsite'] = $downSite;

            $getTableData = DB::table('sites')->where('sites.user_id', '=', $masterid)
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('site_status.DT_status', '!=', 'N')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select([
                    'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                    'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                ])
                ->get();
                $userinfo['getTableData'] = $getTableData;
            return response()->json([
                'status' => '200',
                'result' => $userinfo,
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'Unauthorized User'
            ]);
        }

        
    }


    public function getDashboardData(Request $request)
    {
        $status = $request->status;
        $search_key = $request->search;
        $getUserInfo = User::whereEmail($request->email)->first();
        $dashboard_id = $getUserInfo->dashboard_id;
        $role = $getUserInfo->role;
        if ($role == "BU admin") {
            $role_id = "user_id";
        } else if ($role == "Client admin") {
            $role_id = "client_id";
        } else if ($role == "UT admin") {
            $role_id = "ut_id";
        } else {
            $role_id = "siteuser_id";
        }

        $masterid = Dashboards::where("id", $dashboard_id)->value("master_id");
        if ($status == "all") {
            if ($search_key=="null") {
                $getSiteUserData = DB::table('sites')->where('sites.' . $role_id, '=', $masterid)
                    ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('site_status.DT_status', '!=', 'N')
                    ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                    ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                    ->select([
                        'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                        'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                    ])->paginate(20);
                return response()->json([
                    'status' => "Ok",
                    'result' => $getSiteUserData,
                ]);
            } else {
                $getSiteUserData = DB::table('sites')->where('sites.' . $role_id, '=', $masterid)
                    ->where('sites.name', 'LIKE', '%' . $search_key . '%')
                    ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('site_status.DT_status', '!=', 'N')
                    ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                    ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                    ->select([
                        'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                        'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                    ])->paginate(20);
                return response()->json([
                    'status' => "Ok",
                    'result' => $getSiteUserData,
                ]);

            }

        } else if ($status == "live") {
          if ($search_key=="null") {
                $getSiteUserData = DB::table('sites')->where('sites.' . $role_id, '=', $masterid)
                    ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('site_status.DT_status', '=', '1')
                    ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                    ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                    ->select([
                        'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                        'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                    ])->paginate(20);
                return response()->json([
                    'status' => "Ok",
                    'result' => $getSiteUserData,
                ]);
            } else {
                $getSiteUserData = DB::table('sites')->where('sites.' . $role_id, '=', $masterid)
                    ->where('sites.name', 'LIKE', '%' . $search_key . '%')
                    ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('site_status.DT_status', '=', '1')
                    ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                    ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                    ->select([
                        'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                        'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                    ])->paginate(20);
                return response()->json([
                    'status' => "Ok",
                    'result' => $getSiteUserData,
                ]);

            }

        } else if ($status == "down") {
           if ($search_key=="null") {
                $getSiteUserData = DB::table('sites')->where('sites.' . $role_id, '=', $masterid)
                    ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('site_status.DT_status', '=', '0')
                    ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                    ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                    ->select([
                        'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                        'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                    ])->paginate(20);
                return response()->json([
                    'status' => "Ok",
                    'result' => $getSiteUserData,
                ]);
            } else {
                $getSiteUserData = DB::table('sites')->where('sites.' . $role_id, '=', $masterid)
                    ->where('sites.name', 'LIKE', '%' . $search_key . '%')
                    ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('site_status.DT_status', '=', '0')
                    ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                    ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                    ->select([
                        'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                        'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                    ])->paginate(20);
                return response()->json([
                    'status' => "Ok",
                    'result' => $getSiteUserData,
                ]);

            }

        }

        
    }


    public function allSiteAlarmStatus (Request $request) {

        $userEmail = User::whereEmail($request->email)->first();
        if ($userEmail) {
            $masterid = Dashboards::where("id", $userEmail->dashboard_id)->value("master_id");

            $user_info = User::where('owner_id', $masterid)->get();
            $alarmSound = Alarmstatus::where('user_id', $masterid)->first();
            $device = Device::where('user_id', $masterid)->get();
            $sitesum = Site::where('user_id', $masterid)->count();
            $liveSite = SiteStatus::where('user_id', $masterid)->where('DT_status', '1')->count();
            $downSite = SiteStatus::where('user_id', $masterid)
                ->Where('DT_status', '0')
                ->count() + SiteStatus::where('user_id', $masterid)
                ->where('DT_status', 'N')->count();
            $userinfo['sum'] = count($user_info);
            $userinfo['device_sum'] = count($device);
            $userinfo['site_sum'] = $sitesum;
            $userinfo['livesite'] = $liveSite;
            $userinfo['downsite'] = $downSite;
            $userinfo['alarmStatus'] = $alarmSound;

            return response()->json([
                'status' => '200',
                'result' => $userinfo,
                // 'result' => $userinfo = [
                //     $sitesum,
                //     $liveSite,
                //     $downSite,
                //     $alarmSound,
                // ],
                // 'pagination' => $userinfo->paginate(5)
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'Unauthorized User'
            ]);
        }


    }


    public function siteOverview(Request  $request){
        $userOverview = User::whereEmail($request->email)->first();
        if ($userOverview) {
            $masterid = Dashboards::where("id", $userOverview->dashboard_id)->value("master_id");
            $allSites = DB::table('sites')->where('user_id', $masterid)
            ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select(['sites.id', 'sites.site_number', 'sites.name', 'sites.uprisers', 'locations.long', 'locations.lat'])
            ->paginate(30);
            return response()->json([
                'status' => '200',
                'result' => $allSites,
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'Unauthorized User'
            ]);
        }


    }



    public function getUserSiteData(Request $request) {

        $getUserInfo = User::whereEmail($request->email)->first();
        // dd($sitecount);
        if ($getUserInfo) {
            $getSiteUserData = DB::table('sites')->where('ut_id', '=', $getUserInfo)
            ->join('details', 'sites.serial_number', '=', 'details.serial_number')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '!=', 'N')

            ->select([
                'sites.serial_number', 'sites.site_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D',
            ])
            ->paginate(30);
            return response()->json([
                'status' => '200',
                'result' => $getSiteUserData
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'Unauthorized User'
            ]);
        }

        
    }


    // public function singleSiteDetail ($id) {

    //     $userSiteInfo = Site::where('id', $id)->first();

    //     if ($userSiteInfo) {
    //         $userinfo['status'] = SiteStatus::where('serial_number', $userSiteInfo->serial_number)->value('DT_status');
    //         $userinfo['analog_values'] = Detail::select('a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24')
    //             ->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();
    //         $userinfo['digital_values'] = Detail::select('d1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11', 'd12', 'd13', 'd14', 'd15', 'd16', 'd17', 'd18', 'd19', 'd20', 'd21', 'd22', 'd23', 'd24')
    //             ->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();
    //         return response()->json([
    //             'status' => '200',
    //             'result' => $userinfo,
    //         ]);
    //     }
    // }


                
   public function singleSiteDetail(Request $request)
    {

        $userSiteInfo = Site::where('id', $request->id)->first();

        if ($userSiteInfo) {

			 $siteinfo['name']= $userSiteInfo->name;
            $upriser_cnt = $userSiteInfo->uprisers;
            $siteinfo['status'] = SiteStatus::where('serial_number', $userSiteInfo->serial_number)->value('DT_status');
            $siteinfo['location'] = Location::select('long', 'lat')->where('serial_number', $userSiteInfo->serial_number)->first();
            $siteinfo['dt_para'] = Detail::select('a1', 'a2', 'a3')
                ->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();

            $siteinfo['voltage_values'] = Detail::select('a4', 'a5', 'a6')->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();

            $siteinfo['current_values'] = Detail::select('a7', 'a8', 'a9')
                ->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();
           $uprisera = Detail::select('d1 as red', 'd2 as yellow', 'd3 as blue')
                ->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();
            $upriserb = Detail::select('d4 as red', 'd5 as yellow', 'd6 as blue')
                ->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();
            $upriserc = Detail::select('d7 as red', 'd8 as yellow', 'd9 as blue')
                ->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();
            $upriserd = Detail::select('d10 as red', 'd11 as yellow', 'd12 as blue')
                ->where('serial_number', $userSiteInfo->serial_number)->orderBy('updated_at', 'desc')->first();
			$siteinfo['upriser_cnt'] =$upriser_cnt ;
			$uprisera['name']="Upriser A";
            $upriserb['name']="Upriser B";
            $upriserc['name']="Upriser C";
            $upriserd['name']="Upriser D";
            if($upriser_cnt==1){
                $siteinfo['upriser']= array( $uprisera);

            }else if($upriser_cnt==2){
                $siteinfo['upriser']= array( $uprisera, $upriserb);

            }else if($upriser_cnt==3){
                $siteinfo['upriser']= array( $uprisera, $upriserb, $upriserc);

            }else {
		
                $siteinfo['upriser']= array( $uprisera, $upriserb, $upriserc, $upriserd);

           }
            return response()->json([
                'status' => 'OK',
                'result' => $siteinfo,
            ]);
        }

    }

    public function siteLocation (){

        $userSiteInfo = Site::where('id', $id)->first();

    }







}