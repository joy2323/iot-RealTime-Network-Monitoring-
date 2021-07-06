<?php

namespace App\Http\Controllers;

use App\Alarmstatus;
use App\AnalogSetting;
use App\AnalogUnit;
use App\AnalogValue;
use App\ChannelConfig;
use App\Communication;
use App\Dashboards;
use App\Detail;
use App\Device;
use App\Label;
use App\Location;
use App\Site;
use App\SiteStatus;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

// use Auth;

class DeviceConfigController extends Controller
{

    public function createSite(Request $request)
    {
        try {

            $dataSite = $request->createSite;
            $objectSite = (object) $dataSite;
            $dataDevice = $request->createDevice;
            $objectDevice = (object) $dataDevice;
            $category = $objectDevice->Category;
            $devicecatid = DB::table('device_categories')->where('name', $category)->value('id');
			
            $clientCheck = User::where('email', $objectSite->ClientEmail)->first();
		  
            if (!$clientCheck) {
                return response()->json([
                    'message' => 'Client not registered',
                    'status' => '400',
                ]);
            }
		    $buname = strtoupper($objectDevice->Owner);
            $deviceuser = DB::table('users')->where('name', 'LIKE', '%'.$buname.'%')->where('owner_id', $clientCheck->id)->first();

            if (!$deviceuser) {
                return response()->json([
                    'message' => 'BU not registered',
                    'status' => '400',
                ]);
            }
            $deviceuserid = $deviceuser->id;
            $deviceCheck = Device::where('serial_number', $objectDevice->SerialNumber)->first();
            if ($deviceCheck) {
                return response()->json([
                    'message' => 'Device Already Exist',
                    'status' => '201',
                ]);
            }
            $siteCheck = Site::where('serial_number', $objectSite->SerialNumber)->first();
            if ($siteCheck) {
                return response()->json([
                    'message' => 'Site Already Exist',
                    'status' => '201',
                ]);
            }
            if ($devicecatid == 1) {
                $this->createDT($request);

            } else if ($devicecatid == 2) {
                $this->createHV($request);
            } else {
                return response()->json([
                    'message' => 'Invalid category',
                    'status' => '400',
                ]);
            }

            return response()->json([
                'message' => 'Data Successfully Added',
                'status' => '200',
            ]);

        } catch (\Throwable $th) {

           return response()->json([
                'message' => 'Error occur!!',
                'status' => '400',
            ]);
        }
		
    }

