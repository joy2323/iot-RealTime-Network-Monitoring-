<?php

namespace App\Http\Controllers;

use App\Alarmstatus;
use App\Helpers\HelperClass;
use App\SiteReport;
use App\User;
use Illuminate\Http\Request;
use App\Dashboards;
use Auth;
use DataTables;
use DB;
use Response;

use Carbon\Carbon;

class AlarmstatusController extends Controller
{

    public function viewAlarmReport()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        if (Auth::user()->role == "Super admin") {
            $alluser = User::where('role', 'Client admin')->where('master_role', '1')->get();
            return view('super_admin.alarmreport', compact('alluser'));
        }
        if (Auth::user()->role == "Client admin") {
            $allBuAdmin = User::where('owner_id', $masterid)->where('role', 'BU admin')->get();
            return view('client_admin.alarmreport', compact('allBuAdmin'));
        } else if (Auth::user()->role == "BU admin") {
            $allUtAdmin = User::where('owner_id', $masterid)->get();
            return view('bu_admin.alarmreport', compact('allUtAdmin'));
        } else if (Auth::user()->role == "UT admin") {
            $allUtUser = User::where('owner_id', $masterid)->get();
            return view('ut_admin.alarmreport', compact('allUtUser'));
        } else {
            return view('site_admin.alarmreport');
        }
    }

    public function fetchAlarmReport(Request $request)
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $status = (!empty($_GET["status"])) ? ($_GET["status"]) : ('');
        $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
        $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
        $section = (!empty($_GET["selectut"])) ? ($_GET["selectut"]) : ('');

        if (Auth::user()->role == "Client admin") {
            if ($status == 'All' && $section == 'All') {
                $getReport = DB::table('sites')->where('client_id', $masterid)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status == 'All' && $section != 'All') {
                $getReport = DB::table('sites')
                    ->where('client_id', $masterid)->where('user_id', $section)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status != 'All' && $section != 'All') {
                $getReport = DB::table('sites')
                    ->where('client_id', $masterid)->where('user_id', $section)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status != 'All' && $section == 'All') {
                $getReport = DB::table('sites')->where('client_id', $masterid)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else {
                $getReport = DB::table('sites')->where('client_id', $masterid)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            }
        } else if (Auth::user()->role == "BU admin") {
            if ($status == 'All' && $section == 'All') {
                $getReport = DB::table('sites')->where('user_id', $masterid)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status == 'All' && $section != 'All') {
                $getReport = DB::table('sites')
                    ->where('user_id', $masterid)->where('ut_id', $section)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status != 'All' && $section != 'All') {
                $getReport = DB::table('sites')
                    ->where('user_id', $masterid)->where('ut_id', $section)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status != 'All' && $section == 'All') {
                $getReport = DB::table('sites')->where('user_id', $masterid)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else {
                $getReport = DB::table('sites')->where('user_id', $masterid)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            }

        } else if (Auth::user()->role == "UT admin") {

            if ($status == 'All' && $section == 'All') {
                $getReport = DB::table('sites')->where('ut_id', $masterid)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status == 'All' && $section != 'All') {
                $getReport = DB::table('sites')
                    ->where('ut_id', $masterid)->where('siteuser_id', $section)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status != 'All' && $section != 'All') {
                $getReport = DB::table('sites')
                    ->where('ut_id', $masterid)->where('siteuser_id', $section)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else if ($status != 'All' && $section == 'All') {
                $getReport = DB::table('sites')->where('ut_id', $masterid)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            } else {
                $getReport = DB::table('sites')->where('ut_id', $masterid)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();
            }
        } else if (Auth::user()->role == "SiteUser admin") {
            if ($status == 'All') {
                $getReport = DB::table('sites')->where('siteuser_id', Auth::user()->id)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();

            } else {
                $getReport = DB::table('sites')->where('siteuser_id', Auth::user()->id)
                    ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                    ->where('site_reports.status', '=', $status)
                    ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                    ->select([
                        'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                    ])
                    ->orderBy('site_reports.created_at', 'DESC')
                    ->orderBy('site_reports.alarm', 'ASC')
                    ->get();

            }

        }
        return datatables()->of($getReport)->make(true);
    }
    public function alarmDurationUpdate()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        if (Auth::user()->role == "Client admin") {
            $getReport = DB::table('sites')->where('sites.client_id', $masterid)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('site_reports.status', 'Active')->where('alarm', "!=", "SITE DOWN")
                ->select(['site_reports.*'])
                ->orderBy('site_reports.alarm', 'asc')
                ->orderBy('site_reports.status', 'desc')
                ->orderBy('site_reports.created_at', 'desc')
                ->get();
            //  dd( $getReport);
            foreach ($getReport as $report) {

                $reports = SiteReport::where('id', $report->id)->first();
                $previousTime = $reports->created_at;
                $start = date($previousTime);
                $end = date('Y-m-d H:i:s');
                $starts = Carbon::parse($start);
                $ends = Carbon::parse($end);
                $interval = $starts->diff($ends);
                $duration = $interval->format('%dd:%hh:%im:%ss');
                $reports->duration = $duration;
                $reports->save();
            }
        } else if (Auth::user()->role == "BU admin") {
            $getReport = DB::table('sites')->where('user_id', $masterid)
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('status', 'Active')->where('alarm', "!=", "SITE DOWN")
                ->select(['site_reports.*'])
                ->orderBy('site_reports.alarm', 'asc')
                ->orderBy('site_reports.status', 'desc')
                ->orderBy('site_reports.created_at', 'desc')
                ->get();
            //  dd( $getReport);
            foreach ($getReport as $report) {

                $reports = SiteReport::where('id', $report->id)->first();
                $previousTime = $reports->created_at;
                $start = date($previousTime);
                $end = date('Y-m-d H:i:s');
                $starts = Carbon::parse($start);
                $ends = Carbon::parse($end);
                $interval = $starts->diff($ends);
                $duration = $interval->format('%dd:%hh:%im:%ss');
                $reports->duration = $duration;
                $reports->save();
            }

        } else if (Auth::user()->role == "UT admin") {
            $getReport = DB::table('sites')->where('ut_id', $masterid)
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('status', 'Active')->where('alarm', "!=", "SITE DOWN")
                ->select(['site_reports.*'])
                ->orderBy('site_reports.alarm', 'asc')
                ->orderBy('site_reports.status', 'desc')
                ->orderBy('site_reports.created_at', 'desc')
                ->get();
            //  dd( $getReport);
            foreach ($getReport as $report) {

                $reports = SiteReport::where('id', $report->id)->first();
                $previousTime = $reports->created_at;
                $start = date($previousTime);
                $end = date('Y-m-d H:i:s');
                $starts = Carbon::parse($start);
                $ends = Carbon::parse($end);
                $interval = $starts->diff($ends);
                $duration = $interval->format('%dd:%hh:%im:%ss');
                $reports->duration = $duration;
                $reports->save();
            }

        } else if (Auth::user()->role == "SiteUser admin") {
            $getReport = DB::table('sites')->where('siteuser_id', Auth::user()->id)
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('status', 'Active')->where('alarm', "!=", "SITE DOWN")
                ->select(['site_reports.*'])
                ->orderBy('site_reports.alarm', 'asc')
                ->orderBy('site_reports.status', 'desc')
                ->orderBy('site_reports.created_at', 'desc')
                ->get();
            //  dd( $getReport);
            foreach ($getReport as $report) {

                $reports = SiteReport::where('id', $report->id)->first();
                $previousTime = $reports->created_at;
                $start = date($previousTime);
                $end = date('Y-m-d H:i:s');
                $starts = Carbon::parse($start);
                $ends = Carbon::parse($end);
                $interval = $starts->diff($ends);
                $duration = $interval->format('%dd:%hh:%im:%ss');
                $reports->duration = $duration;
                $reports->save();
            }
        }

        return Response::json("active alarms duration update");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($getLabel, $siteId, $deviceId)
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Alarmstatus  $alarmstatus
     * @return \Illuminate\Http\Response
     */
    public function show(Alarmstatus $alarmstatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Alarmstatus  $alarmstatus
     * @return \Illuminate\Http\Response
     */
    public function edit(Alarmstatus $alarmstatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Alarmstatus  $alarmstatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alarmstatus $alarmstatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Alarmstatus  $alarmstatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alarmstatus $alarmstatus)
    {
        //
    }

    public function sendAlarmsNotification()
    {

        $activeAlarms = SiteReport::where('status', 'Active')->where('alarm', '!=', 'SITE DOWN')->get();
        foreach ($activeAlarms as $alarms) {
            $getLabel = $alarms->alarm;
            $siteId = $alarms->site_id;
            $helperClass = new HelperClass();
            $getNotification = $helperClass->sendScheduleNotification($alarms);
        }

    }
}