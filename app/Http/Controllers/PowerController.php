<?php

namespace App\Http\Controllers;

use App\Dashboards;
use App\User;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Response;

class PowerController extends Controller
{

    public function powerView()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");

        if (Auth::user()->role == "Super admin") {
            $allPowerAnalysis = User::where('role', 'Client admin')->where("master_role", 1)->get();
            return view('super_admin.power_analysis', compact('allPowerAnalysis'));
        } else if (Auth::user()->role == "Client admin") {
            $allPowerAnalysis = User::where('owner_id', $masterid)->where('role', 'BU admin')->get();
            return view('client_admin.power_analysis', compact('allPowerAnalysis'));

        } else if (Auth::user()->role == "BU admin") {
            $allUtAdmin = User::where('owner_id', $masterid)->get();
            return view('bu_admin.power_analysis', compact('allUtAdmin'));
        } else {

            $allSiteUserAdmin = User::where('owner_id', $masterid)->get();

            return view('ut_admin.power_analysis', compact('allSiteUserAdmin'));
        }

    }

    public function viewPower()
    {

       
    }
    public function fetchPower()
    {
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
        $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
        $section = (!empty($_GET["selectut"])) ? ($_GET["selectut"]) : ('');
        $getPower = null;
        if (Auth::user()->role == "Super admin") {
            if ($section == 'All') {
                $Power = DB::table('sites')
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')
                    ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
                    ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select(['sites.name AS name',
                        DB::raw("sum(down_live_sites.up_duration) AS up_time"),
                        DB::raw("sum(down_live_sites.power) AS power"),
                        DB::raw("sum(down_live_sites.energy) AS energy")])
                    ->groupBy('name')->get();

                $getPower = array();
                $index = 0;
                foreach ($Power as $key => $value) {
                    $getPower[$index] = $value;
                    $bu = DB::table('sites')->where('sites.name', $value->name)
                        ->join('users', 'users.id', '=', 'sites.user_id')->select(['users.name'])->first();
                    $getPower[$index]->bu = $bu->name;
                    $index++;
                }

            } else {
                $Power = DB::table('sites')
                    ->where('client_id', $section)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')
                    ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
                    ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select(['sites.name AS name',
                        DB::raw("sum(down_live_sites.up_duration) AS up_time"),
                        DB::raw("sum(down_live_sites.power) AS power"),
                        DB::raw("sum(down_live_sites.energy) AS energy")])
                    ->groupBy('name')->get();

                $getPower = array();
                $index = 0;
                foreach ($Power as $key => $value) {
                    $getPower[$index] = $value;
                    $bu = DB::table('sites')->where('sites.name', $value->name)
                        ->join('users', 'users.id', '=', 'sites.user_id')->select(['users.name'])->first();
                    $getPower[$index]->bu = $bu->name;
                    $index++;
                }

            }
        } else if (Auth::user()->role == "Client admin") {
            if ($section == 'All') {
                $getPower = DB::table('sites')
                    ->where('client_id', $masterid)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')
                    ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
                    ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.name',
                        DB::raw("sum(down_live_sites.up_duration) AS up_time"),
                        DB::raw("sum(down_live_sites.power) AS power"),
                        DB::raw("sum(down_live_sites.energy) AS energy")])
                    ->groupBy('sites.name');
            } else {
                $getPower = DB::table('sites')
                    ->where('client_id', $masterid)->where('user_id', $section)
                    ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
                    ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.name',
                        DB::raw("sum(down_live_sites.up_duration) AS up_time"),
                        DB::raw("sum(down_live_sites.power) AS power"),
                        DB::raw("sum(down_live_sites.energy) AS energy")])
                    ->groupBy('sites.name');
            }
        } else if (Auth::user()->role == "BU admin") {

            if ($section == 'All') {
                $getPower = DB::table('sites')
                    ->where('user_id', $masterid)
                    ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
                    ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.name',
                        DB::raw("sum(down_live_sites.up_duration) AS up_time"),
                        DB::raw("sum(down_live_sites.power) AS power"),
                        DB::raw("sum(down_live_sites.energy) AS energy")])
                    ->groupBy('sites.name');
            } else {
                $getPower = DB::table('sites')
                    ->where('user_id', $masterid)->where('ut_id', $section)
                    ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
                    ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.name',
                        DB::raw("sum(down_live_sites.up_duration) AS up_time"),
                        DB::raw("sum(down_live_sites.power) AS power"),
                        DB::raw("sum(down_live_sites.energy) AS energy")])
                    ->groupBy('sites.name');

            }

        } else {
            if ($section == 'All') {
                $getPower = DB::table('sites')
                    ->where('ut_id', $masterid)
                    ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
                    ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.name',
                        DB::raw("sum(down_live_sites.up_duration) AS up_time"),
                        DB::raw("sum(down_live_sites.power) AS power"),
                        DB::raw("sum(down_live_sites.energy) AS energy")])
                    ->groupBy('sites.name');

            } else {
                $getPower = DB::table('sites')
                    ->where('ut_id', $masterid)->where('siteuser_id', $section)
                    ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
                    ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
                    ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
                    ->select([
                        'sites.name',
                        DB::raw("sum(down_live_sites.up_duration) AS up_time"),
                        DB::raw("sum(down_live_sites.power) AS power"),
                        DB::raw("sum(down_live_sites.energy) AS energy")])
                    ->groupBy('sites.name');

            }
        }
        return datatables()->of($getPower)->make(true);
    }

    public function fetchSiteQuery(Request $request)
    {
        $str = $request->get('query');
        $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
        if (Auth::user()->role == "Client admin") {
            $getPower = DB::table('sites')->where('sites.name', 'LIKE', '%' . $str . '%')
                ->where('client_id', $masterid)
                ->get();
        } else if (Auth::user()->role == "BU admin") {
            $getPower = DB::table('sites')->where('sites.name', 'LIKE', '%' . $str . '%')
                ->where('user_id', $masterid)
                ->get();
        }if (Auth::user()->role == "UT admin") {
            $getPower = DB::table('sites')->where('sites.name', 'LIKE', '%' . $str . '%')
                ->where('ut_id', $masterid)
                ->get();
        }

        return response()->json($getPower);
    }

    public function fetchPowerQuery(Request $request)
    {
        $id = $request->id;
        $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
        $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');

        $getPower = DB::table('sites')->where('sites.id', $id)
            ->join('down_live_sites', 'sites.id', '=', 'down_live_sites.site_id')
            ->whereDate("down_live_sites.created_at", '>=', date($start_date) . ' 00:00:00')
            ->whereDate('down_live_sites.created_at', '<=', date($end_date) . ' 23:59:59')
            ->select([
                'sites.name', 'down_live_sites.up_duration AS up_time',
                DB::raw("DATE_FORMAT(down_live_sites.created_at,'%Y-%m-%d') AS date"),
                "down_live_sites.power AS power",
                "down_live_sites.energy AS energy"])
            ->get();

        return datatables()->of($getPower)->make(true);
    }

}