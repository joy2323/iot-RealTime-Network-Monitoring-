<?php

namespace App\Http\Controllers;

use App\Dashboards;
use App\InjStation;
use App\Site;
use App\User;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Response;

class InjStationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        $getData = DB::table('sites')->where('sites.user_id', '=', $masterid)
            ->join('details', 'sites.serial_number', '=', 'details.serial_number')
            ->where('details.d1', '!=', 'N')
            ->select([
                'sites.*',
            ])
            ->first();
        $getDetails = DB::table('details')->where('details.serial_number', '=', $getData->serial_number)
            ->select([
                'details.d1', 'details.d2', 'details.d3', 'details.d4',
                'details.d5', 'details.d6', 'details.d7', 'details.d8',
                'details.d9', 'details.d10', 'details.d11', 'details.d12',
            ])
            ->first();
        $getLabel = DB::table('labels')->where('labels.serial_number', '=', $getData->serial_number)
            ->select([
                'labels.d1', 'labels.d2', 'labels.d3', 'labels.d4',
                'labels.d5', 'labels.d6', 'labels.d7', 'labels.d8',
                'labels.d9', 'labels.d10', 'labels.d11', 'labels.d12',
            ])
            ->first();
        $getStatus = DB::table('site_status')->where('site_status.serial_number', '=', $getData->serial_number)
            ->select([
                'site_status.DT_status',
            ])
            ->first();
        return view('inj_admin.inj_dashboard', compact('getData', 'getDetails', 'getLabel', 'getStatus'));
    }

    public function indexclient()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        $users = User::where('owner_id', $masterid)->where('role', 'INJ admin')->get();
        $sitesum = 0;
        foreach ($users as $key => $value) {
            # code...
            $sitesum = $sitesum + Site::where('user_id', $value->id)->count();
        }

        $userinfo['sum'] = $sitesum;

        $getTableData = DB::table('sites')->where('sites.client_id', '=', $masterid)
            ->join('details', 'sites.serial_number', '=', 'details.serial_number')
            ->where('details.d1', '!=', 'N')

            ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
            ->where('devices.device_category_id', '2')

            ->select([
                'sites.id as siteid', 'sites.site_number', 'sites.serial_number', 'sites.name', 'details.*',
            ])
            ->get();

        if (request()->ajax()) {
            return DataTables::of($getTableData)
                ->addIndexColumn()
                ->addColumn('action', 'actionInj')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('client_admin.client_Inj_dashboard', compact('userinfo', 'users'));

    }

    public function viewstations()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        $allSites = DB::table('sites')->where('sites.client_id', $masterid)
            ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
            ->where('devices.device_category_id', '2')
            ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select(['sites.id', 'sites.site_number', 'sites.name', 'sites.uprisers', 'locations.long', 'locations.lat'])
            ->get();
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

        return view('inj_admin.viewstations');
    }

    public function viewAlarmReport()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        $Inj = User::where('owner_id', $masterid)->get();

        return view('inj_admin.alarmreport', compact('Inj'));
    }

    public function fetchAlarmReport()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        $status = (!empty($_GET["status"])) ? ($_GET["status"]) : ('');
        $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
        $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
        if ($status == 'All') {
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
        return datatables()->of($getReport)->make(true);
    }

    public function alarmDurationUpdate()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

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
        return Response::json("active alarms duration update");
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
     * @param  \App\InjStation  $injStation
     * @return \Illuminate\Http\Response
     */
    public function show(InjStation $injStation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InjStation  $injStation
     * @return \Illuminate\Http\Response
     */
    public function edit(InjStation $injStation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InjStation  $injStation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InjStation $injStation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InjStation  $injStation
     * @return \Illuminate\Http\Response
     */
    public function destroy(InjStation $injStation)
    {
        //
    }
}