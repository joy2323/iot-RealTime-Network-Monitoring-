<?php

namespace App\Http\Controllers;

use App\Control;
use App\Dashboards;
use App\Site;
use App\User;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Response;

class ControlController extends Controller
{
    //

    public function viewcontrol()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $user = Auth::user();
        $ctrldata = array();
        $type = '';
        $index = 0;
        if ($user->role == "Client admin") {
            $user_info = User::where('owner_id', $masterid)->where('role', 'BU admin')->get();
            $type = 'client';
            foreach ($user_info as $key => $value) {
                # code...
                $ctrldata[$index] = $value;
                $ut_info = User::where('owner_id', $value->id)->get();
                $ctrldata[$index]->ut_info = $ut_info;
                $index++;

            }
        } else if ($user->role == "BU admin") {
            $type = 'bu';

            $user_info = $user;
            $ctrldata[0] = $user_info;
            $ut_info = User::where('owner_id', $user_info->id)->get();
            $ctrldata[0]->ut_info = $ut_info;

        } else if ($user->role = "UT admin") {
            $type = 'ut';
            $ut_info = $user;
            $user_info = User::where('id', $user->owner_id)->first();
            $ctrldata[0] = $user_info;
            $ctrldata[0]->ut_info = $ut_info;

        }

        return view('control_dashboard', compact('ctrldata', 'type'));
    }

    public function control($id)
    {

        $site = Site::where('id', $id)->first();
        $user = "";
        if (Auth::user()->role == "Client admin") {
            $user = $site->client_id;
        } else if (Auth::user()->role == "BU admin") {
            $user = $site->user_id;
        } else if (Auth::user()->role == "UT admin") {
            $user = $site->ut_id;
        }
        if (Auth::user()->id != $user) {
            return redirect('/Unauthorized');
        }
        $control_data = DB::table('sites')->where('sites.id', '=', $id)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->join('controls', 'sites.id', '=', 'controls.site_id')
            ->join('ctrl_feedback', 'sites.serial_number', '=', 'ctrl_feedback.serial_number')
            ->select([
                'sites.site_number', 'sites.ctrl_enable', 'sites.serial_number', 'sites.name',
                'sites.uprisers', 'site_status.DT_status', 'ctrl_feedback.*', 'controls.*',
            ])
            ->first();
        return view('control_terminal', compact('control_data'));
    }

    public function autocomplete(Request $request)
    {
        $str = $request->get('query');
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        if (Auth::user()->role == 'Client admin') {
            $data = Site::select("name")
                ->where('client_id', $masterid)
                ->where('sites.ctrl_enable', '>', '0')
                ->where('name', 'LIKE', '%' . $str . '%')
                ->get();

        } else if (Auth::user()->role == 'BU admin') {
            $data = Site::select("name")
                ->where('user_id', $masterid)
                ->where('sites.ctrl_enable', '>', '0')

                ->where('name', 'LIKE', '%' . $str . '%')
                ->get();

        } else if (Auth::user()->role == 'UT admin') {
            $data = Site::select("name")
                ->where('ut_id', $masterid)
                ->where('sites.ctrl_enable', '>', '0')

                ->where('name', 'LIKE', '%' . $str . '%')
                ->get();

        }

        return response()->json($data);
    }

    public function getTabledata()
    {
        $bu = (!empty($_GET["bu_id"])) ? ($_GET["bu_id"]) : ('');
        $ut = (!empty($_GET["ut_id"])) ? ($_GET["ut_id"]) : ('');
        $buname = (!empty($_GET["bu_name"])) ? ($_GET["bu_name"]) : ('');
        $utname = (!empty($_GET["ut_name"])) ? ($_GET["ut_name"]) : ('');
        $dtname = (!empty($_GET["dt_name"])) ? ($_GET["dt_name"]) : ('');
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        if ($dtname == "") {
            if ($ut == 'All' && $bu == 'All') {
                $user = Auth::user();
                if ($user->role == 'Client admin') {
                    $getTableData = DB::table('sites')->where('sites.client_id', '=', $masterid)
                        ->where('sites.ctrl_enable', '>', '0')

                        ->join('controls', 'sites.id', '=', 'controls.site_id')
                        ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                        ->where('site_status.DT_status', '!=', 'N')
                        ->select([
                            'sites.id', 'sites.site_number', 'sites.ctrl_enable', 'sites.serial_number', 'sites.name', 'controls.c1 AS CB_MAIN'
                            , 'controls.c2 AS CB_A', 'controls.c3 AS CB_B', 'controls.c4 AS CB_C', 'controls.c5 AS CB_D', 'site_status.DT_status AS status',
                        ])
                        ->get();

                } else if ($user->role == 'BU admin') {
                    $getTableData = DB::table('sites')->where('sites.user_id', '=', $masterid)
                        ->where('sites.ctrl_enable', '>', '0')

                        ->join('controls', 'sites.id', '=', 'controls.site_id')
                        ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                        ->where('site_status.DT_status', '!=', 'N')
                        ->select([
                            'sites.id', 'sites.site_number', 'sites.ctrl_enable', 'sites.serial_number', 'sites.name', 'controls.c1 AS CB_MAIN'
                            , 'controls.c2 AS CB_A', 'controls.c3 AS CB_B', 'controls.c4 AS CB_C', 'controls.c5 AS CB_D', 'site_status.DT_status AS status',
                        ])
                        ->get();

                } else if ($user->role == 'UT admin') {
                    $getTableData = DB::table('sites')->where('sites.ut_id', '=', $masterid)
                        ->where('sites.ctrl_enable', '>', '0')

                        ->join('controls', 'sites.id', '=', 'controls.site_id')
                        ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                        ->where('site_status.DT_status', '!=', 'N')
                        ->select([
                            'sites.id', 'sites.site_number', 'sites.ctrl_enable', 'sites.serial_number', 'sites.name', 'controls.c1 AS CB_MAIN'
                            , 'controls.c2 AS CB_A', 'controls.c3 AS CB_B', 'controls.c4 AS CB_C', 'controls.c5 AS CB_D', 'site_status.DT_status AS status',
                        ])
                        ->get();

                }

            } else if ($bu |= 'All' && $ut == 'All') {
                $bu_id = User::where('name', $buname)->value('id');

                $getTableData = DB::table('sites')->where('sites.user_id', $bu_id)
                    ->where('sites.ctrl_enable', '>', '0')
                    ->join('controls', 'sites.id', '=', 'controls.site_id')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('site_status.DT_status', '!=', 'N')
                    ->select([
                        'sites.id', 'sites.site_number', 'sites.ctrl_enable', 'sites.serial_number', 'sites.name', 'controls.c1 AS CB_MAIN'
                        , 'controls.c2 AS CB_A', 'controls.c3 AS CB_B', 'controls.c4 AS CB_C', 'controls.c5 AS CB_D', 'site_status.DT_status AS status',
                    ])
                    ->get();
            } else if ($bu |= 'All' && $ut |= 'All') {

                $ut_id = User::where('name', $utname)->value('id');
                $getTableData = DB::table('sites')->where('sites.ut_id', '=', $ut_id)
                    ->where('sites.ctrl_enable', '>', '0')
                    ->join('controls', 'sites.id', '=', 'controls.site_id')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('site_status.DT_status', '!=', 'N')
                    ->select([
                        'sites.id', 'sites.site_number', 'sites.ctrl_enable', 'sites.serial_number', 'sites.name', 'controls.c1 AS CB_MAIN'
                        , 'controls.c2 AS CB_A', 'controls.c3 AS CB_B', 'controls.c4 AS CB_C', 'controls.c5 AS CB_D', 'site_status.DT_status AS status',
                    ])
                    ->get();
            }

        } else {

            $getTableData = DB::table('sites')->where('sites.name', '=', $dtname)
                ->where('sites.ctrl_enable', '>', '0')
                ->join('controls', 'sites.id', '=', 'controls.site_id')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('site_status.DT_status', '!=', 'N')
                ->select([
                    'sites.id', 'sites.site_number', 'sites.ctrl_enable', 'sites.serial_number', 'sites.name', 'controls.c1 AS CB_MAIN'
                    , 'controls.c2 AS CB_A', 'controls.c3 AS CB_B', 'controls.c4 AS CB_C', 'controls.c5 AS CB_D', 'site_status.DT_status AS status',
                ])
                ->get();

        }

        return datatables()->of($getTableData)
            ->addIndexColumn()
            ->addColumn('action', 'actionctrl')
            ->rawColumns(['action'])->make(true);

    }

    public function getBU_UT(Request $request)
    {
        $input = $request->all();
        $user_id = $input['id'];
        $ut_info = User::where('owner_id', $user_id)->get();
        return response()->json($ut_info);
    }

    public function getUT(Request $request)
    {
        $input = $request->all();
        $user_id = $input['id'];
        $ownerid = User::where('id', $user_id)->value('owner_id');
        $user_info = User::where('id', $ownerid)->first();
        return response()->json($user_info);

    }

    public function sitedata($sitename)
    {
        $site_info = DB::table('sites')->where('sites.name', '=', $sitename)->first();
        $user_info = User::where('id', $site_info->user_id)->first();
        $ut_info = User::where('id', $site_info->ut_id)->first();
        return response()->json([
            "user_info" => $user_info,
            "ut_info" => $ut_info,
        ]);

    }

    public function validateControlPassword(Request $request)
    {
        $user = Auth::user();
        if ($user->role == "Client admin") {
            if (password_verify($request->password, $user->password)) {
                return response()->json([
                    "Auth_info" => 'Passed',
                    "Status" => '200',
                    "AuthControl" => $user->ctrl_auth,
                ]);

            } else {

                return response()->json([
                    "Auth_info" => 'Invalid password!!!',
                    "Status" => '201',
                ]);

            }
        } else if ($user->role == "BU admin") {
            $admin_ctrl_auth = User::where('id', $user->owner_id)->value('ctrl_auth');
            if (password_verify($request->password, $user->password)) {
                if ($admin_ctrl_auth == '1') {
                    return response()->json([
                        "Auth_info" => 'Passed',
                        "Status" => '200',
                        "AuthControl" => $user->ctrl_auth,
                    ]);

                } else {
                    return response()->json([
                        "Auth_info" => 'Passed',
                        "Status" => '200',
                        "AuthControl" => $admin_ctrl_auth,
                    ]);

                }

            } else {

                return response()->json([
                    "Auth_info" => 'Invalid password',
                    "Status" => '201',
                ]);

            }
        } else if ($user->role == "UT admin") {
            $buadmin_ctrl_auth = User::where('id', $user->owner_id)->first();
            $admin_ctrl_auth = User::where('id', $buadmin_ctrl_auth->owner_id)->value('ctrl_auth');
            if (password_verify($request->password, $user->password)) {
                if ($admin_ctrl_auth == '1') {
                    if ($buadmin_ctrl_auth->ctrl_auth == '1') {
                        return response()->json([
                            "Auth_info" => 'Passed',
                            "Status" => '200',
                            "AuthControl" => $user->ctrl_auth,
                        ]);

                    } else {
                        return response()->json([
                            "Auth_info" => 'Passed',
                            "Status" => '200',
                            "AuthControl" => $buadmin_ctrl_auth->ctrl_auth,
                        ]);

                    }
                } else {
                    return response()->json([
                        "Auth_info" => 'Passed',
                        "Status" => '200',
                        "AuthControl" => $admin_ctrl_auth,
                    ]);

                }

            } else {

                return response()->json([
                    "Auth_info" => 'Invalid password',
                    "Status" => '201',
                ]);

            }
        }

    }

    public function ControlENDN(Request $request)
    {

        $checkSite = Site::where("serial_number", $request->serial_number)->first();
        $checkctrl = Control::where("site_id", $checkSite->id)->first();
        $controlEN = $checkSite->ctrl_enable;
        $channelNum = $checkSite->uprisers;

        if ($checkctrl == null) {
            $ctrl_data = [];
            $ctrl_data['site_id'] = $checkSite->id;
            if ($controlEN == '1') {
                for ($i = 1; $i <= 8; $i++) {
                    if ($i <= $channelNum + 1) {
                        $ctrl_data["c$i"] = '0';
                    } else {
                        $ctrl_data["c$i"] = 'N';
                    }
                }
            } else if ($controlEN == '2') {

                for ($i = 1; $i <= 8; $i++) {
                    if ($i == 1) {
                        $ctrl_data["c$i"] = '0';
                    } else {
                        $ctrl_data["c$i"] = 'N';
                    }
                }
            }
            $checkctrl = Control::create($ctrl_data);
            $checkctrl->save();
        } else {
            $ctrl_data = [];

            if ($controlEN == '1') {
                for ($i = 2; $i <= 8; $i++) {
                    if ($i <= $channelNum + 1) {
                        $ctrl_data["c$i"] = '0';
                    } else {
                        $ctrl_data["c$i"] = 'N';
                    }

                }
            } else if ($controlEN == '2') {

                for ($i = 2; $i <= 8; $i++) {
                    $ctrl_data["c$i"] = 'N';
                }
            }
            DB::table('controls')
                ->where('site_id', $checkSite->id)
                ->update($ctrl_data);

        }

    }

    public function controlTest()
    {
        $clients = User::where('role', 'Client admin')
            ->select([
                'id', 'name',
            ])->get();
        return view('super_admin.control_dashboard', compact('clients'));
    }

    public function getClientUnit(Request $request)
    {

        $clients = User::where('role', 'BU admin')->where('owner_id', $request->id)
            ->where('master_role', 1)
            ->select([
                'id', 'name',
            ])->get();
        return response()->json($clients);
    }

    public function getClientSubUnit(Request $request)
    {

        $clients = User::where('role', 'UT admin')->where('owner_id', $request->id)
            ->where('master_role', 1)
            ->select([
                'id', 'name',
            ])->get();
        return response()->json($clients);

    }
    public function controlsites(Request $request)
    {
        
        $client = $request->client_id;
        $bu = $request->bu_id;
        $ut = $request->ut_id;
        $index = 0;
        $ctrldata = array();
        if ($client == "All" && $bu == "All" && $ut == "All") {
            $ctrlsites = DB::table('sites')
                ->where('sites.ctrl_enable', '>', '0')
                ->join('controls', 'sites.id', '=', 'controls.site_id')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('ctrl_feedback', 'sites.serial_number', '=', 'ctrl_feedback.serial_number')
                ->where('site_status.DT_status', '!=', 'N')
                ->get();
            foreach ($ctrlsites as $key => $value) {
                # code...
                $ctrldata[$index] = $value;
                $ctrldata[$index]->client = User::where('id', $value->client_id)->value("name");
                $ctrldata[$index]->bu = User::where('id', $value->user_id)->value("name");
                $ctrldata[$index]->ut = User::where('id', $value->ut_id)->value("name");
                $ctrldata[$index]->name = $value->name;
                $ctrldata[$index]->status = $value->DT_status;
                if($value->c8==1 && $value->z8 ==1){
                    $ctrldata[$index]->feedback ="OK";
                }else if($value->c8==2 && $value->z8 ==2){
                    $ctrldata[$index]->feedback ="OK";
                }else{
                    $ctrldata[$index]->feedback ="Not OK";
                }
               
                $index++;
            }
        }else if ($client != "All" && $bu == "All" && $ut == "All") {

                $ctrlsites = DB::table('sites')
                ->where('sites.client_id', $client)
                ->where('sites.ctrl_enable', '>', '0')
                ->join('controls', 'sites.id', '=', 'controls.site_id')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('ctrl_feedback', 'sites.serial_number', '=', 'ctrl_feedback.serial_number')
                ->where('site_status.DT_status', '!=', 'N')
                ->get();
            foreach ($ctrlsites as $key => $value) {
                # code...
                $ctrldata[$index] = $value;
                $ctrldata[$index]->client = User::where('id', $value->client_id)->value("name");
                $ctrldata[$index]->bu = User::where('id', $value->user_id)->value("name");
                $ctrldata[$index]->ut = User::where('id', $value->ut_id)->value("name");
                $ctrldata[$index]->name = $value->name;
                $ctrldata[$index]->status = $value->DT_status;
                if($value->c8==1 && $value->z8 ==1){
                    $ctrldata[$index]->feedback ="OK";
                }else if($value->c8==2 && $value->z8 ==2){
                    $ctrldata[$index]->feedback ="OK";
                }else{
                    $ctrldata[$index]->feedback ="Not OK";
                }
                $index++;
            }

        }
        else if ( $bu != "All" && $ut == "All") {

                $ctrlsites = DB::table('sites')
                ->where('sites.client_id', $client)
                ->where('sites.user_id', $bu)
                ->where('sites.ctrl_enable', '>', '0')
                ->join('controls', 'sites.id', '=', 'controls.site_id')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('ctrl_feedback', 'sites.serial_number', '=', 'ctrl_feedback.serial_number')
                ->where('site_status.DT_status', '!=', 'N')
                ->get();
            foreach ($ctrlsites as $key => $value) {
                # code...
                $ctrldata[$index] = $value;
                $ctrldata[$index]->client = User::where('id', $value->client_id)->value("name");
                $ctrldata[$index]->bu = User::where('id', $value->user_id)->value("name");
                $ctrldata[$index]->ut = User::where('id', $value->ut_id)->value("name");
                $ctrldata[$index]->name = $value->name;
                $ctrldata[$index]->status = $value->DT_status;
                if($value->c8==1 && $value->z8 ==1){
                    $ctrldata[$index]->feedback ="OK";
                }else if($value->c8==2 && $value->z8 ==2){
                    $ctrldata[$index]->feedback ="OK";
                }else{
                    $ctrldata[$index]->feedback ="Not OK";
                }
                $index++;
            }

        }
        else if ($ut != "All") {

                    $ctrlsites = DB::table('sites')
                    ->where('sites.client_id', $client)
                    ->where('sites.user_id', $bu)
                    ->where('sites.ut_id', $bu)
                    ->where('sites.ctrl_enable', '>', '0')
                    ->join('controls', 'sites.id', '=', 'controls.site_id')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->join('ctrl_feedback', 'sites.serial_number', '=', 'ctrl_feedback.serial_number')
                    ->where('site_status.DT_status', '!=', 'N')
                    ->get();
                foreach ($ctrlsites as $key => $value) {
                    # code...
                    $ctrldata[$index] = $value;
                    $ctrldata[$index]->client = User::where('id', $value->client_id)->value("name");
                    $ctrldata[$index]->bu = User::where('id', $value->user_id)->value("name");
                    $ctrldata[$index]->ut = User::where('id', $value->ut_id)->value("name");
                    $ctrldata[$index]->name = $value->name;
                    $ctrldata[$index]->status = $value->DT_status;
                    if($value->c8 == 1 && $value->z8 == 1){
                        $ctrldata[$index]->feedback ="OK";
                    }else if($value->c8 == 2 && $value->z8 == 2){
                        $ctrldata[$index]->feedback ="OK";
                    }else{
                        $ctrldata[$index]->feedback ="Not OK";
                    }
                    $index++;
                }
            

        }

        return DataTables::of($ctrldata)->make(true);
    }

    public function sendTestCommand(Request $request)
    {
        $site = Site::where('sites.ctrl_enable', '>', '0')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '1')
            ->get();
        foreach ($site as $key => $value) {
            $site_id = $value->id;
            $storeControl = Control::where('site_id', $site_id)->first();

            if ($request->CMD == "true" && ($storeControl->c8 == 0 || $storeControl->c8 == 2)) {
                $storeControl->c8 = '1';
            } else if ($request->CMD == "false" && $storeControl->c8 == 1) {
                $storeControl->c8 = '2';
            }

            $storeControl->save();
            $controldata = array('c8' => $storeControl->c8);
            $helperClass = new HelperClass();
            $response = $helperClass->sendCtrlSMS($phonenumber, json_encode($controldata), "infobip");
            \Log::info($controldata);
        }

        return response()->json([
            'message' => 'Successful',
            'status' => 200,
        ]);

    }
}