    public function createDT(Request $request)
    {

        $dataSite = $request->createSite;
        $objectSite = (object) $dataSite;
        $clientCheck = User::where('email', $objectSite->ClientEmail)->first();
        $dataDevice = $request->createDevice;
        $objectDevice = (object) $dataDevice;
        $category = $objectDevice->Category;
        $devicecatid = DB::table('device_categories')->where('name', $category)->value('id');
		$buname = strtoupper($objectDevice->Owner);
        $deviceuser = DB::table('users')->where('name', 'LIKE', '%'.$buname.'%')->where('owner_id', $clientCheck->id)->first();
        $deviceuserid = $deviceuser->id;
        $deviceCheck = Device::where('serial_number', $objectDevice->SerialNumber)->first();
        $details = Device::create([
            'device_category_id' => $devicecatid,
            'serial_number' => $objectDevice->SerialNumber,
            'name' => strtoupper($objectDevice->DeviceName),
            'INJstation' => strtoupper($objectDevice->INJStation),
            'feeder' => strtoupper($objectDevice->Feeder),
            'status' => $objectDevice->Status,
            'network' => $objectDevice->DeviceNetwork,
            'phone_number' => $objectDevice->SIMnumber,
            'subscription_date' => $objectDevice->SubscriptionDate,
            'activation' => $objectDevice->Activation,
            'user_id' => $deviceuserid,
        ]);
        $details->save();
        $ut_user = User::where('name', strtoupper($objectSite->UT))->where("owner_id",$deviceuserid)->first();
        if (!$ut_user || $ut_user == null) {

            $ut_user = User::create([
                'name' => strtoupper($objectSite->UT),
                'email' => $objectSite->UTEmail,
                'role' => 'UT admin',
                'phone_number' => '0123456789',
                'address' => $deviceuser->address,
                'image' => 'img/profile/site.png',
                'password' => Hash::make($objectSite->UTPass),
                'owner_id' => $deviceuserid,
            ]);

            $dash = Dashboards::create([
                'name' => strtoupper($objectSite->UT),
                'master_id' => $ut_user->id,
            ]);
            $dash->save();
            $ut_user->dashboard_id = $dash->id;
            $ut_user->save();

            $communicationut = Communication::create([
                'sms_user_type' => 'First Responder',
                'sms_mobile_number' => '0123456789',
                'sms_enable' => '0',
                'email_user_type' => 'First Responder',
                'email_address' => 'utemail',
                'email_enable' => '0',
                'user_id' => $ut_user->id,
                'owner_id' => $deviceuserid,
                'role' => "UT admin",
                'schedule_time' => 0,
            ]);
            $communicationut->save();
        }

        $client = $clientCheck->id;
        $sitenum = $objectSite->SiteID;
        // $siteName = ucwords($objectSite->SiteName, "-");
        $siteName = $objectSite->SiteName;
        $site = Site::create([
            'device_id' => $details->id,
            'site_number' => $objectSite->SiteID,
            'name' => strtoupper($siteName),
            'phone_number' => $objectSite->DevicePhonenumber,
            'uprisers' => $objectSite->Uprisers,
            'serial_number' => $objectSite->SerialNumber,
            'ctrl_enable' => '0',
            'email' => $objectSite->Email,
            'username' => $objectSite->Username,
            'password' => $objectSite->Password,
            'confirm_password' => $objectSite->Confirmpassword,
            'activation' => $objectSite->Activation,
            'user_id' => $deviceuserid,
            'client_id' => $client,
            'ut_id' => $ut_user->id,

        ]);
        $site->save();

        $siteid = $site->id;
        $dataComm = $request->communication;
        $objectComm = ((object) $dataComm)->FirstSet;
        $objectFirst = (object) $objectComm;
        $objectSMS = $objectFirst->SMS;
        $objectEmail = $objectFirst->Email;

        for ($i = 0; $i < 2; $i++) {
            $communication = Communication::where('user_id', $deviceuserid)
                ->where('email_address', $objectEmail[$i]['EmailAddress'])
                ->where('sms_mobile_number', $objectSMS[$i]['MobileNumber'])->get()->count() > 0;
            if (!$communication) {
                $communication = Communication::create([
                    'sms_user_type' => $objectSMS[$i]['UserType'],
                    'sms_mobile_number' => $objectSMS[$i]['MobileNumber'],
                    'sms_enable' => $objectSMS[$i]['Enable'],
                    'email_user_type' => $objectEmail[$i]['UserType'],
                    'email_address' => $objectEmail[$i]['EmailAddress'],
                    'email_enable' => $objectEmail[$i]['Enable'],
                    'user_id' => $deviceuserid,
                    'owner_id' => $client,
                    'role' => "BU admin",
                    'schedule_time' => 0,
                ]);
                $communication->save();
            }

        }

        $label = Label::create([
            'site_id' => $site->id,
            'serial_number' => $site->serial_number,
            'a1' => $sitenum . ' ' . 'Env. Temp.',
            'a2' => $sitenum . ' ' . 'Trans. Temp.',
            'a3' => $sitenum . ' ' . 'Oil Level',
            'a4' => $sitenum . ' ' . 'Voltage Phase A',
            'a5' => $sitenum . ' ' . 'Voltage Phase B',
            'a6' => $sitenum . ' ' . 'Voltage Phase C',
            'a7' => $sitenum . ' ' . 'Current Phase A',
            'a8' => $sitenum . ' ' . 'Current Phase B',
            'a9' => $sitenum . ' ' . 'Current Phase C',
            'd1' => $sitenum . ' ' . 'Red Upriser A',
            'd2' => $sitenum . ' ' . 'Yellow Upriser A',
            'd3' => $sitenum . ' ' . 'Blue Upriser A',
            'd4' => $sitenum . ' ' . 'Red Upriser B',
            'd5' => $sitenum . ' ' . 'Yellow Upriser B',
            'd6' => $sitenum . ' ' . 'Blue Upriser B',
            'd7' => $sitenum . ' ' . 'Red Upriser C',
            'd8' => $sitenum . ' ' . 'Yellow Upriser C',
            'd9' => $sitenum . ' ' . 'Blue Upriser C',
            'd10' => $sitenum . ' ' . 'Red Upriser D',
            'd11' => $sitenum . ' ' . 'Yellow Upriser D',
            'd12' => $sitenum . ' ' . 'Blue Upriser D',
        ]);
        $label->save();

        $siteupriser = $site->uprisers;
        $totalchannel = $siteupriser * 3;

        $getDigitalArray = array('d1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11', 'd12',
            'd12', 'd13', 'd14', 'd15', 'd16', 'd17', 'd18', 'd19', 'd20', 'd21', 'd22', 'd23', 'd24');
        $getAnalogArray = array('a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12',
            'a12', 'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24');
        $array_details = [];
        $array_details = ['serial_number' => $site->serial_number];
        for ($i = 0; $i < $totalchannel; $i++) {
            $array_details += [$getDigitalArray[$i] => 'N'];

        }
        // dd($array_product);
        $details = Detail::create($array_details);
        $details->save();

        //populate channel config
        $array_channel = [];
        $array_channel = ['serial_number' => $site->serial_number];
        for ($i = 0; $i < $totalchannel; $i++) {
            $array_channel += [$getDigitalArray[$i] => '1'];

        }
        $channel = ChannelConfig::create($array_channel);
        $channel->save();

        //analog setting
        $array_analog_setting = [];
        $content_obj = array(
            "alarm_name" => "ALARM NAME",
            "settings" => array(
                "25" => "false",
                "50" => "false",
                "75" => "false",
            ),
            "max_input" => 100,
        );
        $array_analog_setting = ['serial_number' => $site->serial_number];
        $array_content = [];
        for ($i = 0; $i < 24; $i++) {
            $alarmName = Label::where('serial_number', $site->serial_number)->value($getAnalogArray[$i]);
            $content_obj['alarm_name'] = $alarmName;
            array_push($array_content, (object) $content_obj);
        }
        $array_analog_setting += ['content' => json_encode($array_content)];
        $analogSetting = AnalogSetting::create($array_analog_setting);
        $analogSetting->save();

        //inset default loacation

        $location = Location::create([
            'serial_number' => $site->serial_number,
            'lat' => "6.545454",
            'long' => "3.334343",
        ]);
        $location->save();

        $alarmSound = Alarmstatus::create([

            'site_id' => $siteid,
            'user_id' => $deviceuserid,
            'alarm_status' => 'N',
        ]);
        $alarmSound->save();
        //analog valueF
        $array_analog_value = [];
        $array_analog_value = ['serial_number' => $site->serial_number];
        $analogValue = AnalogValue::create($array_analog_value);
        $analogValue->save();

        //analog unit

        $array_analog_unit = [];
        $array_analog_unit = ['serial_number' => $site->serial_number];
        $analogUnit = AnalogUnit::create($array_analog_unit);
        $analogUnit->save();

        //site status
        $array_site_status = [];
        $array_site_status = ['serial_number' => $site->serial_number];
        $array_site_status += ["user_id" => $deviceuserid];
        if ((int) $siteupriser == 1) {
            $array_site_status += ["Up_A" => "2"];
        } else if ((int) $siteupriser == 2) {
            $array_site_status += ["Up_A" => "2"];
            $array_site_status += ["Up_B" => "2"];
        } else if ((int) $siteupriser == 3) {
            $array_site_status += ["Up_A" => "2"];
            $array_site_status += ["Up_B" => "2"];
            $array_site_status += ["Up_C" => "2"];
        } else if ((int) $siteupriser == 4) {
            $array_site_status += ["Up_A" => "2"];
            $array_site_status += ["Up_B" => "2"];
            $array_site_status += ["Up_C" => "2"];
            $array_site_status += ["Up_D" => "2"];
        }
        $site_status = SiteStatus::create($array_site_status);
        $site_status->save();

    }

