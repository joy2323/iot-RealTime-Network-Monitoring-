<?php

namespace App\Http\Controllers;

use App\Alarmstatus;
use App\Alarmtracker;
use App\AnalogSetting;
use App\AnalogValue;
use App\ChannelConfig;
use App\Control;
use App\CtrlFeedback;
use App\Detail;
use App\Device;
use App\Helpers\HelperClass;
use App\Label;
use App\Location;
use App\Power;
use App\Site;
use App\SiteReport;
use App\SiteStatus;
use Carbon\Carbon;
use DB;
use App\PushNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;

class DeviceApiController extends Controller
{

    public function getDetails(Request $request)
    {
        $serialNumber = $request->serial_number;
        $checkSite = Site::where("serial_number", $serialNumber)->first();
        if ($checkSite == null) {
            \Log::info($request->serial_number . " Do not exist in DB");
            return response()->json([
                'message' => 'Do not exist in DB',
            ]);

        }
        $deviceId = Device::where('serial_number', $serialNumber)->value('device_category_id');

        if ($deviceId == 1) {
            return $this->getDTDetails($request);
        } else if ($deviceId == 2) {
            return $this->getHVDetails($request);
        } else {
            return response()->json([
                'message' => 'Device not in invalid category',
                'status' => '400',
            ]);
        }

    }

    public function getHVDetails(Request $request)
    {
        $getDArray = array(
            'd1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11',
            'd12', 'd13', 'd14', 'd15', 'd16', 'd17', 'd18', 'd19', 'd20', 'd21', 'd22', 'd23', 'd24',
        );
        $getAArray = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12',
            'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24',
        );
        $rawdata = $request->all();

        $checkSite = Site::where("serial_number", $rawdata["serial_number"])->first();
        $all_data = [];
        $all_data["serial_number"] = $rawdata["serial_number"];
        for ($d = 1; $d < 25; $d++) {
            $all_data["d$d"] = $rawdata["d$d"];
        }

        for ($a = 1; $a < 25; $a++) {
            $all_data["a$a"] = $rawdata["a$a"];
        }

        $ctrl_datafb = [];
        $ctrl_datafb["serial_number"] = $rawdata["serial_number"];

        for ($z = 1; $z < 7; $z++) {
            $ctrl_datafb["z$z"] = $rawdata["z$z"];
        }
        $checkctrlfb = CtrlFeedback::where("serial_number", $request->serial_number)->first();
        if ($checkctrlfb == null) {
            $checkctrlfb = CtrlFeedback::create($ctrl_datafb);
            $checkctrlfb->save();
        } else {
            if ($checkctrlfb->serial_number = $request->serial_number) {
                DB::table('ctrl_feedback')
                    ->where('serial_number', $request->serial_number)
                    ->update($ctrl_datafb);
            }
        }

        $checkctrl = Control::where("site_id", $checkSite->id)->first();

        if ($checkctrl == null) {
            $channelNum = $checkSite->uprisers;
            $ctrl_data = [];
            $ctrl_data['site_id'] = $checkSite->id;
            $controlEN = $checkSite->ctrl_enable;
            if ($controlEN == '1') {

                for ($i = 1; $i <= 8; $i++) {
                    if ($i <= $channelNum + 1) {
                        $ctrl_data["c$i"] = '0';
                    } else {
                        $ctrl_data["c$i"] = 'N';
                    }

                }
            } else if ($controlEN == '2') {

                for ($i = 1; $i <= 8; $i++) {
                    if ($i == 1) {
                        $ctrl_data["c$i"] = '0';
                    } else {
                        $ctrl_data["c$i"] = 'N';
                    }

                }

            }
            $checkctrl = Control::create($ctrl_data);
            $checkctrl->save();
        }

        $channelConfig = ChannelConfig::where("serial_number", $request->serial_number)->first();
        $analogValue = AnalogValue::where("serial_number", $request->serial_number)->first();
        for ($a = 0; $a < 13; $a++) {
            $j = $a + 1;
            if ($channelConfig[$getAArray[$a]] == "0") {
                $all_data["a$j"] = "R";
            } else {
                //not tested
                if ((is_numeric($analogValue["a$j"])) && (is_numeric($all_data["a$j"]))) {
                    if ($all_data["a$j"] < 1000) {
                        $all_data["a$j"] = $all_data["a$j"] * $analogValue["a$j"];
                    } else {
                        $all_data["a$j"] = "0";
                    }

                }

            }
        }

        for ($d = 0; $d < 13; $d++) {
            $i = $d + 1;
            if ($channelConfig[$getDArray[$d]] == "0") {
                $all_data["d$i"] = "R";
            }

        }

        $detail = new Detail();
        $checkDetail = Detail::whereSerialNumber($request->serial_number)->first();
        if ($checkDetail == null) {
            $details = Detail::create($all_data);
            $details->save();
        } else {
            if ($checkDetail->serial_number = $request->serial_number) {
                $all_data["updated_at"] = Carbon::now();
                DB::table('details')
                    ->where('serial_number', $request->serial_number)
                    ->update($all_data);
            }
        }

        $data = [];
        $data["serial_number"] = $rawdata["serial_number"];
        $data["lat"] = $rawdata["lat"];
        $data["long"] = $rawdata["long"];

        $location = new Location();
        $checkLocation = Location::whereSerialNumber($request->serial_number)->first();

