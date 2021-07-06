<?php

namespace App\Http\Controllers;

use App\Dashboards;
use App\Scada;
use App\Site;
use App\SiteStatus;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Response;

class DesktopAppApiController extends Controller
{

    public $successStatus = 200;
    public function sitePreview(Request $request)
    {
        $id = $request->id;
        $masterid = Dashboards::where("id", $id)->value("master_id");
        $user_info = User::where('owner_id', $masterid)->where('role', 'BU admin')
            ->where('master_role', 1)->get();
        $sitesum = Site::where('client_id', $masterid)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')->count();
        $liveSite = Site::where('client_id', $masterid)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('DT_status', '1')->count();
        $downSite = Site::where('client_id', $masterid)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('DT_status', '!=', '1')->count();
        $userinfo['sum'] = count($user_info);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        return response()->json($userinfo, 200);
    }

    public function siteSummary(Request $request)
    {
        $id = $request->id;
        $masterid = Dashboards::where("id", $id)->value("master_id");
        $summarydata = array();
        $index = 0;

        $user_info = User::where('role', 'BU admin')->where('owner_id', $masterid)
            ->where('master_role', 1)
            ->select([
                'id', 'name',
            ])->get();

        foreach ($user_info as $key => $value) {
            $sitesum = Site::where('user_id', $value->id)->count();
            $liveSite = SiteStatus::where('user_id', $value->id)->where('DT_status', '1')->count();
            $downSite = SiteStatus::where('user_id', $value->id)
                ->where('DT_status', '!=', '1')->count();
            $summarydata[$index] = $value;
            $summarydata[$index]->name = $value->name;
            $summarydata[$index]->sitesum = $sitesum;
            $summarydata[$index]->livesite = $liveSite;
            $summarydata[$index]->downsite = $downSite;
            $index++;
        }

        return Response::json($summarydata);
    }

    public function activateWorkspace(Request $request)
    {
        $ip_address = $request->ip_address;
        $mac_address = $request->mac_address;
        $email = $request->email;
        $client_id = User::where('email', $email)->where('role', 'Client admin')->where('master_role', 1)->value('id');
        if ($client_id != null) {
            $checkaccess = Scada::where('email', $email)->value('id');
            if ($checkaccess != null) {
                return response()->json(['Info' => 'Workstation already activated, please Login'], 201);
            } else {
                $scada_access = Scada::create([
                    'client_id' => $client_id,
                    'email' => $email,
                    'mac_address' => $mac_address,
                    'ip_address' => $ip_address,
                    'activate' => 1,
                ]);
                $scada_access->save();
                if ($scada_access) {
                    return response()->json(['Success' => 'Activation successful'], 200);
                } else {
                    return response()->json(['Fail' => 'Activation Fail'], 300);
                }

            }

        } else {
            return response()->json(['error' => 'Email not authorized'], 401);
        }

    }

    public function loginAuth(Request $request)
    {
        $ip_address = $request->ip_address;
        $mac_address = $request->mac_address;
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Scada::where('client_id', Auth::user()->id)->first();
            if ($user->activate != null) {
                if ($user->mac_address == $mac_address && $user->ip_address == $ip_address) {
                    $token = Str::random(60);
                    $user->access_token = hash('sha256', $token);
                    $user->save();
                    return response()->json(['token' => $user->access_token], 200);
                } else {
                    return response()->json(['error' => 'Ip or MAC compromised'], 401);
                }

            } else {
                return response()->json(['error' => 'Workstation not activate'], 401);
            }

        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->token;
        $user = Scada::where('access_token', $token)->first();
        if ($user) {
            $user->access_token = "";
            $user->save();
            return response()->json(['token' => ""], 200);
        } else {

            return response()->json(['error' => 'Logout Fail'], 401);
        }

    }