    public function createHV(Request $request)
    {
        $dataSite = $request->createSite;
        $objectSite = (object) $dataSite;
        $clientCheck = User::where('email', $objectSite->ClientEmail)->first();
        $dataDevice = $request->createDevice;
        $objectDevice = (object) $dataDevice;
        $category = $objectDevice->Category;
        $devicecatid = DB::table('device_categories')->where('name', $category)->value('id');
        $deviceuser = DB::table('users')->where('name', $objectDevice->Owner)->first();

        $deviceuserid = $deviceuser->id;
        $deviceCheck = Device::where('serial_number', $objectDevice->SerialNumber)->first();
        $details = Device::create([
            'device_category_id' => $devicecatid,
            'serial_number' => $objectDevice->SerialNumber,
            'name' => $objectDevice->DeviceName,
            'INJstation' => strtoupper($objectDevice->INJStation),
            'feeder' => strtoupper($objectDevice->Feeder),
            'status' => $objectDevice->Status,
            'network' => $objectDevice->DeviceNetwork,
            'phone_number' => $objectDevice->SIMnumber,
            'subscription_date' => $objectDevice->SubscriptionDate,
            'activation' => $objectDevice->Activation,
            'user_id' => $deviceuserid,
        ]);
        $details->save();
        $client = $clientCheck->id;
        $sitenum = $objectSite->SiteID;
        $site = Site::create([
            'device_id' => $details->id,
            'site_number' => $objectSite->SiteID,
            'name' => strtoupper($objectSite->SiteName),
            'phone_number' => $objectSite->DevicePhonenumber,
            'uprisers' => $objectSite->Feeders,
            'serial_number' => $objectSite->SerialNumber,
            'ctrl_enable' => '0',
            'email' => $objectSite->Email,
            'username' => $objectSite->Username,
            'password' => $objectSite->Password,
            'confirm_password' => $objectSite->Confirmpassword,
            'activation' => $objectSite->Activation,
            'user_id' => $deviceuserid,
            'client_id' => $client,
            'ut_id' => null,

        ]);
        $site->save();

        $siteid = $site->id;
        $dataComm = $request->communication;
        $objectComm = ((object) $dataComm)->FirstSet;
        $objectFirst = (object) $objectComm;
        $objectSMS = $objectFirst->SMS;
        $objectEmail = $objectFirst->Email;

        for ($i = 0; $i < 2; $i++) {
            $communication = Communication::where('user_id', $deviceuserid)
                ->where('email_address', $objectEmail[$i]['EmailAddress'])
                ->where('sms_mobile_number', $objectSMS[$i]['MobileNumber'])->get()->count() > 0;
            if (!$communication) {
                $communication = Communication::create([
                    'sms_user_type' => $objectSMS[$i]['UserType'],
                    'sms_mobile_number' => $objectSMS[$i]['MobileNumber'],
                    'sms_enable' => $objectSMS[$i]['Enable'],
                    'email_user_type' => $objectEmail[$i]['UserType'],
                    'email_address' => $objectEmail[$i]['EmailAddress'],
                    'email_enable' => $objectEmail[$i]['Enable'],
                    'user_id' => $deviceuserid,
                    'owner_id' => $client,
                    'role' => "INJ admin",
                    'schedule_time' => 0,
                ]);
                $communication->save();
            }

        }

        $label = Label::create([
            'site_id' => $site->id,
            'serial_number' => $site->serial_number,
            'd1' => $sitenum . ' ' . 'Feeder 1',
            'd2' => $sitenum . ' ' . 'Feeder 2',
            'd3' => $sitenum . ' ' . 'Feeder 3',
            'd4' => $sitenum . ' ' . 'Feeder 4',
            'd5' => $sitenum . ' ' . 'Feeder 5',
            'd6' => $sitenum . ' ' . 'Feeder 6',
            'd7' => $sitenum . ' ' . 'Feeder 7',
            'd8' => $sitenum . ' ' . 'Feeder 8',
            'd9' => $sitenum . ' ' . 'Feeder 9',
            'd10' => $sitenum . ' ' . 'Feeder 10',
            'd11' => $sitenum . ' ' . 'Feeder 11',
            'd12' => $sitenum . ' ' . 'Feeder 12',
        ]);
        $label->save();

        $feeders = $site->uprisers;

        $getDigitalArray = array('d1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11', 'd12',
            'd12', 'd13', 'd14', 'd15', 'd16', 'd17', 'd18', 'd19', 'd20', 'd21', 'd22', 'd23', 'd24');
        $getAnalogArray = array('a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12',
            'a12', 'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24');
        $array_details = [];
        $array_details = ['serial_number' => $site->serial_number];
        for ($i = 0; $i < $feeders; $i++) {
            $array_details += [$getDigitalArray[$i] => '0'];

        }
        // dd($array_product);
        $details = Detail::create($array_details);
        $details->save();

        //populate channel config
        $array_channel = [];
        $array_channel = ['serial_number' => $site->serial_number];
        for ($i = 0; $i < $feeders; $i++) {
            $array_channel += [$getDigitalArray[$i] => '1'];

        }
        $channel = ChannelConfig::create($array_channel);
        $channel->save();

        //analog setting
        $array_analog_setting = [];
        $content_obj = array(
            "alarm_name" => "ALARM NAME",
            "settings" => array(
                "25" => "false",
                "50" => "false",
                "75" => "false",
            ),
            "max_input" => 100,
        );
        $array_analog_setting = ['serial_number' => $site->serial_number];
        $array_content = [];
        for ($i = 0; $i < 24; $i++) {
            $alarmName = Label::where('serial_number', $site->serial_number)->value($getAnalogArray[$i]);
            $content_obj['alarm_name'] = $alarmName;
            array_push($array_content, (object) $content_obj);
        }
        $array_analog_setting += ['content' => json_encode($array_content)];
        $analogSetting = AnalogSetting::create($array_analog_setting);
        $analogSetting->save();

        //inset default loacation

        $location = Location::create([
            'serial_number' => $site->serial_number,
            'lat' => "6.545454",
            'long' => "3.334343",
        ]);
        $location->save();

        $array_analog_value = [];
        $array_analog_value = ['serial_number' => $site->serial_number];
        $analogValue = AnalogValue::create($array_analog_value);
        $analogValue->save();

        //analog unit

        $array_analog_unit = [];
        $array_analog_unit = ['serial_number' => $site->serial_number];
        $analogUnit = AnalogUnit::create($array_analog_unit);
        $analogUnit->save();

    }

