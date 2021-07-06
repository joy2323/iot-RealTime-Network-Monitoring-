<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilsAPIController extends Controller
{

    public function cleanalarmtracker()
    {
        $getReport = DB::table('alarmtracker')
            ->whereDate('created_at', '<', date('Y-m-d') . ' 00:00:00')
            ->delete();

        return response()->json(["status" => 200, "message" => "alarm tracker cleaned"]);

    }

    
    public function cleanSitereport()
    {
        $today = Carbon::today();

        $site = Site::join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')->get();
        foreach ($site as $key => $value) {
            # code...
            $getReport = DB::table('site_reports')->where('site_reports.site_id', '=', $value->id)
                ->where('site_reports.status', '=', 'Resolved')
                ->whereDate('site_reports.created_at', '<=', date($today))
                ->whereRaw('site_reports.updated_at <= DATE_ADD(site_reports.created_at, INTERVAL 15 MINUTE)')
                ->delete();
			
 			$Report = DB::table('site_reports')->where('site_reports.site_id', '=', $value->id)
                ->where('site_reports.duration', '=', 1)
                ->whereDate('site_reports.created_at', '<', date($today))
                ->whereDate('site_reports.updated_at' , '<', date($today))
                ->delete();
        }

        $dontDuplicate = DB::table('site_reports')->where('site_reports.status', '=', 'Active')
            ->Where("site_reports.alarm", "!=", "SITE DOWN")->get()->unique('alarm');

        foreach ($dontDuplicate as $key => $value) {
            $getAlarm = DB::table('site_reports')->where('site_reports.status', '=', 'Active')
                ->where('site_reports.site_id', '=', $value->site_id)
                ->where('site_reports.alarm', '=', $value->alarm)->get();
            if (count($getAlarm) > 1) {
                $dontDeleteThisRow = DB::table('site_reports')->where('site_reports.status', '=', 'Active')
                    ->where('site_reports.id', '=', $value->id)->first();
                DB::table('site_reports')->where('site_reports.status', '=', 'Active')
                    ->where('site_reports.alarm', '=', $value->alarm)
                    ->where('site_reports.id', '!=', $value->id)->delete();

            }

        }

        return response()->json([
            "message" => "Site report table clean successfull",
        ]);
    }
    //
}