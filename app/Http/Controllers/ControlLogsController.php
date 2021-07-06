<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use DB;
use App\Dashboards;
class ControlLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('control_logs');
    }

    public function show()
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
        $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
        if (Auth::user()->role == "Client admin") {
            $logs = DB::table('sites')->where('client_id',  $masterid)->where('ctrl_enable', ">", "0")
                ->join("control_logs", "control_logs.sitename", "sites.name")
                ->whereDate('control_logs.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('control_logs.created_at', '<=', date($end_date) . ' 23:59:59')
                ->join("users", "control_logs.user", "users.name")->select(['users.role', 'control_logs.*',
            ])->get();

        } else if (Auth::user()->role == "BU admin") {
            $client_name = DB::table('users')->where('users.id', Auth::user()->owner_id)->value('name');
            $logs = DB::table('sites')->where('user_id',  $masterid)->where('ctrl_enable', ">", "0")
                ->join("control_logs", "control_logs.sitename", "sites.name")
                ->whereDate('control_logs.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('control_logs.created_at', '<=', date($end_date) . ' 23:59:59')
                ->where('control_logs.user', "!=", $client_name)
                ->join("users", "control_logs.user", "users.name")->select(['users.role', 'control_logs.*',
            ])->get();

        } else {
            $logs = DB::table('sites')->where('ctrl_enable', ">", "0")
                ->join("control_logs", "control_logs.sitename", "sites.name")
                ->whereDate('control_logs.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('control_logs.created_at', '<=', date($end_date) . ' 23:59:59')
                ->join("users", "control_logs.user", "users.name")->select(['users.role', 'control_logs.*',
            ])->get();
        }
        return datatables()->of($logs)->make(true);
    }

}