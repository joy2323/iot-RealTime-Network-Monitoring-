<?php

namespace App\Http\Controllers;

use App\Communication;
use App\Site;
use App\SiteReport;
use App\SiteStatus;
use App\User;
use Auth;
use Carbon\Carbon;
use DataTables;
use App\Dashboards;
use DB;
use Illuminate\Http\Request;
use Response;

class ClientAdminController extends Controller
{

    public function index()
    {

        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");      
        $users = User::where('owner_id', $masterid)->where('role', 'BU admin')->where('master_role', 1)->get();
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
            ->where('DT_status', '0')->count()
         + Site::where('client_id', $masterid)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('DT_status', 'N')->count();
        $userinfo['sum'] = count($users);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;

        return view('client_admin.client_dashboard', compact('userinfo', 'users'));
    }
    public function loadsite()
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        $id = (!empty($_GET["id"])) ? ($_GET["id"]) : ('');

        if ($id == 'All' || $id = "") {
            $getTableData = DB::table('sites')->where('sites.client_id', '=', $masterid)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
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

        } else {
            $name = (!empty($_GET["name"])) ? ($_GET["name"]) : ('');
            $id = User::where('name', $name)->value('id');
            $getTableData = DB::table('sites')->where('sites.user_id', '=', $id)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
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

        }
        return DataTables::of($getTableData)
            ->addIndexColumn()
            ->addColumn('action', 'action')
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getSiteLocation($id)
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");

