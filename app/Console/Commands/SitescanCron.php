<?php

namespace App\Console\Commands;

use App\Detail;
use App\Device;
use App\SiteReport;
use App\DownLiveSite;
use App\Site;
use App\SiteStatus;
use App\User;
use App\Power;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Response;

use Illuminate\Console\Command;

class SitescanCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitescan:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All site scanner';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Log::info("Cron is working fine!");
        $this-> scanAllSites();


    }

    public function scanAllSites()
    {

        $getSite = SiteStatus::join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('devices.device_category_id', '1')
            ->select([
                'site_status.*',
            ])->get();
        $getAnalogArray = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12',
            'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24',
        );
        $getArray = array(
            'd1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11', 'd12',
            'd13', 'd14', 'd15', 'd16', 'd17', 'd18', 'd19', 'd20', 'd21', 'd22', 'd23', 'd24',
        );
        $all_data = [];

        foreach ($getSite as $key => $siteStatus) {
            $avePower = '0';
            $presentTime = $siteStatus->updated_at->format('Y-m-d');
            $today = date('Y-m-d');

            $checkCat = Device::where("serial_number", $siteStatus->serial_number)->value('device_category_id');
            if ($checkCat != 1) {
                return;
            }
            $siteid = Site::where("serial_number", $siteStatus->serial_number)->value("id");
            $userid = $siteStatus->user_id;
            $getPower = Power::where("serial_number", $siteStatus->serial_number)
                ->whereDate("updated_at", '>=', $today . ' 00:00:00')
                ->whereDate("updated_at", '<=', $today . ' 23:59:59')->first();
            $getdownlive = DownLiveSite::where("serial_number", $siteStatus->serial_number)
                ->where("updated_at", '>=', $today)
                ->first();
            if ($getPower != null) {
                $avePower = $getPower->current_power / $getPower->countperhr;
            }
            if ($presentTime == $today) {

                if ($getdownlive) {
                    if ($siteStatus->DT_status == "0") {
                        $previousduration = $getdownlive->down_duration;
                        $presentTime = $getdownlive->updated_at;
                        $start = date($presentTime);
                        $today = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $todays = Carbon::parse($today);
                        $interval = $starts->diffInSeconds($todays);
                        $duration = ($interval) + ($previousduration);
                        $getdownlive->down_duration = $duration;
                        $formatTime = gmdate("H:i:s", $duration);
                        $getdownlive->power = round($avePower, 3);
                        $getdownlive->energy = round(($avePower * ($getdownlive->up_duration / 3600)), 3);
                        $getdownlive->down_time = $formatTime;

                    } else if ($siteStatus->DT_status == "1") {
                        $previousdownduration = $getdownlive->down_duration;
                        $formatdownTime = gmdate("H:i:s", $$previousdownduration);
                        $getdownlive->down_time = $formatdownTime;
                        $previousduration = $getdownlive->up_duration;
                        $presentTime = $getdownlive->updated_at;
                        $start = date($presentTime);
                        $today = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $todays = Carbon::parse($today);
                        $interval = $starts->diffInSeconds($todays);
                        $duration = ($interval) + ($previousduration);
                        $getdownlive->up_duration = $duration;
                        $formatTime = gmdate("H:i:s", $duration);
                        $getdownlive->up_time = $formatTime;
                        $getdownlive->energy = round(($avePower * ($duration / 3600)), 3);
                        $getdownlive->power = round($avePower, 3);

                    }
                    $getdownlive->save();
                } else {

                    if ($siteStatus->DT_status == "0") {
                        $presentTime = $siteStatus->updated_at;
                        $start = date($presentTime);
                        $today = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $todays = Carbon::parse($today);
                        $duration = $starts->diffInSeconds($todays);
                        $formatTime = gmdate("H:i:s", $duration);
                        $downlive = DownLiveSite::create([
                            'serial_number' => $siteStatus->serial_number,
                            'site_id' => $siteid,
                            'down_time' => $formatTime,
                            'down_duration' => $duration,
                            'up_time' => "00:00:00",
                            'up_duration' => "0",
                            'power' => round($avePower, 3),
                        ]);
                        $downlive->save();
                    } else if ($siteStatus->DT_status == "1") {
                        $presentTime = $siteStatus->updated_at;
                        $start = date($presentTime);
                        $today = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $todays = Carbon::parse($today);
                        $duration = $starts->diffInSeconds($todays);
                        $formatTime = gmdate("H:i:s", $duration);
                        $downlive = DownLiveSite::create([
                            'serial_number' => $siteStatus->serial_number,
                            'site_id' => $siteid,
                            'up_time' => $formatTime,
                            'up_duration' => $duration,
                            'down_time' => "00:00:00",
                            'down_duration' => "0",
                            'power' => round($avePower, 3),
                            'energy' => round(($avePower * ($duration / 3600)), 3),
                        ]);
                        $downlive->save();
                    }

                }
            } else if ($presentTime < $today) {

                if ($getdownlive) {
                    $presentTime = date('Y-m-d');

                    if ($siteStatus->DT_status == "0") {

                        $start = date($presentTime);
                        $today = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $todays = Carbon::parse($today);
                        $duration = $starts->diffInSeconds($todays);
                        $formatTime = gmdate("H:i:s", $duration);
                        $getdownlive->down_duration = $duration;
                        $getdownlive->down_time = $formatTime;
                        $getdownlive->power = round($avePower, 3);
                        $getdownlive->energy = round(($avePower * ($duration / 3600)), 3);
                    } else if ($siteStatus->DT_status == "1") {
                        //  dd($getdownlive);
                        $previousdownduration = $getdownlive->down_duration;
                        $formatdownTime = gmdate("H:i:s", $$previousdownduration);
                        $getdownlive->down_time = $formatdownTime;
                        $start = date($presentTime);
                        $today = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $todays = Carbon::parse($today);
                        $duration = $starts->diffInSeconds($todays);
                        $formatTime = gmdate("H:i:s", $duration);
                        $getdownlive->up_duration = $duration;
                        $getdownlive->up_time = $formatTime;
                        $getdownlive->power = round($avePower, 3);
                        $getdownlive->energy = round(($avePower * ($duration / 3600)), 3);
                    }
                    $getdownlive->save();
                } else {
                    //get today time from start of today
                    $presentTime = date('Y-m-d');
                    if ($siteStatus->DT_status == "0") {
                        $start = date($presentTime);
                        $today = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $todays = Carbon::parse($today);
                        $duration = $starts->diffInSeconds($todays);
                        $formatTime = gmdate("H:i:s", $duration);
                        // $starts->diff($todays)->format('%hh:%im:%ss');
                        // dd(($interval));
                        $downlive = DownLiveSite::create([
                            'serial_number' => $siteStatus->serial_number,
                            'site_id' => $siteid,
                            'down_time' => $formatTime,
                            'down_duration' => $duration,
                            'up_time' => "00:00:00",
                            'up_duration' => "0",
                            'power' => round($avePower, 3),
                        ]);
                        $downlive->save();
                    } else if ($siteStatus->DT_status == "1") {
                        $presentTime = $siteStatus->updated_at;
                        $start = date($presentTime);
                        $today = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $todays = Carbon::parse($today);
                        $duration = $starts->diffInSeconds($todays);
                        $formatTime = gmdate("H:i:s", $duration);
                        $downlive = DownLiveSite::create([
                            'serial_number' => $siteStatus->serial_number,
                            'site_id' => $siteid,
                            'up_time' => $formatTime,
                            'up_duration' => $duration,
                            'down_time' => "00:00:00",
                            'down_duration' => "0",
                            'power' => round($avePower, 3),
                            'energy' => round(($avePower * ($duration / 3600)), 3),
                        ]);
                        $downlive->save();
                    }

                }

            }

            $previousTime = $siteStatus->updated_at;
            $start = date($previousTime);
            $end = date('Y-m-d H:i:s');
            $start = Carbon::parse($start);
            $end = Carbon::parse($end);
            $minutes = $end->diffInRealMinutes($start);
            //10 minutus time interval for down site scan
            $serial_number = $siteStatus->serial_number;
            $siteId = Site::where('serial_number', $serial_number)->value('id');
            $deviceId = Device::where('serial_number', $serial_number)->value('id');
            if ($minutes > 10) {
                $siteStatus = SiteStatus::where('serial_number', $serial_number)->first();
                if ($siteStatus->DT_status != "N") {
                    $all_data["serial_number"] = $serial_number;
                    $checkDetail = Detail::whereSerialNumber($serial_number)->first();
                    $upriser = Site::where('serial_number', $serial_number)->value('uprisers');
                    $channel = $upriser * 3;
                    for ($j = 0; $j < 24; $j++) {
                        $d_counts = $j + 1;
                        if ($d_counts <= $channel) {
                            $all_data["d$d_counts"] = '0';
                        } else {
                            $all_data["d$d_counts"] = 'R';

                        }
                        $d_counts = 0;
                    }
                    for ($k = 0; $k < 24; $k++) {
                        $d_counts = $k + 1;
                        if ($checkDetail[$getAnalogArray[$k]] != 'R') {
                            $all_data["a$d_counts"] = '0';
                        }
                        $d_counts = 0;
                    }
                    DB::table('details')
                        ->where('serial_number', $serial_number)
                        ->update($all_data);
                    $ctrl_datafb = [];
                    for ($fb = 0; $fb < 8; $fb++) {
                        $fb_counts = $fb + 1;
                        $ctrl_datafb["z$fb_counts"] = '0';
                        $fb_counts = 0;
                    }
                    $ctrl_datafb["serial_number"] = $serial_number;
                    $ctrl_datafb["updated_at"] = Carbon::now();
                    DB::table('ctrl_feedback')
                        ->where('serial_number', $serial_number)
                        ->update($ctrl_datafb);
                    if ($siteStatus->DT_status == "1") {
                        $getdownlive = DownLiveSite::where("serial_number", $siteStatus->serial_number)
                            ->where("updated_at", '>=', $today)
                            ->first();
                        $downDuration = $getdownlive->down_duration;
                        $upDuration = $getdownlive->up_duration;
                        $downInterval = $downDuration - 600;
                        $upInterval = $upDuration - 600;
                        $formatUpTime = gmdate("H:i:s", $upInterval);
                        $formatDownTime = gmdate("H:i:s", $downInterval);
                        $dbEnergy = $getdownlive->energy;
                        $getEnergy = $dbEnergy - round(($avePower * (600 / 3600)), 3);
                        $getdownlive->up_time = $formatUpTime;
                        $getdownlive->down_time = $formatDownTime;
                        $getdownlive->energy = $getEnergy;
                        $getdownlive->save();
                    }

                    $siteStatus->DT_status = "0";
                    $siteStatus->alarm_status = "1";
                    if ($siteStatus->Up_A !== 'N') {
                        $siteStatus->Up_A = "2";
                        $siteStatus->Up_AStatus = "0";
                    } else {
                        $siteStatus->Up_AStatus = "N";
                    }
                    if ($siteStatus->Up_B !== 'N') {
                        $siteStatus->Up_B = "2";
                        $siteStatus->Up_BStatus = "0";
                    } else {
                        $siteStatus->Up_BStatus = "N";
                    }

                    if ($siteStatus->Up_C !== 'N') {
                        $siteStatus->Up_C = "2";
                        $siteStatus->Up_CStatus = "0";
                    } else {
                        $siteStatus->Up_CStatus = "N";
                    }
                    if ($siteStatus->Up_D !== 'N') {
                        $siteStatus->Up_D = "2";
                        $siteStatus->Up_DStatus = "0";
                    } else {
                        $siteStatus->Up_DStatus = "N";
                    }

                    $siteStatus->updated_at = date('Y-m-d H:i:s');
                    $siteStatus->save();
                }
                $countsall = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', "SITE DOWN")->count() > 0;
                if ($countsall) {
                    $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', "SITE DOWN")->first();
                    $previousTime = $reports->created_at;
                    $start = date($previousTime);
                    $end = date('Y-m-d H:i:s');
                    $starts = Carbon::parse($start);
                    $ends = Carbon::parse($end);
                    $interval = $starts->diff($ends);
                    $duration = $interval->format('%dd:%hh:%im:%ss');
                    $reports->duration = $duration;
                    $reports->save();
                } else {
                    $status = siteStatus::where('serial_number', $serial_number)->value("DT_status");
                    if ($status != "N") {
                        $alarmTime = Carbon::now()->subMinute(10);
                        \Log::info($alarmTime);
                        $storeAlarmReport = SiteReport::create([
                            'alarm' => "SITE DOWN",
                            'site_id' => $siteId,
                            'device_id' => $deviceId,
                            'duration' => '1',
                            'status' => 'Active',
                            'stop_display' => 0,
                            'stop_message' => 0,
                            'total_responder' => 0,
                            'created_at' => $alarmTime,
                            'updated_at' => $alarmTime,
                        ]);
                        $storeAlarmReport->save();
                    }
                }

            }

            $countsall = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', "SITE DOWN")->count() > 0;
            if ($countsall) {
                $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', "SITE DOWN")->first();
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
        return Response::json("Scan complete");

    }


}