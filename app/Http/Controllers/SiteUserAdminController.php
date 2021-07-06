<?php

namespace App\Http\Controllers;

use App\Alarmstatus;
use App\Site;
use App\User;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Response;
use App\Dashboards;

class SiteUserAdminController extends Controller
{

    public function viewDashboard()
    {
        // $user_info = User::where('owner_id',  $masterid)->count();
      
        $sitesum = Site::where('siteuser_id',  Auth::user()->id)->count();
        $liveSite = DB::table('sites')->where('siteuser_id', Auth::user()->id)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '1')
            ->count();
        $downSite = DB::table('sites')->where('siteuser_id',  Auth::user()->id)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '0')
            ->count()
         + DB::table('sites')->where('siteuser_id',  Auth::user()->id)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', 'N')
            ->count();

        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        $getTableData = DB::table('sites')->where('siteuser_id',  Auth::user()->id)
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

        return view('site_admin.site_dashboard', $userinfo);

    }

    public function getSiteUserSiteLocation()
    {
       
        $locations = DB::table('sites')->where('sites.siteuser_id', Auth::user()->id)
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
        $siteOwner = Site::where('siteuser_id', Auth::user()->id)->value('user_id');
        $alarmSound = Alarmstatus::where('user_id', $siteOwner)->first();
        $sitesum = Site::where('siteuser_id', Auth::user()->id)->count();
        $liveSite = DB::table('sites')->where('siteuser_id',  Auth::user()->id)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '1')
            ->count();
        $downSite = DB::table('sites')->where('siteuser_id',  Auth::user()->id)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', '0')
            ->count() + DB::table('sites')->where('siteuser_id', Auth::user()->id)
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('site_status.DT_status', '=', 'N')
            ->count();

        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        $userinfo['alarmStatus'] = $alarmSound;
        return Response::json($userinfo);
    }

    public function resetAlarm()
    {
        $site = Site::where('siteuser_id', $user_id)->get();
        foreach ($site as $key => $value) {
            $alarm= Alarmstatus::where('site_id',$value->id)->first();
            $alarm->alarm_status = '0';
            $value->save();
        }

        return Response::json("success");
    }

}