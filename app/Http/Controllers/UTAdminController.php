<?php

namespace App\Http\Controllers;

use App\Alarmstatus;
use App\Communication;
use App\Dashboards;
use App\Site;
use App\SiteReport;
use App\User;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Response;

class UTAdminController extends Controller
{
    //

    public function viewDashboard()
    {

        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $user_info = User::where('owner_id', $masterid)->count();
        $sitesum = Site::where('ut_id', $masterid)->count();
        $liveSite = DB::table('sites')->where('ut_id', $masterid)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '1')
            ->count();
        $downSite = DB::table('sites')->where('ut_id', $masterid)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '0')
            ->count()
         + DB::table('sites')->where('ut_id', $masterid)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', 'N')
            ->count();

        $userinfo['sum'] = $user_info;
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;

        $getTableData = DB::table('sites')->where('ut_id', $masterid)
            ->join('details', 'sites.serial_number', '=', 'details.serial_number')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '!=', 'N')
            ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
            ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
            ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
            ->select([
                'sites.serial_number', 'sites.site_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.up_time',
            ])

            ->get();
        // dd($getTableData);

        if (request()->ajax()) {
            return DataTables::of($getTableData)
                ->addIndexColumn()
                ->addColumn('action', 'action')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('ut_admin.ut_dashboard', $userinfo);
    }

    public function sitePreview()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $user_info = User::where('owner_id', $masterid)->count();
        $siteOwner = Site::where('ut_id', $masterid)->value('user_id');
        $alarmSound = Alarmstatus::where('user_id', $siteOwner)->first();
        $sitesum = Site::where('ut_id', $masterid)->count();
        $liveSite = DB::table('sites')->where('ut_id', $masterid)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '1')
            ->count();
        $downSite = DB::table('sites')->where('ut_id', $masterid)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '0')
            ->count() + DB::table('sites')->where('ut_id', $masterid)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', 'N')
            ->count();

        $userinfo['sum'] = $user_info;
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        $userinfo['alarmStatus'] = $alarmSound;
        return Response::json($userinfo);
    }

    public function resetAlarm()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $user_id = $masterid;
        $alarmSound = Alarmstatus::where('ut_id', $user_id)->get();
        foreach ($alarmSound as $key => $value) {
            $value->alarm_status = '0';
            $value->save();
        }

        return Response::json("success");
    }

    public function getUtSiteLocation()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $locations = DB::table('sites')->where('sites.ut_id', $masterid)
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

    public function getAllSiteUser()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $getAllSiteUser = User::where('owner_id', $masterid)->get();
        // dd($getAllSiteUser);
        if (request()->ajax()) {
            return DataTables::of($getAllSiteUser)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="siteuser/' . $row->id . '" data-toggle="tooltip" data-placement="top" title="View SiteUser"><i class="fa fa-eye text-success"></i></a>
                        &nbsp; &nbsp;
                        <a class="edit" data-toggle="modal" data-target="#modal-edit-siteuser" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-warning"></i></a>
                        &nbsp; &nbsp;
                        <a class="delete"  title="Delete" href="javascript:void(0)" data-id="' . $row->id . '"><i class="fa fa-trash text-danger"></i></a>
                        &nbsp; &nbsp;
                        </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('ut_admin.view_siteuser');

    }

    public function createSiteUser()
    {
        return view('ut_admin.create_siteuser');
    }

    public function addSiteUser(Request $request)
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $addUt = User::create([
            'name' => $request->name,
            'email' => $request->loginemail,
            'role' => 'SiteUser admin',
            'phone_number' => $phone_number,
            'address' => $request->address,
            'image' => 'img/profile/site.png',
            'password' => Hash::make($request->password),
            'owner_id' => $masterid,
        ]);
        $addUt->save();

        $communication = Communication::where('user_id', $addUt->id)
            ->where('email_address', $request->email)
            ->where('sms_mobile_number', $request->phone_number)->get()->count() > 0;
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        if (!$communication) {
            $communication = Communication::create([
                'sms_user_type' => 'First Respondent',
                'sms_mobile_number' => $phone_number,
                'sms_enable' => 'false',
                'email_user_type' => 'First Respondent',
                'email_address' => $request->email,
                'email_enable' => 'true',
                'user_id' => $addUt->id,
                'owner_id' => $masterid,
                'role' => "SiteUser admin",
                'schedule_time' => 0,
            ]);
            $communication->save();
        }
        return redirect()->back()->with('status', 'UT Added Successfully!');
    }

    public function getSingleSiteUser($id)
    {
        $getSiteUser = User::find($id);

        return response()->json($getSiteUser, 200);
    }

    public function editSiteUserInfo(Request $request)
    {
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $editSiteUser = User::findOrfail($request->id);
        $editSiteUser->name = $request->name;
        $editSiteUser->email = $request->email;
        $editSiteUser->phone_number = $phone_number;
        $editSiteUser->address = $request->address;

        $editSiteUser->save();

        if ($editSiteUser) {
            return response()->json([
                "status" => 200,
                "message" => "Site User Info updated successfully",
                "id" => $request->id,
            ]);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Failed to update Site User Info Details",
            ]);
        }
    }

    public function deleteSiteUser($id)
    {
        $deleteSiteUser = User::find($id)->delete();
        if ($deleteSiteUser) {
            return response()->json([
                "status" => 200,
                "message" => "UT Admin Successfully deleted",
                "id" => $id,
            ]);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Failed to delete UT Admin",
            ]);
        }
    }

    public function getSiteUserDetail($id)
    {
        //Get individual Site User Info
        $geSiteUserDetail = User::where('id', $id)->first();
        // dd($geSiteUserDetail);
        $sitecount = Site::where('ut_id', $id)->count();

        $getTableData = DB::table('sites')->where('ut_id', '=', $id)
            ->join('details', 'sites.serial_number', '=', 'details.serial_number')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '!=', 'N')

            ->select([
                'sites.serial_number', 'sites.site_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D',
            ])
            ->get();
        // dd($getTableData);
        if (request()->ajax()) {
            return DataTables::of($getTableData)
                ->addIndexColumn()
                ->addColumn('action', 'action')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('ut_admin.siteuser_detail', compact('geSiteUserDetail', 'sitecount'));
    }

    public function viewAllUtSites()
    {
        // $allSites = Site::where('user_id',  $masterid)->get();
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $allSites = DB::table('sites')->where('ut_id', $masterid)
            ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select(['sites.id', 'sites.site_number', 'sites.name', 'sites.uprisers', 'locations.long', 'locations.lat'])
            ->get();
        // dd($allSites);
        if (request()->ajax()) {
            return DataTables::of($allSites)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = ' &nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-allutsite" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-success"></i></a>
                    &nbsp; &nbsp;
                    </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('ut_admin.viewallsites');
    }

    // Method to Edit Individual Site

    public function getSingleSiteUserSite($id)
    {
        $getSiteUserSite = Site::find($id);

        return response()->json($getSiteUserSite, 200);
    }

    public function editSiteUserSiteInfo(Request $request)
    {
        $editSiteUserSite = Site::findOrfail($request->id);
        $editSiteUserSite->name = $request->name;
        $editSiteUserSite->save();

        if ($editSiteUserSite) {
            return response()->json([
                "status" => 200,
                "message" => "Site Info updated successfully",
                "id" => $request->id,
            ]);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Failed to update Site Info",
            ]);
        }
    }

    // Adding Sites to Siteuser Admin
    public function getSitesAndSiteUser()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $getAllSite = Site::where('ut_id', $masterid)->where('siteuser_id', null)->get();

        $allSiteUserAdmin = User::where('owner_id', $masterid)->get();

        return view('ut_admin.add_sites_user', compact('getAllSite', 'allSiteUserAdmin'));
    }

    public function addSiteAndSiteUser(Request $request)
    {
        $addSiteUser = Site::where('id', $request->siteid)->first();

        $addSiteUser->siteuser_id = $request->selectsiteuserid;
        // dd($addSiteUser);
        $addSiteUser->save();

        return redirect()->back();
    }

}