    public function getDeviceParameter()
    {
        $siteParameter = Site::join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select([
                'sites.serial_number', 'sites.name', 'sites.uprisers', 'locations.lat', 'locations.long',
            ])->get();

        return response()->json($siteParameter);

    }

    public function getsitebyname(Request $request)
    {
        $str = $request->get('query');
        $data = Site::where('name', 'LIKE', '%' . $str . '%')
            ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select([
                'sites.serial_number', 'sites.name', 'sites.uprisers', 'locations.lat', 'locations.long',
            ])->get();
        return response()->json($data);
    }


    public function getClient(Request $request)
    {
        $clients = User::where('role', 'Client admin')
            ->where('master_role', 1)
            ->select([
                'id', 'name',
            ])->get();
        return response()->json($clients);
    }

    public function getClientUnit(Request $request)
    {

        $clients = User::where('role', 'BU admin')->where('owner_id', $request->id)
            ->where('master_role', 1)
            ->select([
                'id', 'name',
            ])->get();
        return response()->json($clients);
    }

    public function getClientSubUnit(Request $request)
    {

        $clients = User::where('role', 'UT admin')->where('owner_id', $request->id)
            ->where('master_role', 1)
            ->select([
                'id', 'name',
            ])->get();
        return response()->json($clients);

    }