    public function checkAuth(Request $request)
    {
        $token = $request->access_token;
        $user = Scada::where('access_token', $token)->first();

        if ($user != null) {
            return response()->json(['success' => 'authorized'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function CBControl(Request $request)
    {
        $site = Site::where('serial_number', $request->serialnumber)->first();
        $site_id = $site->id;
        $uprisers = $site->uprisers;
        $phonenumber = $site->phone_number;
        $site_name = $site->name;
        $countsall = Control::where('site_id', $site_id)->count() > 0;
        $commandsent = '';
        $siteEnable = $site->ctrl_enable;
        if ($countsall) {
            $storeControl = Control::where('site_id', $site_id)->first();
            if ($siteEnable == '1') {
                if ($request->CM == "true") {
                    $storeControl->c1 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'MAIN:ON, ';
                } else if ($request->CM == "false") {

                    $storeControl->c1 = '0';
                    $commandsent = $commandsent . 'MAIN:OFF, ';
                }
                if ($request->CA == "true" && $uprisers >= 1) {
                    $storeControl->c2 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'UP_A:ON, ';
                } else if ($request->CA == "false" && $uprisers >= 1) {
                    $storeControl->c2 = '0';
                    $commandsent = $commandsent . 'UP_A:OFF, ';
                }
                if ($request->CB == "true" && $uprisers >= 2) {
                    $storeControl->c3 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'UP_B:ON, ';
                } else if ($request->CB == "false" && $uprisers >= 2) {
                    $storeControl->c3 = '0';
                    $commandsent = $commandsent . 'UP_B:OFF, ';
                }
                if ($request->CC == "true" && $uprisers >= 3) {
                    $storeControl->c4 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'UP_C:ON, ';
                } else if ($request->CC == "false" && $uprisers >= 3) {
                    $storeControl->c4 = '0';
                    $commandsent = $commandsent . 'UP_C:OFF, ';
                }
                if ($request->CD == "true" && $uprisers >= 4) {
                    $storeControl->c5 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'UP_D:ON, ';
                } else if ($request->CD == "false" && $uprisers >= 4) {
                    $storeControl->c5 = '0';
                    $commandsent = $commandsent . 'UP_D:OFF, ';
                }

            } else if ($siteEnable == '2') {
                if ($request->CM == "true") {
                    $storeControl->c1 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'MAIN:ON, ';
                } else if ($request->CM == "false") {

                    $storeControl->c1 = '0';
                    $commandsent = $commandsent . 'MAIN:OFF, ';
                }

                $storeControl->c2 = 'N';

                $storeControl->c3 = 'N';

                $storeControl->c4 = 'N';

                $storeControl->c5 = 'N';

            }
            $storeControl->save();

        }
        $controldata = array('c1' => $storeControl->c1, 'c2' => $storeControl->c2, 'c3' => $storeControl->c3, 'c4' => $storeControl->c4, 'c5' => $storeControl->c5);
        $helperClass = new HelperClass();
        $response = $helperClass->sendCtrlSMS($phonenumber, json_encode($controldata), "infobip");
        \Log::info($controldata);
        $logs = ControlLogs::create([
            'email' => Auth::user()->email,
            'user' => Auth::user()->name,
            'sitename' => $site_name,
            'command' => $commandsent,
            'ip_address' => \Request::ip(),
        ]);
        $logs->save();
        return response()->json([
            'message' => 'Successful',
            'status' => 200,
        ]);

    }

    public function CBControlFB(Request $request)
    {
        $feedback = CtrlFeedback::where('ctrl_feedback.serial_number', $request->serialnumber)
            ->join('sites', 'sites.serial_number', '=', 'ctrl_feedback.serial_number')
            ->join('controls', 'sites.id', '=', 'controls.site_id')
            ->select(['ctrl_feedback.*', 'controls.*',
            ])->first();
        return response()->json($feedback, 200);
    }

    public function loadScadaData(Request $request)
    {
        $token = $request->access_token;
        $client_id = Scada::where('access_token', $token)->value('client_id');

        $getData = DB::table('sites')->where('sites.client_id', '=', $client_id)
            ->join('details', 'sites.serial_number', '=', 'details.serial_number')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '!=', 'N')

            ->select([
                'sites.name', 'sites.serial_number', 'sites.site_number', 'site_status.DT_status',
                'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'details.*',
            ])
            ->get();

        return response()->json($getData, 200);

    }

    public function loadNodeDetails(Request $request)
    {
        $id = $request->id;

        $masterid = Dashboards::where("id", $id)->value("master_id");

        $getTableData = DB::table('sites')->where('sites.user_id', '=', $masterid)
            ->join('details', 'sites.serial_number', '=', 'details.serial_number')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '!=', 'N')
            ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
            ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
            ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
        // ->select([
        //     'sites.site_number', 'sites.serial_number', 'sites.name', 'site_status.DT_status',
        //     'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C',
        //     'site_status.Up_D', "down_live_sites.up_time", "down_live_sites.up_time", "down_live_sites.down_time",
        //     "down_live_sites.power", "down_live_sites.energy",
        //     'd1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11', 'd12',
        //     'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12',
        // ])

            ->select([
                'sites.name', 'site_status.DT_status', 'down_live_sites.up_time',
                'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C',
                'site_status.Up_D',
            ])
            ->get();
        return Response::json($getTableData, 200);
    }

    public function loadSiteDetails(Request $request)
    {
        $serial_number = $request->serial_number;
        $userinfo['detail'] = Site::where('serial_number', $serial_number)->first();
        $userinfo['status'] = SiteStatus::where('serial_number', $serial_number)->value('DT_status');
        $userinfo['analog_values'] = Detail::select('a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12')
            ->where('serial_number', $serial_number)->orderBy('updated_at', 'desc')->first();
        $userinfo['digital_values'] = Detail::select('d1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11', 'd12')
            ->where('serial_number', $serial_number)->orderBy('updated_at', 'desc')->first();
        return Response::json($userinfo);

    }

}