        if ($id == 'All') {
            $locations = DB::table('sites')->where('sites.client_id', $masterid)
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->where('devices.device_category_id', '=', 1)
                ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
                ->join('site_status', 'locations.serial_number', '=', 'site_status.serial_number')
                ->select([
                    'sites.user_id', 'sites.serial_number', 'sites.name', 'site_status.DT_status'
                    , 'site_status.alarm_status', 'locations.lat', 'locations.long', 'locations.updated_at',
                ])

                ->get();
        } else {

            $locations = DB::table('sites')->where('sites.user_id', $id)
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->where('devices.device_category_id', '=', 1)
                ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
                ->join('site_status', 'locations.serial_number', '=', 'site_status.serial_number')
                ->select([
                    'sites.user_id', 'sites.serial_number', 'sites.name', 'site_status.DT_status'
                    , 'site_status.alarm_status', 'locations.lat', 'locations.long', 'locations.updated_at',
                ])

                ->get();

        }
        return Response::json($locations);
    }

    public function siteSummary($id)
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        $summarydata = array();
        $index = 0;
        if ($id == "All") {
            $user_info = User::where('role', 'BU admin')->where('owner_id', $masterid)
            ->where('master_role', 1)
                ->get();

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
        } else {
            $user_info = User::where('id', $id)->first();
            $sitesum = Site::where('user_id', $id)->count();
            $liveSite = SiteStatus::where('user_id', $id)->where('DT_status', '1')->count();
            $downSite = SiteStatus::where('user_id', $id)
                ->where('DT_status', '!=', '1')->count();
            $summarydata[$index] = $user_info;
            $summarydata[$index]->name = $user_info->name;
            $summarydata[$index]->sitesum = $sitesum;
            $summarydata[$index]->livesite = $liveSite;
            $summarydata[$index]->downsite = $downSite;

        }
        return Response::json($summarydata);
    }

    public function sitePreview($id)
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        $user_info = User::where('owner_id', $masterid)->where('role', 'BU admin')->where('master_role', 1)->get();
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
        $userinfo['info'] = $user_info;
        $userinfo['sum'] = count($user_info);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        return Response::json($userinfo);
    }

    public function getAllBu()
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        // dd($allUser);
        $viewBUAdmins = User::where('users.owner_id', $masterid)->where('master_role', 1)
            ->where('users.role', '=', 'BU admin')
            ->select([
                'users.id', 'users.name AS name', 'users.address AS address', 'users.ctrl_auth AS control',
            ])->get();
        $BUData = array();
        $index = 0;
        foreach ($viewBUAdmins as $key => $value) {
            # code...
            $comms = Communication::where('communications.user_id', $value->id)
                ->select([
                    'communications.email_address', 'communications.sms_mobile_number',
                ])->first();
            $BUData[$index] = $value;
            if ($comms != null) {
                $BUData[$index]->phone_number = '234' . $comms->sms_mobile_number;
                $BUData[$index]->email = $comms->email_address;

            } else {
                $BUData[$index]->phone_number = 'No phone number';
                $BUData[$index]->email = 'No Email address';

            }
            $index++;

        }

        if (request()->ajax()) {
            return DataTables::of($BUData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="buadmin/' . $row->id . '" data-toggle="tooltip" data-placement="top" title="View User"><i class="fa fa-eye text-success"></i></a>
                        &nbsp; &nbsp;
                        <a class="edit" data-toggle="modal" data-target="#modal-edit-buinfo" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-success"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('client_admin.viewall_bu');
    }

    public function getBUAdminDetails($id)
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        //Get individual BU Info
        $getBUDetails = User::where('id', $id)->first();

        $communication = Communication::where('user_id', $id)
            ->select([
                'email_address as email', 'sms_mobile_number  as phone_number',
            ])->get();

        $getTableData = DB::table('sites')->where('sites.user_id', '=', $id)
            ->join('details', 'sites.serial_number', '=', 'details.serial_number')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '!=', 'N')

            ->select([
                'sites.serial_number', 'sites.site_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D',
            ])
            ->get();
        // dd($getTableData);
        $sitecount = $getTableData->count();
        if (request()->ajax()) {
            return DataTables::of($getTableData)
                ->addIndexColumn()
                ->addColumn('action', 'action')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('client_admin.buadmin_details', compact('getBUDetails', 'sitecount', 'communication'));
    }

    public function getSingleBUAdmin($id)
    {

        $getBUAdmin=array();      
        $user = User::where('id',$id)
        ->where('users.role', '=', 'BU admin')
        ->select(['users.id', 'users.name AS name', 'users.address AS address', 'users.ctrl_auth AS control'])->first();
        $getBUAdmin[0]=$user;
        $comm=Communication::where('communications.user_id', $id)
        ->select([
             'communications.email_address AS email', 'communications.sms_mobile_number AS phone_number',
            ])
            ->first();
            if(!$comm){
                $app = app();
                $comm = $app->make('stdClass');
                $comm->email ="No Email Attached";
                $comm->phone_number ="No Phone Number Attached";
            }   
            $getBUAdmin[0]->email = $comm->email ;
            $getBUAdmin[0]->phone_number = $comm->phone_number ;
        return response()->json($getBUAdmin, 200);
    }


    // public function getSingleBUAdmin($id)
    // {
    //     $getBUAdmin = Communication::where('communications.user_id', $id)
    //         ->join('users', 'users.id', '=', 'communications.user_id')
    //         ->where('users.role', '=', 'BU admin')
    //         ->select([
    //             'users.id', 'users.name AS name', 'users.address AS address', 'users.ctrl_auth AS control', 'communications.email_address AS email', 'communications.sms_mobile_number AS phone_number',
    //         ])
    //         ->first();
    //     return response()->json($getBUAdmin, 200);
    // }

    public function editBUAdminInfo(Request $request)
    {
        
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        $editUser = User::findOrfail($request->id);
        $editUser->address = $request->address;
        $editUser->ctrl_auth = $request->enable;
        $editUser->save();

        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $editComm = Communication::where('communications.user_id', $request->id)->first();
       
        if(!$editComm){
            $editComm = Communication::create([
                'sms_user_type' => "First respondent",
                'sms_mobile_number' => $phone_number,
                'sms_enable' => '0',
                'email_user_type' => "First respondent",
                'email_address' => $request->email,
                'email_enable' => '1',
                'user_id' => $editUser->id,
                'owner_id' => $masterid,
                'role' => $editUser->role,
                'schedule_time' => "0",
            ]);
            
        }else{
            $editComm->email_address = $request->email;
            $editComm->sms_mobile_number = $phone_number;  
        }
    
        $editComm->save();
        if ($editUser && $editComm) {
            return response()->json([
                "status" => 200,
                "message" => "BU Info updated successfully",
                "id" => $request->id,
            ]);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Failed to update BU Info Details",
            ]);
        }
    }

    // To View All Sites
    public function allSitesPage()
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        $allBUAdmin = User::where('owner_id', $masterid)->where('role', 'BU admin')->where('master_role', 1)->get();
        $ClientUT = array();
        $index = 0;
        foreach ($allBUAdmin as $key => $value) {
            # code...
            $allUtAdmin = User::where('owner_id', $value->id)->where('role', 'UT admin')->get();
            foreach ($allUtAdmin as $key => $value) {
                # code...
                $ClientUT[$index] = $value;
                $ClientUT[$index]->id = $value->id;
                $ClientUT[$index]->name = $value->name;
                $index++;
            }

        }
        return view('client_admin.viewall_clientsites', compact('allBUAdmin', 'ClientUT'));
    }
    public function viewAllSites(Request $request)
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        $ut = $request->ut;
        $bu = $request->bu;
        if ($ut == "All" && $bu == "All") {
            $allSites = DB::table('sites')->where('client_id', $masterid)
                ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->select(['sites.id', 'sites.site_number', 'sites.name', 'sites.uprisers', 'locations.long', 'locations.lat'])
                ->get();
        } else if ($ut == "All" && $bu != "All") {
            $allSites = DB::table('sites')->where('user_id', $bu)
                ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->select(['sites.id', 'sites.site_number', 'sites.name', 'sites.uprisers', 'locations.long', 'locations.lat'])
                ->get();

        } else if ($ut != "All" && $bu != "All") {
            $allSites = DB::table('sites')->where('ut_id', $ut)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
                ->select(['sites.id', 'sites.site_number', 'sites.name', 'sites.uprisers', 'locations.long', 'locations.lat'])
                ->get();
        }
        return DataTables::of($allSites)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = ' &nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-site" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-info"></i></a>
                    &nbsp; &nbsp;';
                return $btn;
            })

        // <a class="edit" data-toggle="modal" data-target="#modal-view-site" data-id="' . $row->id . '" data-placement="top" title="View" ><i class="fa fa-eye text-info"></i></a>
        // &nbsp; &nbsp;
            ->rawColumns(['action'])
            ->make(true);

    }

    public function loadBUUTs($id)
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");

        if ($id == "All") {
            $allBUAdmin = User::where('owner_id', $masterid)->where('role', 'BU admin')->where('master_role', 1)->get();
            $ClientUT = array();
            $index = 0;
            foreach ($allBUAdmin as $key => $value) {
                # code...
                $allUtAdmin = User::where('owner_id', $value->id)->where('role', 'UT admin')->get();
                foreach ($allUtAdmin as $key => $value) {
                    # code...
                    $ClientUT[$index] = $value;
                    $ClientUT[$index]->id = $value->id;
                    $ClientUT[$index]->name = $value->name;
                    $index++;
                }

            }
        } else {

            $ClientUT = User::where('owner_id', $id)->where('role', 'UT admin')->get();
        }
        return response()->json($ClientUT, 200);
    }

    public function loadUTBU($id)
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        if ($id == "All") {
            $GetBU = User::where('owner_id', $masterid)->where('role', 'BU admin')->where('master_role', 1)->get();
        } else {
            $GetBUID = User::where('id', $id)->value('owner_id');
            $GetBU = User::where('id', $GetBUID)->get();
        }

        return response()->json($GetBU, 200);
    }

}