    public function getSites(Request $request)
    {
        
        $client = $request->client_id;
        $bu = $request->bu_id;
        $ut = $request->ut_id;
        $index = 0;
        $ctrldata = array();
        if ($client == "All" && $bu == "All" && $ut == "All") {
            $siteParameter = Site::join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select([
                'sites.serial_number', 'sites.name', 'sites.uprisers', 'locations.lat', 'locations.long',
            ])->get();

      
        }else if ($client != "All" && $bu == "All" && $ut == "All") {

            $siteParameter = Site::where('client_id', $client)->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select([
                'sites.serial_number', 'sites.name', 'sites.uprisers', 'locations.lat', 'locations.long',
            ])->get();
        }
        else if ( $bu != "All" && $ut == "All") {

            $siteParameter = Site::where('client_id', $client)
            ->where('user_id', $bu)->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select([
                'sites.serial_number', 'sites.name', 'sites.uprisers', 'locations.lat', 'locations.long',
            ])->get();

        }
        else if ($ut != "All") {
            $siteParameter = Site::where('client_id', $client)
            ->where('user_id', $bu)
            ->where('ut_id', $bu)->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select([
                'sites.serial_number', 'sites.name', 'sites.uprisers', 'locations.lat', 'locations.long',
            ])->get();

        }

        return response()->json($siteParameter);
    }

}