<?php

namespace App\Http\Controllers;

use App\Alarmstatus;
use App\Communication;
use App\Dashboards;
use App\Device;
use App\Site;
use App\SiteReport;
use App\SiteStatus;
use App\User;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Response;

class BUAdminController extends Controller
{
    public function viewBu()
    {
        // $userinfo = Auth::user();
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

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

        if (request()->ajax()) {
            return DataTables::of($getTableData)
                ->addIndexColumn()
                ->addColumn('action', 'action')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bu_admin.bu_dashboard', $userinfo);
    }

    public function getSiteLocation()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        $locations = DB::table('sites')->where('sites.user_id', $masterid)
            ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->join('site_status', 'locations.serial_number', '=', 'site_status.serial_number')
            ->join('alarmstatuses', 'sites.id', '=', 'alarmstatuses.site_id')
            ->select([
                'sites.user_id', 'sites.serial_number', 'sites.name', 'site_status.DT_status'
                , 'site_status.alarm_status', 'locations.lat', 'locations.long', 'locations.updated_at',
            ])
            ->get();
        return Response::json($locations);
    }
    public function sitePreview()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

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
        return Response::json($userinfo);
    }

    public function resetAlarm()
    {
        \Log::info('reset alarm sound');
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $user_id = $masterid;
        $alarmSound = Alarmstatus::where('user_id', $user_id)->get();
        foreach ($alarmSound as $key => $value) {
            $value->alarm_status = '0';
            $value->save();
        }

        return Response::json("success");
    }

    public function getAllUt()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        $getUser = User::where('owner_id', $masterid)
            ->where('users.role', '=', 'UT admin')
            ->select([
                'users.id', 'users.name AS name', 'users.address AS address',
            ])
            ->get();

        $getUT = array();
        $index = 0;
        foreach ($getUser as $key => $value) {
            # code...
            $getComm = Communication::where('communications.user_id', $value->id)
                ->select([
                    'communications.email_address', 'communications.sms_mobile_number',
                ])
                ->first();

            $getUT[$index] = $value;
            if ($getComm != null) {
                $getUT[$index]->email = $getComm->email_address;
                $getUT[$index]->phone_number = '234' . $getComm->sms_mobile_number;
            } else {
                $getUT[$index]->email = "Email Required";
                $getUT[$index]->phone_number = "Phone number Required";
            }
            $index++;
        }

        if (request()->ajax()) {
            return DataTables::of($getUT)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->email == "Email Required") {
                        $btn = '<a href="ut/' . $row->id . '" data-toggle="tooltip" data-placement="top" title="View User"><i class="fa fa-eye text-success"></i></a>
                        &nbsp; &nbsp;
                        <a class="edit" data-toggle="modal" data-target="#modal-edit-data" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-success"></i></a>';

                    } else {
                        $btn = '<a href="ut/' . $row->id . '" data-toggle="tooltip" data-placement="top" title="View User"><i class="fa fa-eye text-success"></i></a>
                        &nbsp; &nbsp;
                        <a class="edit" data-toggle="modal" data-target="#modal-edit-data" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-success"></i></a>';

                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bu_admin.alluts');
    }

    // Method to edit UT Info
    public function getUt($id)
    {

        $getUT = array();

        $user = User::where('id', $id)
            ->where('users.role', '=', 'UT admin')
            ->select(['users.id', 'users.name AS name', 'users.address AS address', 'users.ctrl_auth'])->first();
        $getUT[0] = $user;
        $comm = Communication::where('communications.user_id', $id)
            ->select([
                'communications.email_address AS email', 'communications.sms_mobile_number AS phone_number',
            ])
            ->first();
        if (!$comm) {
            $app = app();
            $comm = $app->make('stdClass');
            $comm->email = "No Email Attached";
            $comm->phone_number = "No Phone Number Attached";
        }
        $getUT[0]->email = $comm->email;
        $getUT[0]->phone_number = $comm->phone_number;
        return response()->json($getUT, 200);
    }

    public function editUtInfo(Request $request)
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $editUser = User::findOrfail($request->id);
        $editUser->address = $request->address;
        $editUser->ctrl_auth = $request->enable;
        $editUser->save();
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $editComm = Communication::where('communications.user_id', $request->id)->first();

        if (!$editComm) {
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

        } else {
            $editComm->email_address = $request->email;
            $editComm->sms_mobile_number = $phone_number;
        }

        $editComm->save();

        if ($editUser && $editComm) {
            return response()->json([
                "status" => 200,
                "message" => "Ut Info updated successfully",
                "id" => $request->id,
            ]);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Failed to update Ut Info Details",
            ]);
        }
    }

    public function getUtDetails($id)
    {

        //Get individual Ut Info
        $getUtDetail = array();
        $user = User::where('id', $id)
            ->where('users.role', '=', 'UT admin')
            ->select(['users.id', 'users.name AS name', 'users.address AS address'])->first();
        $getUtDetail[0] = $user;
        $comm = Communication::where('communications.user_id', $id)
            ->select([
                'communications.email_address AS email', 'communications.sms_mobile_number AS phone_number',
            ])
            ->first();
        if (!$comm) {
            $app = app();
            $comm = $app->make('stdClass');
            $comm->email = "No Email Attached";
            $comm->phone_number = "No Phone Number Attached";
        }
        $getUtDetail[0]->email = $comm->email;
        $getUtDetail[0]->phone_number = $comm->phone_number;
        $getTableData = DB::table('sites')->where('ut_id', '=', $id)
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
        $sitecount = $getTableData->count();

        if (request()->ajax()) {
            return DataTables::of($getTableData)
                ->addIndexColumn()
                ->addColumn('action', 'action')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bu_admin.utdetails', compact('getUtDetail', 'sitecount'));
    }

    // To View All Sites
    public function viewAllSites()
    {
        // $allSites = Site::where('user_id',  $masterid)->get();
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        $allSites = DB::table('sites')->where('user_id', $masterid)
            ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select(['sites.id', 'sites.site_number', 'sites.name', 'sites.uprisers', 'locations.long', 'locations.lat'])
            ->get();
        // dd($allSites);
        if (request()->ajax()) {
            return DataTables::of($allSites)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = ' &nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-site" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-info"></i></a>
                    &nbsp; &nbsp;

                    </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bu_admin.viewallsites');
    }

    // Method to Edit Individual Site

    // To get Site User Page
    public function getSiteUser()
    {
        $getAllSite = Site::where('user_id', $masterid)->where('ut_id', null)->get();

        $allUtAdmin = User::where('owner_id', $masterid)->get();

        return view('bu_admin.addsiteuser', compact('getAllSite', 'allUtAdmin'));
    }

    public function addSiteUser(Request $request)
    {
        // dd($request->siteid);
        $addSiteUser = Site::where('id', $request->siteid)->first();

        $addSiteUser->ut_id = $request->selectutid;
        // dd($addSiteUser);
        $addSiteUser->save();

        return redirect()->back();
    }
 

}