        if ($checkLocation == null) {
            $location = Location::create($data);
            $location->save();
        } else {
            if ($checkLocation->serial_number = $request->serial_number) {
                DB::table('locations')
                    ->where('serial_number', $request->serial_number)
                    ->update($data);
            }
        }
        return $this->storeStationReport($all_data);
    }

    public function storeStationReport($request)
    {
        $this->request = $request;
        $siteId = Site::where('serial_number', $request['serial_number'])->value('id');
        $feeders = Site::where('serial_number', $request['serial_number'])->value('uprisers');
        $collection = $this->digitalData($this->request);
        $getDigitalArray = $collection['getArray'];
        $this->getFeederAlarm($request, $getDigitalArray, $siteId, $feeders);
        $storeControl = Control::where('site_id', $siteId)->select(['c1', 'c2', 'c3', 'c4', 'c5'])->first();
        return response()->json($storeControl);
    }

    public function getFeederAlarm($request, $getArray, $siteId, $feeders)
    {
        $deviceId = Device::where('serial_number', $request['serial_number'])->value('id');
        if ($this->ifDownStation($request, $getArray, $feeders)) {
            $siteStatus = SiteStatus::where('serial_number', $request['serial_number'])->first();
            if ($siteStatus) {
                $siteStatus->DT_status = '0';
                $siteStatus->alarm_status = '1';
            } else {
                $siteStatus = SiteStatus::create([
                    'serial_number' => $request['serial_number'],
                    'DT_status' => '0',
                    'alarm_status' => '1',
                ]);
            }

            $siteStatus->save();
            $getLabel = 'STATION';
            $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
            if ($reports) {
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
                $this->saveFeederAlarm($getLabel, $siteId, $deviceId);
            }
        } else {

            $siteStatus = SiteStatus::where('serial_number', $request['serial_number'])->first();
            if ($siteStatus) {
                $siteStatus->DT_status = '1';
                $siteStatus->alarm_status = '0';
            } else {
                $siteStatus = SiteStatus::create([
                    'serial_number' => $request['serial_number'],
                    'DT_status' => '1',
                    'alarm_status' => '0',
                ]);
            }

            $siteStatus->save();
            $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', 'STATION')->first();
            if ($reports) {
                $previousTime = $reports->created_at;
                $start = date($previousTime);
                $end = date('Y-m-d H:i:s');
                $starts = Carbon::parse($start);
                $ends = Carbon::parse($end);
                $interval = $starts->diff($ends);
                $duration = $interval->format('%dd:%hh:%im:%ss');
                $reports->duration = $duration;
                $reports->status = "Resolved";
                $reports->save();
            }

            for ($i = 0; $i < $feeders; $i++) {
                # code...
                $getLabel = Label::where('serial_number', $request['serial_number'])->value($getArray[$i]);
                $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
                if ($reports) {
                    $previousTime = $reports->created_at;
                    $start = date($previousTime);
                    $end = date('Y-m-d H:i:s');
                    $starts = Carbon::parse($start);
                    $ends = Carbon::parse($end);
                    $interval = $starts->diff($ends);
                    $duration = $interval->format('%dd:%hh:%im:%ss');
                    if ($request[$getArray[$i]] == '0') {
                        $reports->duration = $duration;
                        $reports->save();
                    } else if ($request[$getArray[$i]] == '1') {
                        $reports->duration = $duration;
                        $reports->status = "Resolved";
                        $reports->save();
                    }
                } else {

                    if ($request[$getArray[$i]] == '0') {
                        $this->saveFeederAlarm($getLabel, $siteId, $deviceId);
                    }
                }
            }
        }
    }

    public function ifDownStation($request, $getArray, $feeders)
    {
        $stationDown = true;
        for ($i = 0; $i < $feeders; $i++) {
            # code...
            if ($request[$getArray[$i]] == '1') {
                $stationDown = false;
                break;
            }
        }
        return $stationDown;
    }

    public function saveFeederAlarm($getLabel, $siteId, $deviceId)
    {
        $storeAlarmReport = SiteReport::create([
            'alarm' => $getLabel,
            'site_id' => $siteId,
            'device_id' => $deviceId,
            'duration' => '1',
            'status' => 'Active',
            'stop_display' => 0,
            'stop_message' => 0,
            'total_responder' => 0,
        ]);
        $storeAlarmReport->save();
        $helperClass = new HelperClass();
        $getNotification = $helperClass->sendNotification($siteId, $getLabel);

    }

    public function getDTDetails(Request $request)
    {
        $getDArray = array(
            'd1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11',
            'd12', 'd13', 'd14', 'd15', 'd16', 'd17', 'd18', 'd19', 'd20', 'd21', 'd22', 'd23', 'd24',
        );
        $getAArray = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12',
            'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24',
        );
        $rawdata = $request->all();
        $checkSite = Site::where("serial_number", $rawdata["serial_number"])->first();
        $control = Control::where("site_id", $checkSite->id)->first();
        $all_data = [];
        $all_data["serial_number"] = $rawdata["serial_number"];
        $d_counts = 0;

        $checkCB = false;
        $checkCtrl = false;
        $d_datae = explode('>', $rawdata["d"]);
        foreach ($d_datae as $d_data) {
            $d_counts++;
            $all_data["d$d_counts"] = $d_data;
            if ($d_data == '1') {
                $checkCB = true;
            }

        }

        $a_counts = 0;
        $a_datae = explode('>', $rawdata["a"]);
        foreach ($a_datae as $a_data) {
            $a_counts++;
            $all_data["a$a_counts"] = $a_data;
        }

        $z_counts = 0;
        $ctrl_datafb = [];
        $ctrl_datafb["serial_number"] = $rawdata["serial_number"];
        $z_datae = explode('>', $rawdata["z"]);
        foreach ($z_datae as $z_data) {
            $z_counts++;
            $ctrl_datafb["z$z_counts"] = $z_data;
            if ($z_data == '1') {
                $checkCtrl = true;

            }

        }

        //DT control relation codes
        if ($request->serial_number == 'ENUENGBU550OUK552') {

            if ($checkCB && $ctrl_datafb["z2"] == '1') {
                $ctrl_datafb["z1"] = '1';

            } else if ($checkCB && $ctrl_datafb["z2"] == '0') {
                $ctrl_datafb["z1"] = '1';
                $checkctrl->ctrl_chk = 0;
            } else {
                $ctrl_datafb["z1"] = '0';
            }
            for ($i = 2; $i <= 8; $i++) {
                $ctrl_datafb["z$i"] = '0';
            };
        }
        $ctrl_datafb["updated_at"] = Carbon::now();
        $checkctrlfb = CtrlFeedback::where("serial_number", $request->serial_number)->first();
        if ($checkctrlfb == null) {
            $checkctrlfb = CtrlFeedback::create($ctrl_datafb);
            $checkctrlfb->save();
        } else {
            if ($checkctrlfb->serial_number = $request->serial_number) {
                DB::table('ctrl_feedback')
                    ->where('serial_number', $request->serial_number)
                    ->update($ctrl_datafb);
            }
        }

        if ($checkSite->ctrl_enable > 0) {

            if ($control == null) {
                if ($checkCtrl == true) {
                    $ctrl_data["ctrl_chk"] = 0;
                }
                $channelNum = $checkSite->uprisers;
                $ctrl_data = [];
                $ctrl_data['site_id'] = $checkSite->id;
                $controlEN = $checkSite->ctrl_enable;
                if ($controlEN == '1') {

                    for ($i = 1; $i <= 8; $i++) {
                        if ($i <= $channelNum + 1) {
                            $ctrl_data["c$i"] = '0';
                        } else {
                            $ctrl_data["c$i"] = 'N';
                        }

                    }
                } else if ($controlEN == '2') {

                    for ($i = 1; $i <= 8; $i++) {
                        if ($i == 1) {
                            $ctrl_data["c$i"] = '0';
                        } else {
                            $ctrl_data["c$i"] = 'N';
                        }

                    }

                }
                $checkctrl = Control::create($ctrl_data);
                $checkctrl->save();
            } else {

                // $control = Control::where('controls.site_id', $checkSite->id)->first();
                //check if control cCB tripped
                $sitestatus = SiteStatus::where('site_status.serial_number', $checkSite->serial_number)->first();
                $feedback = CtrlFeedback::where('serial_number', $checkSite->serial_number)->first();
                $siteId = Site::where('serial_number', $checkSite->serial_number)->value('id');
                if ($checkCtrl == true) {
                    $control->ctrl_chk = 0;
                }
                if (($control->c1 == '1' && $feedback->z1 == '0' && $control->ctrl_chk == 0)) {
                    $control->c1 = '2';
                    // send tripper CB email
                    $CBName = "Main";
                    $status = 1;
                    $this->CBTripAlarm($siteId, $CBName, $status);

                } else if ($feedback->z1 == '1') {
                    $control->c1 = '1';
                    $CBName = "Main";
                    $status = 0;
                    $this->CBTripAlarm($siteId, $CBName, $status);
                } else if ($control->c1 == '2') {
                    $CBName = "Main";
                    $status = 1;
                    $this->CBTripAlarm($siteId, $CBName, $status);
                }
                if ($checkSite->ctrl_enable == '1') {
                    if ($control->c2 == '1' && $feedback->z2 == '0' && $feedback->z1 == '1' && $control->ctrl_chk == 0) {
                        $control->c2 = '2';
                        $CBName = "UP1";
                        $status = 1;
                        $this->CBTripAlarm($siteId, $CBName, $status);

                    } else if ($feedback->z2 == '1') {
                        $CBName = "UP1";
                        $status = 0;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    } else if ($control->c2 == '2') {
                        $CBName = "UP1";
                        $status = 1;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    }

                    if ($control->c3 == '1' && $feedback->z3 == '0' && $feedback->z1 == '1' && $control->ctrl_chk == 0) {
                        $control->c3 = '2';
                        $CBName = "UP2";
                        $status = 1;
                        $this->CBTripAlarm($siteId, $CBName, $status);

                    } else if ($feedback->z3 == '1') {
                        $CBName = "UP2";
                        $status = 0;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    } else if ($control->c3 == '2') {
                        $CBName = "UP2";
                        $status = 1;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    }

                    if ($control->c4 == '1' && $feedback->z4 == '0' && $feedback->z1 == '1' && $control->ctrl_chk == 0) {
                        $control->c4 = '2';
                        $CBName = "UP3";
                        $status = 1;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    } else if ($feedback->z4 == '1') {
                        $CBName = "UP3";
                        $status = 0;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    } else if ($control->c4 == '2') {
                        $CBName = "UP3";
                        $status = 1;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    }

                    if ($control->c5 == '1' && $feedback->z5 == '0' && $feedback->z1 == '1' && $control->ctrl_chk == 0) {
                        $control->c5 = '2';
                        $CBName = "UP4";
                        $status = 1;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    } else if ($feedback->z5 == '1') {
                        $CBName = "UP4";
                        $status = 0;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    } else if ($control->c5 == '2') {
                        $CBName = "UP4";
                        $status = 1;
                        $this->CBTripAlarm($siteId, $CBName, $status);
                    }

                } else {
                    $control->c2 = 'N';
                    $control->c3 = 'N';
                    $control->c4 = 'N';
                    $control->c5 = 'N';
                }
                $control->save();

            }
        }

        // checkchannel configuration
        $channelConfig = ChannelConfig::where("serial_number", $request->serial_number)->first();
        $analogValue = AnalogValue::where("serial_number", $request->serial_number)->first();
        for ($a = 0; $a < 13; $a++) {
            $j = $a + 1;
            if ($channelConfig[$getAArray[$a]] == "0") {
                $all_data["a$j"] = "R";
            } else {
                //not tested
                if ((is_numeric($analogValue["a$j"])) && (is_numeric($all_data["a$j"]))) {
                    if ($all_data["a$j"] < 1000) {
                        $all_data["a$j"] = $all_data["a$j"] * $analogValue["a$j"];
                    } else {
                        $all_data["a$j"] = "0";
                    }

                }

            }
        }

        for ($d = 0; $d < 13; $d++) {
            $i = $d + 1;
            if ($channelConfig[$getDArray[$d]] == "0") {
                $all_data["d$i"] = "R";
            }

        }

        $siteStatus = SiteStatus::where('serial_number', $request->serial_number)->first();
        $siteupriser = Site::where('serial_number', $request->serial_number)->value('uprisers');

        if ((int) $siteupriser == 1) {
            $siteStatus->Up_C = "N";
            $siteStatus->Up_B = "N";
            $siteStatus->Up_D = "N";
        } else if ((int) $siteupriser == 2) {
            $siteStatus->Up_C = "N";
            $siteStatus->Up_D = "N";
        } else if ((int) $siteupriser == 3) {
            $siteStatus->Up_D = "N";
        }
        $siteStatus->save();

        $detail = new Detail();
        $checkDetail = Detail::whereSerialNumber($request->serial_number)->first();
        if ($checkDetail == null) {
            $details = Detail::create($all_data);
            $details->save();
        } else {
            if ($checkDetail->serial_number = $request->serial_number) {
                $all_data["updated_at"] = Carbon::now();
                DB::table('details')
                    ->where('serial_number', $request->serial_number)
                    ->update($all_data);
            }
        }

        //process power data
        $this->powerDetails($request, $all_data);

        $data = [];
        $data["serial_number"] = $rawdata["serial_number"];
        $location_counts = 0;
        $location_datae = explode('>', $rawdata["l"]);
        foreach ($location_datae as $location_data) {
            $location_counts++;
            if ($location_counts == 1) {
                $data["lat"] = $location_data;
            }

            if ($location_counts == 2) {
                $data["long"] = $location_data;
            }
        }
        $location = new Location();
        $checkLocation = Location::whereSerialNumber($request->serial_number)->first();
        if ($checkLocation->lat != $data["lat"] && $checkLocation->long != $data["long"] && $checkLocation->is_set == 0) {
            $data["is_set"] = 1;
            DB::table('locations')
                ->where('serial_number', $request->serial_number)
                ->update($data);

        }
        return $this->storeSiteReport($all_data);
    }

    // process site alarm from incoming data
    public function storeSiteReport($request)
    {
        $this->request = $request;
        $siteId = Site::where('serial_number', $request['serial_number'])->value('id');
        $deviceId = Device::where('serial_number', $request['serial_number'])->value('id');
        $collectionAnalog = $this->analogData($this->request);
        $getAnalogAlarm = $collectionAnalog['getAnalogArray'];
        
        //get alarms on analog channels
        $this->getAnalogAlarm($request, $getAnalogAlarm);
        $collection = $this->digitalData($this->request);
        $totalUprisers = $collection['totalUpriser'];
        $getDigitalArray = $collection['getArray'];
        
        //get alarms on digitalchannel
        $this->getDigitalAlarm($totalUprisers, $request, $getDigitalArray);
        //Site down updates
        $this->ifDownSite($siteId);

        //update site alarm status
        $this->updateSiteStatus($request);

        $storeControl = Control::where('site_id', $siteId)->select(['c1', 'c2', 'c3', 'c4', 'c5'])->first();

        return response()->json($storeControl);
    }

    public function ifDownSite($site_Id)
    {
        $countsall = SiteReport::where('site_id', $site_Id)->where('status', 'Active')->where('alarm', "SITE DOWN")->count() > 0;
        if ($countsall) {
            $reports = SiteReport::where('site_id', $site_Id)->where('status', 'Active')->where('alarm', "SITE DOWN")->first();
            $previousTime = $reports->created_at;
            $start = date($previousTime);
            $end = date('Y-m-d H:i:s');
            $start = Carbon::parse($start);
            $end = Carbon::parse($end);
            $interval = $start->diff($end);
            $minutes = $end->diffInRealMinutes($start);

            //if down site is only active less than 15 minuts delete the report
            if ($minutes <= 15) {
                $reports->delete();
            } else {

                $data = [];
                $duration = $interval->format('%dd:%hh:%im:%ss');
                $data["duration"] = $duration;
                $data["status"] = "Resolved";
                DB::table('site_reports')
                    ->where('site_id', $site_Id)
                    ->update($data);
            }

        }
    }

    public function getDigitalAlarm($totalUprisers, $request, $getArray)
    {
        $siteId = Site::where('serial_number', $request['serial_number'])->value('id');
        if ($totalUprisers == 1) {
            $this->UpriserAlarm($request, $getArray, $siteId, 0);
        } else if ($totalUprisers == 2) {
            $this->UpriserAlarm($request, $getArray, $siteId, 0);
            $this->UpriserAlarm($request, $getArray, $siteId, 3);
        } else if ($totalUprisers == 3) {
            $this->UpriserAlarm($request, $getArray, $siteId, 0);
            $this->UpriserAlarm($request, $getArray, $siteId, 3);
            $this->UpriserAlarm($request, $getArray, $siteId, 6);
        } else if ($totalUprisers == 4) {
            $this->UpriserAlarm($request, $getArray, $siteId, 0);
            $this->UpriserAlarm($request, $getArray, $siteId, 3);
            $this->UpriserAlarm($request, $getArray, $siteId, 6);
            $this->UpriserAlarm($request, $getArray, $siteId, 9);
        }
    }

    public function digitalData($request)
    {
        $totalUprisers = Site::where('serial_number', $request['serial_number'])->value('uprisers');
        $serialNumber = $request['serial_number'];
        $getArray = array(
            'd1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11', 'd12',
            'd13', 'd14', 'd15', 'd16', 'd17', 'd18', 'd19', 'd20', 'd21', 'd22', 'd23', 'd24',
        );
        return (["totalUpriser" => $totalUprisers, "getArray" => $getArray]);
    }

    public function UpriserAlarm($request, $getArray, $siteId, $i)
    {
        // $getNotification = "No Alarm Yet";
        $isAll = false;
        $deviceId = Device::where('serial_number', $request['serial_number'])->value('id');
        $getLabel = Label::where('serial_number', $request['serial_number'])->value($getArray[$i]);
        $getLabel = str_replace(" Red", "", $getLabel);
        $countsall = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->count() > 0;
        if ($countsall) {
            $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
            $previousTime = $reports->created_at;
            $start = date($previousTime);
            $end = date('Y-m-d H:i:s');
            $starts = Carbon::parse($start);
            $ends = Carbon::parse($end);
            $interval = $starts->diff($ends);
            $duration = $interval->format('%dd:%hh:%im:%ss');
            if ($request[$getArray[$i]] == '0' && $request[$getArray[$i + 1]] == '0' && $request[$getArray[$i + 2]] == '0') {
                $isAll = true;
                $reports->duration = $duration;
                $reports->save();
            } else if ($request[$getArray[$i]] == '1' || $request[$getArray[$i + 1]] == '1' || $request[$getArray[$i + 2]] == '1') {
                $stopduration = $starts->diffInMinutes($ends);
                $getAlarmCount = DB::table('alarmtracker')->where('site_id', $siteId)
                    ->where('alarm',$getLabel)->first();
				
                if ($stopduration <= 1) { 
                    $reports->delete();
                    if($getAlarmCount){
						DB::table('alarmtracker')->where('site_id', '=', $siteId)->delete();
					}
                } else {
                    $reports->duration = $duration;
                    $reports->stop_display = $stopduration;
                    $reports->status = "Resolved";
                    $reports->save();
                }

            }
        } else {

            if ($request[$getArray[$i]] == '0' && $request[$getArray[$i + 1]] == '0' && $request[$getArray[$i + 2]] == '0') {
                $getLabelall = Label::where('serial_number', $request['serial_number'])->value($getArray[$i]);
                $getLabelall = str_replace(" Red", "", $getLabelall);
                $isAll = true;
                for ($j = $i; $j < $i + 3; $j++) {
                    $channel = $getArray[$j];
                    $getLabel = Label::where('serial_number', $request['serial_number'])->value($channel);
                    $counts = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->count() > 0;
                    if ($counts) {
                        $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
                        $previousTime = $reports->created_at;
                        $start = date($previousTime);
                        $end = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $ends = Carbon::parse($end);
                        $interval = $starts->diff($ends);
                        $duration = $interval->format('%dd:%hh:%im:%ss');

                        $stopduration = $starts->diffInMinutes($ends);
                        $getAlarmCount = DB::table('alarmtracker')->where('site_id', '=', $siteId)
                            ->where('alarm', '=', $getLabel)->first();
					
                        if ($stopduration <= 1) {
                            $reports->delete();
                          if($getAlarmCount){
							     DB::table('alarmtracker')->where('site_id', '=', $siteId)->delete();
							}
                        } else {
                            $reports->duration = $duration;
                            $reports->stop_display = $stopduration;
                            $reports->status = "Resolved";
                            $reports->save();
                        }
                    }
                }
                $this->saveAlarm($getLabelall, $siteId, $deviceId);
            }
        }

        if (!$isAll) {
            for ($j = $i; $j < $i + 3; $j++) {
                $channel = $getArray[$j];
                $getLabel = Label::where('serial_number', $request['serial_number'])->value($channel);
                $counts = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->count() > 0;
                // dd($counts);
                if ($counts) {
                    $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
                    $previousTime = $reports->created_at;
                    $start = date($previousTime);
                    $end = date('Y-m-d H:i:s');
                    $starts = Carbon::parse($start);
                    $ends = Carbon::parse($end);
                    $interval = $starts->diff($ends);
                    $duration = $interval->format('%dd:%hh:%im:%ss');

                    if ($request[$channel] == '0') {
                        $reports->duration = $duration;
                        $reports->save();
                        $siteStatus = SiteStatus::where('serial_number', $request['serial_number'])->first();

                    } else {
                        $stopduration = $starts->diffInMinutes($ends);
                        $getAlarmCount = DB::table('alarmtracker')->where('site_id', '=', $siteId)
                            ->where('alarm', '=', $getLabel)->first();
                        if ($stopduration <= 1) {
                            $reports->delete();
							if($getAlarmCount){
								 
							     DB::table('alarmtracker')->where('site_id', '=', $siteId)->delete();
							}
                          
                        } else {
                            $reports->duration = $duration;
                            $reports->stop_display = $stopduration;
                            $reports->status = "Resolved";
                            $reports->save();
                        }
                    }
                } else {

                    if ($request[$channel] == '0') {
                        $this->saveAlarm($getLabel, $siteId, $deviceId);
                    }
                }
            }
        }
        // return $getNotification;
    }

    public function CBTripAlarm($siteId, $CBName, $status)
    {

        $siteShort = Site::where('id', $siteId)->first();
        $siteShortName = $siteShort->site_number;
        $getLabel = $siteShortName . " " . $CBName . " CB Tripped";
        $countsall = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->count() > 0;

        if ($countsall) {
            $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
            $previousTime = $reports->created_at;
            $start = date($previousTime);
            $end = date('Y-m-d H:i:s');
            $starts = Carbon::parse($start);
            $ends = Carbon::parse($end);
            $interval = $starts->diff($ends);
            $duration = $interval->format('%dd:%hh:%im:%ss');

            if ($status == 1) {
                $reports->duration = $duration;
                $reports->save();

            } else {
                $reports->duration = $duration;
                $reports->status = "Resolved";
                $reports->save();

            }
        } else {
            if ($status == 1) {
                // dd("Alarm tripp");
                $deviceId = Device::where('serial_number', $siteShort->serial_number)->value('id');
                $storeAlarmReport = SiteReport::create([
                    'alarm' => $getLabel,
                    'site_id' => $siteId,
                    'device_id' => $deviceId,
                    'duration' => '1',
                    'status' => 'Active',
                    'stop_display' => 0,
                    'stop_message' => 0,
                    'total_responder' => 0,
                ]);
                $storeAlarmReport->save();

            }
        }

        // return $getNotification;
    }

    public function analogData($request)
    {
        $getAnalogArray = array(
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12',
            'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24',
        );
        return (["getAnalogArray" => $getAnalogArray]);
    }
    public function getAnalogAlarm($request, $getArray)
    {
        $getLabel = "";
        $analogContent = AnalogSetting::where('serial_number', $request['serial_number'])->value('content');
        $siteId = Site::where('serial_number', $request['serial_number'])->value('id');
        $deviceId = Device::where('serial_number', $request['serial_number'])->value('id');
        $analogset = json_decode($analogContent);
        for ($j = 0; $j < 10; $j++) {
            $getLabel = Label::where('serial_number', $request['serial_number'])->value($getArray[$j]);
            $countsall = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->count() > 0;
            $this->getAnalogStatus($request, $getArray, $getLabel, $deviceId, $analogset, $j, $countsall, $siteId);
        }
    }

    public function getAnalogStatus($request, $getArray, $getLabel, $deviceId, $analogset, $j, $countsall, $siteId)
    {
        if ($getLabel !== $getArray[$j]) {
            $value = $request[$getArray[$j]];
            $settingsmin = (array) $analogset[$j]->settings;
            $alarmlabel = $analogset[$j]->alarm_name;
            $maxValue = $analogset[$j]->max_input;
            if ($j === 0) {
                $keyvalue = array_search('true', $settingsmin);
                if ($keyvalue) {
                    if ($countsall) {
                        $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
                        $previousTime = $reports->created_at;
                        $start = date($previousTime);
                        $end = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $ends = Carbon::parse($end);
                        $interval = $starts->diff($ends);
                        $duration = $interval->format('%dd:%hh:%im:%ss');

                        if ($value >= $maxValue) {
                            $reports->duration = $duration;
                            $reports->save();
                            $siteStatus = SiteStatus::where('serial_number', $request['serial_number'])->first();
                            // if ($siteStatus->DT_status == "0") {
                            //     $helperClass = new HelperClass();
                            //     $getNotification = $helperClass->sendNotification($siteId, $getLabel);
                            // }
                        } else {
                            $reports->duration = $duration;
                            $reports->status = "Resolved";
                            $reports->save();
                        }
                    } else {
                        if ($value >= $maxValue) {
                            $this->saveAlarm($getLabel, $siteId, $deviceId);
                        }
                    }
                }
            }
            if ($j === 1) {
                $keyvalue = array_search('true', $settingsmin);
                if ($keyvalue) {
                    if ($countsall) {
                        $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
                        $previousTime = $reports->created_at;
                        $start = date($previousTime);
                        $end = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $ends = Carbon::parse($end);
                        $interval = $starts->diff($ends);
                        $duration = $interval->format('%dd:%hh:%im:%ss');

                        if ($value >= $maxValue) {
                            $reports->duration = $duration;
                            $reports->save();
                            $siteStatus = SiteStatus::where('serial_number', $request['serial_number'])->first();
                            // if ($siteStatus->DT_status == "0") {
                            //     $helperClass = new HelperClass();
                            //     $getNotification = $helperClass->sendNotification($siteId, $getLabel);
                            // }
                        } else {
                            $reports->duration = $duration;
                            $reports->status = "Resolved";
                            $reports->save();
                        }
                    } else {
                        if ($value >= $maxValue) {
                            $this->saveAlarm($getLabel, $siteId, $deviceId);
                        }
                    }
                }
            }
            if ($j === 2) {
                $keyvalue = array_search('true', $settingsmin);
                if ($keyvalue) {
                    if ($countsall) {
                        $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
                        $previousTime = $reports->created_at;
                        $start = date($previousTime);
                        $end = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $ends = Carbon::parse($end);
                        $interval = $starts->diff($ends);
                        $duration = $interval->format('%dd:%hh:%im:%ss');

                        if ($value <= $keyvalue) {
                            $reports->duration = $duration;
                            $reports->save();
                            $siteStatus = SiteStatus::where('serial_number', $request['serial_number'])->first();
                            // if ($siteStatus->DT_status == "0") {
                            //     $helperClass = new HelperClass();
                            //     $getNotification = $helperClass->sendNotification($siteId, $getLabel);
                            // }
                        } else {
                            $reports->duration = $duration;
                            $reports->status = "Resolved";
                            $reports->save();
                        }
                    } else {
                        if ($value <= $keyvalue) {
                            $this->saveAlarm($getLabel, $siteId, $deviceId);
                        }
                    }
                }
            }
            if ($j > 2 && $j < 6) {
                $keyvalue = array_search('true', $settingsmin);
                //  dd($keyvalue);
                if ($keyvalue) {
                    if ($countsall) {
                        $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
                        $previousTime = $reports->created_at;
                        $start = date($previousTime);
                        $end = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $ends = Carbon::parse($end);
                        $interval = $starts->diff($ends);
                        $duration = $interval->format('%dd:%hh:%im:%ss');

                        if ($value <= (($keyvalue / 100) * $maxValue) || $value > ($maxValue)) {
                            $reports->duration = $duration;
                            $reports->save();
                            $siteStatus = SiteStatus::where('serial_number', $request['serial_number'])->first();
                            // if ($siteStatus->DT_status == "0") {
                            //     $helperClass = new HelperClass();
                            //     $getNotification = $helperClass->sendNotification($siteId, $getLabel);
                            // }
                        } else {
                            $reports->duration = $duration;
                            $reports->status = "Resolved";
                            $reports->save();
                        }
                    } else {
                        if ($value <= (($keyvalue / 100) * $maxValue) || $value > ($maxValue)) {
                            $this->saveAlarm($getLabel, $siteId, $deviceId);
                        }
                    }
                }
            }
            if ($j > 5 && $j < 9) {
                $keyvalue = array_search('true', $settingsmin);
                if ($keyvalue) {
                    if ($countsall) {
                        $reports = SiteReport::where('site_id', $siteId)->where('status', 'Active')->where('alarm', $getLabel)->first();
                        $previousTime = $reports->created_at;
                        $start = date($previousTime);
                        $end = date('Y-m-d H:i:s');
                        $starts = Carbon::parse($start);
                        $ends = Carbon::parse($end);
                        $interval = $starts->diff($ends);
                        $duration = $interval->format('%dd:%hh:%im:%ss');

                        if ($value <= (($keyvalue / 100) * $maxValue) || $value > ($maxValue)) {
                            $reports->duration = $duration;
                            $reports->save();
                            $siteStatus = SiteStatus::where('serial_number', $request['serial_number'])->first();
                            // if ($siteStatus->DT_status == "0") {
                            //     $helperClass = new HelperClass();
                            //     $getNotification = $helperClass->sendNotification($siteId, $getLabel);
                            // }
                        } else {
                            $reports->duration = $duration;
                            $reports->status = "Resolved";
                            $reports->save();
                        }
                    } else {
                        if ($value <= (($keyvalue / 100) * $maxValue) || $value > ($maxValue)) {
                            $this->saveAlarm($getLabel, $siteId, $deviceId);
                        }
                    }
                }
            }
        }
    }

    public function saveAlarm($getLabel, $siteId, $deviceId)
    {
	 //\Log::info($getLabel);
        $getAlarmCount = Alarmtracker::where('site_id', $siteId)
            ->where('alarm', $getLabel)
            ->whereDate('created_at', '>=', date('Y-m-d') . ' 00:00:00')
            ->whereDate('created_at', '<=', date('Y-m-d') . ' 23:59:59')
            ->first();

        if (!$getAlarmCount) {
            $alarmTracker = Alarmtracker::create([
                'site_id' => $siteId,
                'alarm' => $getLabel,
                'countalarm' => '1',
            ]);
            $alarmTracker->save();
        } else {
                $getAlarmCount->countalarm = ($getAlarmCount->countalarm) + 1;
                $getAlarmCount->save();
            
        }

        $storeAlarmReport = SiteReport::create([
            'alarm' => $getLabel,
            'site_id' => $siteId,
            'device_id' => $deviceId,
            'duration' => '1',
            'status' => 'Active',
            'stop_display' => 0,
            'stop_message' => 0,
            'total_responder' => 0,
        ]);
        $storeAlarmReport->save();

        $countsall = Alarmstatus::where('site_id', $siteId)->count();
        $userId = Site::where('id', $siteId)->value('user_id');
        $serialNumber = Site::where('id', $siteId)->value('serial_number');
       
        if ($countsall > 0) {
           $alarmSound = Alarmstatus::where('site_id', $siteId)->first();
           $alarmSound->alarm_name = $getLabel;
           $alarmSound->alarm_status = '1';
         }
         else {
				$alarmSound = Alarmstatus::create([
					'alarm_name' => $getLabel,
					'site_id' => $siteId,
					'user_id' => $userId,
					'alarm_status' => '1',
				]);
        }
        $alarmSound->save();
        $getAlarm = Alarmtracker::where('site_id', $siteId)->first();
        if ($getAlarm) {
            $getAlarmCount = $getAlarm->countalarm;
            if ($getAlarmCount >= 3 && $getAlarmCount <= 5) {
                $helperClass = new HelperClass();
                $getNotification = $helperClass->sendNotification($siteId, $getLabel);
                $pusher= PushNotifier::where('user_id', $userId)->get();
				if($pusher!=null){
					$resp= $helperClass->alarmsnotify($pusher, $siteId, $getLabel);
					 \Log::info($resp);
				}
            }
        }

    }

 	public function testNotify (Request $request){
        $reg_user= $request->regid;
        $siteId= $request->siteid;
        $getLabel=$request->label;
        $helperClass = new HelperClass();
		$response=$helperClass->alarmnotify($reg_user, $siteId, $getLabel); 
	   return response()->json($response);
    }
    public function updateSiteStatus($request)
    {
        $serialNumber = $request['serial_number'];
        $siteId = Site::where('serial_number', $serialNumber)->value('id');
        $sitenum = Site::where('serial_number', $serialNumber)->value('site_number');
        $userId = Site::where('serial_number', $serialNumber)->value('user_id');
        $uprisers = Site::where('serial_number', $serialNumber)->value('uprisers');

        $countsall = SiteReport::where('site_id', $siteId)->where('status', 'Active')->count();
        $siteStatus = SiteStatus::where('serial_number', $serialNumber)->first();
		if($siteStatus->DT_status == "0"){
			$helperClass = new HelperClass();
		    $pusher= PushNotifier::where('user_id', $userId)->get();
            if($pusher!=null){
				$resp=$helperClass->alarmsnotify($pusher, $siteId,  "Site is Live");
				 \Log::info($resp);
			}
		}
        $siteStatus->DT_status = "1";
        $checkLocation = Location::whereSerialNumber($serialNumber)->first();
        $siteStatus->$userId;
        $channelConfig = ChannelConfig::where("serial_number", $serialNumber)->first();

        if ($countsall > 0) {
            $siteStatus->alarm_status = "1";
            $getAlarmNames = SiteReport::select('alarm')->where('site_id', $siteId)->where('status', 'Active')->get();
            $labelRed_A = $sitenum . " Red Upriser A";
            $labelRed_B = $sitenum . " Red Upriser B";
            $labelRed_C = $sitenum . " Red Upriser C";
            $labelRed_D = $sitenum . " Red Upriser D";

            $labelYellow_A = $sitenum . " Yellow Upriser A";
            $labelYellow_B = $sitenum . " Yellow Upriser B";
            $labelYellow_C = $sitenum . " Yellow Upriser C";
            $labelYellow_D = $sitenum . " Yellow Upriser D";

            $labelBlue_A = $sitenum . " Blue Upriser A";
            $labelBlue_B = $sitenum . " Blue Upriser B";
            $labelBlue_C = $sitenum . " Blue Upriser C";
            $labelBlue_D = $sitenum . " Blue Upriser D";

            $label_A = $sitenum . " Upriser A";
            $label_B = $sitenum . " Upriser B";
            $label_C = $sitenum . " Upriser C";
            $label_D = $sitenum . " Upriser D";
            //echo $getAlarmNames;
            $siteStatus->DT_status = "1";
            if ((int) $uprisers == 1) {
                $isUpA = true;
                foreach ($getAlarmNames as $labels) {
                    $label = $labels->alarm;
                    if (Str::contains($label, 'Upriser A')) {
                        $isUpA = false;
                        if (($label == $labelRed_A) || ($label == $labelYellow_A) || ($label == $labelBlue_A)) {
                            $siteStatus->Up_A = 1;
                            $siteStatus->Up_AStatus = 1;
                        } else if ($label == $label_A) {
                            $siteStatus->Up_A = 2;
                            $siteStatus->Up_AStatus = 0;
                        } else {
                            $siteStatus->Up_A = 0;
                            $siteStatus->Up_AStatus = 1;
                        }
                    }
                }
                if ($isUpA) {
                    $siteStatus->Up_A = 0;
                    $siteStatus->Up_AStatus = 1;
                }
                $siteStatus->Up_B = "N";
                $siteStatus->Up_C = "N";
                $siteStatus->Up_D = "N";
                $siteStatus->Up_BStatus = "N";
                $siteStatus->Up_CStatus = "N";
                $siteStatus->Up_DStatus = "N";
            } else if ((int) $uprisers == 2) {
                $isUpA = true;
                $isUpB = true;

                foreach ($getAlarmNames as $labels) {
                    $label = $labels->alarm;
                    if (Str::contains($label, 'Upriser A')) {
                        $isUpA = false;

                        if (($label == $labelRed_A) || ($label == $labelYellow_A) || ($label == $labelBlue_A)) {
                            $siteStatus->Up_A = 1;
                            $siteStatus->Up_AStatus = "1";
                        } else if ($label == $label_A) {
                            $siteStatus->Up_A = 2;
                            $siteStatus->Up_AStatus = "0";
                        } else {
                            $siteStatus->Up_A = 0;
                            $siteStatus->Up_AStatus = "1";
                        }
                    }
                    if (Str::contains($label, 'Upriser B')) {
                        $isUpB = false;
                        if (($label == $labelRed_B) || ($label == $labelYellow_B) || ($label == $labelBlue_B)) {
                            $siteStatus->Up_B = 1;
                            $siteStatus->Up_BStatus = "1";
                        } else if ($label == $label_B) {
                            $siteStatus->Up_B = 2;
                            $siteStatus->Up_BStatus = "0";
                        } else {
                            $siteStatus->Up_B = 0;
                            $siteStatus->Up_BStatus = "1";
                        }
                    }
                }
                if ($isUpA) {
                    $siteStatus->Up_A = 0;
                    $siteStatus->Up_AStatus = "1";

                }
                if ($isUpB) {
                    $siteStatus->Up_B = 0;
                    $siteStatus->Up_BStatus = "1";

                }
                if ($channelConfig['d1'] == "0" && $channelConfig['d2'] == "0" && $channelConfig['d3'] == "0") {
                    $siteStatus->Up_A = "N";
                    $siteStatus->Up_AStatus = "N";
                }

                if ($channelConfig['d4'] == "0" && $channelConfig['d5'] == "0" && $channelConfig['d6'] == "0") {
                    $siteStatus->Up_B = "N";
                    $siteStatus->Up_BStatus = "N";
                }

                $siteStatus->Up_C = "N";
                $siteStatus->Up_D = "N";
                $siteStatus->Up_CStatus = "N";
                $siteStatus->Up_DStatus = "N";
            } else if ((int) $uprisers == 3) {
                $isUpA = true;
                $isUpB = true;
                $isUpC = true;

                foreach ($getAlarmNames as $labels) {
                    $label = $labels->alarm;
                    if (Str::contains($label, 'Upriser A')) {
                        $isUpA = false;
                        if (($label == $labelRed_A) || ($label == $labelYellow_A) || ($label == $labelBlue_A)) {
                            $siteStatus->Up_A = 1;
                            $siteStatus->Up_AStatus = "1";
                        } else if ($label == $label_A) {
                            $siteStatus->Up_A = 2;
                            $siteStatus->Up_AStatus = "0";
                        } else {
                            $siteStatus->Up_A = 0;
                            $siteStatus->Up_AStatus = "1";
                        }
                    }
                    if (Str::contains($label, 'Upriser B')) {
                        $isUpB = false;
                        if (($label == $labelRed_B) || ($label == $labelYellow_B) || ($label == $labelBlue_B)) {
                            $siteStatus->Up_B = 1;
                            $siteStatus->Up_BStatus = "1";
                        } else if ($label == $label_B) {
                            $siteStatus->Up_B = 2;
                            $siteStatus->Up_BStatus = "0";
                        } else {
                            $siteStatus->Up_B = 0;
                            $siteStatus->Up_BStatus = "1";
                        }
                    }

                    if (Str::contains($label, 'Upriser C')) {
                        $isUpC = false;
                        if (($label == $labelRed_C) || ($label == $labelYellow_C) || ($label == $labelBlue_C)) {
                            $siteStatus->Up_C = 1;
                            $siteStatus->Up_CStatus = "1";
                        } else if ($label == $label_C) {
                            $siteStatus->Up_C = 2;
                            $siteStatus->Up_CStatus = "0";
                        } else {
                            $siteStatus->Up_C = 0;
                            $siteStatus->Up_CStatus = "1";
                        }
                    }

                }
                if ($isUpA) {
                    $siteStatus->Up_A = 0;
                    $siteStatus->Up_AStatus = "1";

                }
                if ($isUpB) {
                    $siteStatus->Up_B = 0;
                    $siteStatus->Up_BStatus = "1";

                }
                if ($isUpC) {
                    $siteStatus->Up_C = 0;
                    $siteStatus->Up_CStatus = "1";

                }
                if ($channelConfig['d1'] == "0" && $channelConfig['d2'] == "0" && $channelConfig['d3'] == "0") {
                    $siteStatus->Up_A = "N";
                    $siteStatus->Up_AStatus = "N";
                }

                if ($channelConfig['d4'] == "0" && $channelConfig['d5'] == "0" && $channelConfig['d6'] == "0") {
                    $siteStatus->Up_B = "N";
                    $siteStatus->Up_BStatus = "N";
                }

                if ($channelConfig['d7'] == "0" && $channelConfig['d8'] == "0" && $channelConfig['d9'] == "0") {
                    $siteStatus->Up_C = "N";
                    $siteStatus->Up_CStatus = "N";
                }

                $siteStatus->Up_D = "N";
                $siteStatus->Up_DStatus = "N";

            } else if ((int) $uprisers == 4) {
                $isUpA = true;
                $isUpB = true;
                $isUpC = true;
                $isUpD = true;
                foreach ($getAlarmNames as $labels) {
                    $label = $labels->alarm;
                    if (Str::contains($label, 'Upriser A')) {
                        $isUpA = false;
                        if (($label == $labelRed_A) || ($label == $labelYellow_A) || ($label == $labelBlue_A)) {
                            $siteStatus->Up_A = 1;
                            $siteStatus->Up_AStatus = "1";
                        } else if ($label == $label_A) {
                            $siteStatus->Up_A = 2;
                            $siteStatus->Up_AStatus = "0";
                        } else {
                            $siteStatus->Up_A = 0;
                            $siteStatus->Up_AStatus = "1";
                        }
                    }
                    if (Str::contains($label, 'Upriser B')) {
                        $isUpB = false;
                        if (($label == $labelRed_B) || ($label == $labelYellow_B) || ($label == $labelBlue_B)) {
                            $siteStatus->Up_B = 1;
                            $siteStatus->Up_BStatus = "1";
                        } else if ($label == $label_B) {
                            $siteStatus->Up_B = 2;
                            $siteStatus->Up_BStatus = "0";
                        } else {
                            $siteStatus->Up_B = 0;
                            $siteStatus->Up_BStatus = "1";
                        }
                    }

                    if (Str::contains($label, 'Upriser C')) {
                        $isUpC = false;
                        if (($label == $labelRed_C) || ($label == $labelYellow_C) || ($label == $labelBlue_C)) {
                            $siteStatus->Up_C = 1;
                            $siteStatus->Up_CStatus = "1";
                        } else if ($label == $label_C) {
                            $siteStatus->Up_C = 2;
                            $siteStatus->Up_CStatus = "0";
                        } else {
                            $siteStatus->Up_C = 0;
                            $siteStatus->Up_CStatus = "1";
                        }
                    }

                    if (Str::contains($label, 'Upriser D')) {
                        $isUpD = false;
                        if (($label == $labelRed_D) || ($label == $labelYellow_D) || ($label == $labelBlue_D)) {
                            $siteStatus->Up_D = 1;
                            $siteStatus->Up_DStatus = "1";
                        } else if ($label == $label_D) {
                            $siteStatus->Up_D = 2;
                            $siteStatus->Up_DStatus = "1";
                        } else {
                            $siteStatus->Up_D = 0;
                            $siteStatus->Up_DStatus = "1";
                        }
                    }
                }
                if ($isUpA) {
                    $siteStatus->Up_A = 0;
                    $siteStatus->Up_AStatus = "1";
                }
                if ($isUpB) {
                    $siteStatus->Up_B = 0;
                    $siteStatus->Up_BStatus = "1";

                }
                if ($isUpC) {
                    $siteStatus->Up_C = 0;
                    $siteStatus->Up_CStatus = "1";

                }
                if ($isUpD) {
                    $siteStatus->Up_D = 0;
                    $siteStatus->Up_DStatus = "1";
                }
                if ($channelConfig['d1'] == "0" && $channelConfig['d2'] == "0" && $channelConfig['d3'] == "0") {
                    $siteStatus->Up_A = "N";
                    $siteStatus->Up_AStatus = "N";
                }

                if ($channelConfig['d4'] == "0" && $channelConfig['d5'] == "0" && $channelConfig['d6'] == "0") {
                    $siteStatus->Up_B = "N";
                    $siteStatus->Up_BStatus = "N";
                }

                if ($channelConfig['d7'] == "0" && $channelConfig['d8'] == "0" && $channelConfig['d9'] == "0") {
                    $siteStatus->Up_C = "N";
                    $siteStatus->Up_CStatus = "N";
                }

                if ($channelConfig['d10'] == "0" && $channelConfig['d11'] == "0" && $channelConfig['d12'] == "0") {
                    $siteStatus->Up_D = "N";
                    $siteStatus->Up_DStatus = "N";
                }

            }
            $siteStatus->save();

        } else {
            $siteStatus = SiteStatus::where('serial_number', $serialNumber)->first();
            $siteStatus->DT_status = "1";
            $siteStatus->$userId;
            $siteStatus->alarm_status = "0";
            if ((int) $uprisers == 1) {
                $siteStatus->Up_A = "0";
                $siteStatus->Up_B = "N";
                $siteStatus->Up_C = "N";
                $siteStatus->Up_D = "N";
                $siteStatus->Up_AStatus = "1";
                $siteStatus->Up_BStatus = "N";
                $siteStatus->Up_CStatus = "N";
                $siteStatus->Up_DStatus = "N";
            } else if ((int) $uprisers == 2) {
                $siteStatus->Up_A = "0";
                $siteStatus->Up_B = "0";
                $siteStatus->Up_C = "N";
                $siteStatus->Up_D = "N";
                $siteStatus->Up_AStatus = "1";
                $siteStatus->Up_BStatus = "1";
                $siteStatus->Up_CStatus = "N";
                $siteStatus->Up_DStatus = "N";
            } else if ((int) $uprisers == 3) {

                $siteStatus->Up_A = "0";
                $siteStatus->Up_B = "0";
                $siteStatus->Up_C = "0";
                $siteStatus->Up_D = "N";
                $siteStatus->Up_AStatus = "1";
                $siteStatus->Up_BStatus = "1";
                $siteStatus->Up_CStatus = "1";
                $siteStatus->Up_DStatus = "N";

            } else if ((int) $uprisers == 4) {
                $siteStatus->Up_A = "0";
                $siteStatus->Up_B = "0";
                $siteStatus->Up_C = "0";
                $siteStatus->Up_D = "0";
                $siteStatus->Up_AStatus = "1";
                $siteStatus->Up_BStatus = "1";
                $siteStatus->Up_CStatus = "1";
                $siteStatus->Up_DStatus = "1";
            }
            $siteStatus->updated_at = date('Y-m-d H:i:s');
            $siteStatus->save();
        }
        $checkLocation->save();
    }
  	public function cleanalarmtracker()
    {
        $getReport = DB::table('alarmtracker')
            ->whereDate('created_at', '<', date('Y-m-d') . ' 00:00:00')
            ->delete();

        return response()->json(["status" => 200, "message" => "alarm tracker cleaned"]);

    }
    // get power details from incoming request
    public function powerDetails($request, $all_data)
    {
        $siteId = Site::where('serial_number', $request->serial_number)->value('id');
        $userId = Device::where('serial_number', $request->serial_number)->value('user_id');
        $checkPower = Power::whereSerialNumber($request->serial_number)->first();
        if ($checkPower == null) {
            $Iav = ($all_data['a7'] + $all_data['a8'] + $all_data['a9']) / 3;
            $incoming_power = 1.732 * 0.415 * 0.9 * $Iav;
            $dt = Carbon::now();
            $hour = $dt->hour;
            $day = $dt->day;
            $week = $dt->weekOfMonth;
            $month = $dt->month;
            $year = $dt->year;
            $powers = Power::create([
                'serial_number' => $request->serial_number,
                'countperhr' => "1",
                'hour' => $hour,
                'day' => $day,
                'week' => $week,
                'month' => $month,
                'year' => $year,
                'current_power' => $incoming_power,
                'site_id' => $siteId,
                'user_id' => $userId,

            ]);
            $powers->save();
        } else {
            if (is_numeric($all_data['a7'])) {
                $Iava = floatval($all_data['a7']);
            } else {
                $Iava = 0;
            }
            if (is_numeric($all_data['a8'])) {
                $Iavb = floatval($all_data['a8']);
            } else {
                $Iavb = 0;
            }
            if (is_numeric($all_data['a9'])) {
                $Iavc = floatval($all_data['a9']);
            } else {
                $Iavc = 0;
            }
            $Iav = ($Iava + $Iavb + $Iavc) / 3;
            $incoming_power = 1.732 * 0.415 * 0.9 * $Iav;
            $powers = Power::whereSerialNumber($request->serial_number)->first();
            $cntHr = $powers->countperhr;
            $Hr = $powers->hour;
            $dy = $powers->day;
            $wk = $powers->week;
            $mth = $powers->month;
            $yr = $powers->year;
            $current_power = $powers->current_power;
            $power_hourly = $powers->power_hourly;
            $power_daily = $powers->power_daily;
            $power_weekly = $powers->power_weekly;
            $power_monthly = $powers->power_monthly;
            $power_yearly = $powers->power_yearly;
            $dt = Carbon::now();
            $hour = $dt->hour;
            $day = $dt->day;
            $week = $dt->weekOfMonth;
            $month = $dt->month;
            $year = $dt->year;
            if ($Hr == $hour) {
                $cntHr = $cntHr + 1;
                $current_power = $current_power + $incoming_power;
            } else {
                $power_hourly = $current_power / $cntHr;
                $cntHr = '1';
                $current_power = $incoming_power;
                if ($dy == $day) {
                    $power_daily = $power_daily + $power_hourly;
                } else {
                    if ($wk == $week) {
                        $power_weekly = $power_weekly + $power_daily;
                    } else {
                        if ($mth == $month) {
                            $power_monthly = $power_monthly + $power_weekly;
                        } else {
                            if ($yr == $year) {
                                $power_yearly = $power_yearly + $power_monthly;
                            } else {
                                $power_yearly = $power_monthly;
                            }
                            $power_monthly = $power_daily;
                        }
                        $power_weekly = $power_daily;
                    }
                    $power_daily = $power_hourly;
                }
            }
            $powers->countperhr = $cntHr;
            $powers->current_power = $current_power;
            $powers->countperhr = $cntHr;
            $powers->hour = $hour;
            $powers->day = $day;
            $powers->week = $week;
            $powers->month = $month;
            $powers->year = $year;
            $powers->power_hourly = $power_hourly;
            $powers->power_daily = $power_daily;
            $powers->power_weekly = $power_weekly;
            $powers->power_monthly = $power_monthly;
            $powers->power_yearly = $power_yearly;
            $powers->save();
        }
    }

   

}