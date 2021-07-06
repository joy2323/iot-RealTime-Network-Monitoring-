<?php

namespace App\Http\Controllers;

use App\Alarmstatus;
use App\AnalogSetting;
use App\AnalogUnit;
use App\AnalogValue;
use App\ChannelConfig;
use App\Control;
use App\CtrlFeedback;
use App\Dashboards;
use App\Detail;
use App\Device;
use App\DownLiveSite;
use App\GlobalSetting;
use App\Label;
use App\Location;
use App\Power;
use App\Site;
use App\SiteReport;
use App\SiteStatus;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use App\User;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Response;
use App\LoginActivities;

class HomeController extends Controller
{
    use UploadTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
	
	  /**
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	*/

    public function index()
    {
        $user_info = User::where('role', 'Client admin')->where('master_role', 1)->get();
        $sitesum = Site::join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')->count();
        $activated = SiteStatus::where('DT_status', '1')->orWhere('DT_status', '0')
            ->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '1')->count();
        $current = Carbon::now();
        $getTimelapse = $current->subDays(10);
        $nottransmit = Detail::whereDate('details.updated_at', '<=', $getTimelapse)
            ->join('devices', 'devices.serial_number', '=', 'details.serial_number')
            ->where('device_category_id', '1')->count();
        $liveSite = SiteStatus::where('DT_status', '1')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '1')->count();
        $downSite = SiteStatus::where('DT_status', '0')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '1')->count() + SiteStatus::where('DT_status', 'N')
            ->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '1')->count();

        $current = Carbon::now();
        $getTimelapse = $current->subDays(30);
        $fault = DB::table('site_reports')->where('site_reports.status', 'Active')->whereDate('site_reports.created_at', '<=', $getTimelapse)
            ->Where("site_reports.alarm", "!=", "SITE DOWN")
            ->join('sites', 'sites.id', '=', 'site_reports.site_id')
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('devices.device_category_id', '1')
            ->select(['site_reports.site_id', 'sites.user_id', 'sites.client_id', 'sites.name as SiteName', 'site_reports.alarm as alarm', 'site_reports.duration', 'site_reports.created_at'])
            ->get()->unique('SiteName')->count();

        $userinfo['client'] = $user_info;
        $userinfo['sum'] = count($user_info);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        $userinfo['active'] = $activated;
        $userinfo['notactive'] = $nottransmit;
        $userinfo['faultsite'] = $fault;

        return view('super_admin.home', $userinfo);
    }

    public function indexhv()
    {
        $user_info = User::where('role', 'Client admin')
            ->where('master_role', 1)
            ->join('sites', 'sites.client_id', '=', 'users.id')
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '2')->select([
            'users.*',
        ])->get();
        $sitesum = Site::join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '2')->count();
        $activated = SiteStatus::where('DT_status', '1')->orWhere('DT_status', '0')
            ->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count();
        $notactivated = SiteStatus::where('DT_status', 'N')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count();
        $liveSite = SiteStatus::where('DT_status', '1')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count();
        $downSite = SiteStatus::where('DT_status', '0')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count() + SiteStatus::where('DT_status', 'N')
            ->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count();
        $userinfo['client'] = $user_info;
        $userinfo['sum'] = count($user_info);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        $userinfo['active'] = $activated;
        $userinfo['notactive'] = $notactivated;

        return view('super_admin.homeHV', $userinfo);
    }

    public function Unauthorized()
    {
        return view('401');
    }
    public function getSiteLocation()
    {
        $locations = DB::table('sites')
            ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
            ->where('devices.device_category_id', '=', 1)
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

    public function getHVSiteLocation()
    {
        $locations = DB::table('sites')
            ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
            ->where('devices.device_category_id', '=', 2)
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

    public function getClients()
    {
        $clients = User::where('role', 'Client admin')->where('master_role', 1)
            ->select([
                'id', 'name', 'email', 'phone_number', 'address', 'ctrl_auth',
            ])->get();

        $allClients = array();
        $index = 0;
        foreach ($clients as $key => $value) {
            # code...
            $installedsite = Site::where('sites.client_id', $value->id)
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->where('devices.device_category_id', '=', 1)->count();
            $allClients[$index] = $value;
            $name = User::where('id', $value->id)->value("name");
            $clientactivate = GlobalSetting::where('name', $name)->first();
            if ($clientactivate) {
                $allClients[$index]->setting = $clientactivate->setting['activate'];
            } else {
                $allClients[$index]->setting = '1';
            }

            $allClients[$index]->totalsites = $installedsite;

            $index++;

        }

        if (request()->ajax()) {
            return DataTables::of($allClients)
                ->addIndexColumn()
                ->addColumn('action', 'super_admin.action')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('super_admin.clients');
    }

    public function channelConfig($id)
    {
        $channel = ChannelConfig::where('id', $id)
            ->join('sites', 'channel_configs.serial_number', '=', 'sites.serial_number')->get();

        return response()->json($channel, 200);
    }

    public function viewcreateClient(Request $request)
    {

        return view('super_admin.createClient');
    }

    public function viewTripLog(Request $request)
    {
        $allClient = User::where('role', 'Client admin')->where('master_role', 1)->get();
        return view('super_admin.cbtriplog', compact('allClient'));
    }

    public function loadTripLog(Request $request)
    {
        $status = (!empty($_GET["status"])) ? ($_GET["status"]) : ('');
        $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
        $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
        $ut = (!empty($_GET["selectclient"])) ? ($_GET["selectclient"]) : ('');
        if ($status == 'All' && $ut == 'All') {
            $getReport = DB::table('sites')
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('site_reports.alarm', 'LIKE', '%CB Tripped%')
                ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
            // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                ->select([
                    'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                ])
                ->orderBy('site_reports.created_at', 'DESC')
                ->orderBy('site_reports.alarm', 'ASC')
                ->get();
        } else if ($status == 'All' && $ut != 'All') {
            $getReport = DB::table('sites')->where('client_id', $ut)
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('site_reports.alarm', 'LIKE', '%CB Tripped%')
                ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
            // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                ->select([
                    'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                ])
                ->orderBy('site_reports.created_at', 'DESC')
                ->orderBy('site_reports.alarm', 'ASC')
                ->get();
        } else if ($status != 'All' && $ut != 'All') {
            $getReport = DB::table('sites')->where('client_id', $ut)
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('site_reports.alarm', 'LIKE', '%CB Tripped%')
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
        } else if ($status != 'All' && $ut == 'All') {
            $getReport = DB::table('sites')
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('site_reports.status', '=', $status)
                ->where('site_reports.alarm', 'LIKE', '%CB Tripped%')
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

        return DataTables::of($getReport)->make(true);
    }

    public function viewcreateBU(Request $request)
    {
        $clients = User::where('role', 'Client admin')->where('master_role', 1)
            ->select([
                'id', 'name',
            ])->get();

        return view('super_admin.createBU', compact('clients'));
    }

    public function viewcreateINJ(Request $request)
    {
        $clients = User::where('role', 'Client admin')->where('master_role', 1)
            ->select([
                'id', 'name',
            ])->get();

        return view('super_admin.createINJ', compact('clients'));
    }

    public function createClient(Request $request)
    {
        if ($request->has('profile_image')) {
            // Get image file
            $image = $request->file('profile_image');
            // Make a image name based on user name and current timestamp

            $imgname = str_replace(' ', '', $request->name);

            // Define folder path
            $folder = '';
            // Make a file path where image will be stored [ folder path + file name + file extension]
            $filePath = 'img/profile/' . $imgname . '.' . $image->getClientOriginalExtension();
            // Upload image

            $this->uploadOne($image, $folder, 'public', $imgname);
            // Set user profile image path in database to filePath

        }

        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $createClient = User::create([
            'name' => strtoupper($request->name),
            'email' => $request->loginemail,
            'role' => 'Client admin',
            'phone_number' => $phone_number,
            'address' => $request->address,
            'image' => $filePath,
            'password' => Hash::make($request->password),
            'owner_id' => Auth::user()->id,
            'dash_label1' => $request->level1,
            'dash_label2' => $request->level2,
            'ctrl_auth' => 0,
            'activate' => 1,
            'master_role' => 1,
        ]);

        $dash = Dashboards::create([
            'name' => strtoupper($request->name),
            'master_id' => $createClient->id,
        ]);
        $dash->save();
        $createClient->dashboard_id = $dash->id;
        $createClient->save();
        return back()->with('success', 'Account created successfully!');
    }
    public function createBU(Request $request)
    {
        if ($request->has('profile_image')) {
            // Get image file
            $image = $request->file('profile_image');
            // Make a image name based on user name and current timestamp

            $imgname = str_replace(' ', '', $request->name);

            // Define folder path
            $folder = '';
            // Make a file path where image will be stored [ folder path + file name + file extension]
            $filePath = 'img/profile/' . $imgname . '.' . $image->getClientOriginalExtension();
            // Upload image

            $this->uploadOne($image, $folder, 'public', $imgname);
            // Set user profile image path in database to filePath
        }
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $master = User::findOrfail($request->clientid);
        $createBU = User::create([
            'name' => strtoupper($request->name),
            'email' => $request->loginemail,
            'role' => 'BU admin',
            'phone_number' => $phone_number,
            'address' => $request->address,
            'image' => $filePath,
            'password' => Hash::make($request->password),
            'owner_id' => $request->clientid,
            'dash_label1' => $master->dash_label1,
            'dash_label2' => $master->dash_label2,
            'ctrl_auth' => 0,
            'activate' => 1,
            'master_role' => 1,
        ]);

        $dash = Dashboards::create([
            'name' => strtoupper($request->name),
            'master_id' => $createBU->id,
        ]);
        $dash->save();
        $createBU->dashboard_id = $dash->id;
        $createBU->save();
        return back()->with('success', 'Account created successfully!');

    }

    public function createINJ(Request $request)
    {
        if ($request->has('profile_image')) {
            // Get image file

            $image = $request->file('profile_image');
            // Make a image name based on user name and current timestamp

            $imgname = str_replace(' ', '', $request->name);

            // Define folder path
            $folder = '';
            // Make a file path where image will be stored [ folder path + file name + file extension]
            $filePath = 'img/profile/' . $imgname . '.' . $image->getClientOriginalExtension();
            // Upload image

            $this->uploadOne($image, $folder, 'public', $imgname);
            // Set user profile image path in database to filePath
        }
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $master = User::findOrfail($request->clientid);
        $createINJ = User::create([
            'name' => strtoupper($request->name),
            'email' => $request->loginemail,
            'role' => 'INJ admin',
            'phone_number' => $phone_number,
            'address' => $request->address,
            'image' => $filePath,
            'password' => Hash::make($request->password),
            'owner_id' => $request->clientid,
            'dash_label1' => $master->dash_label1,
            'dash_label2' => $master->dash_label2,
            'ctrl_auth' => "0",
            'activate' => 1,
            'master_role' => 1,
        ]);

        $dash = Dashboards::create([
            'name' => strtoupper($request->name),
            'master_id' => $createINJ->id,
        ]);
        $dash->save();
        $createINJ->dashboard_id = $dash->id;
        $createINJ->save();
        return back()->with('success', 'Account created successfully!');

    }

    public function deleteClient(Request $request)
    {

        $client = User::findOrfail($request->id);
        $client->delete();

        $dash = Dashboards::where("master_id", $request->id)->first();
        $dash->delete();
        return response()->json(['status' => true, 'message' => "Client deleted successfully."]);
    }

    public function editClient(Request $request)
    {
        $editUser = User::findOrfail($request->id);
        // $editSite->site_number               = $request->site_number;
        $editUser->name = $request->name;
		if($request->enable == "1"){
            $editUser->ctrl_auth = $request->enable;
		}
		else{
			 $editUser->ctrl_auth = "0";
		}
        $number = explode('-', $request->number);
        $phone_number = $number[1];
        $editUser->phone_number = $phone_number;
        $editUser->address = $request->address;

        $editUser->email = $request->email;

        $editUser->save();

        if ($editUser) {
            return response()->json([
                "status" => 200,
                "message" => "Client updated successfully",
                "id" => $request->id,
            ]);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Failed to update client Info",
            ]);
        }
    }
    public function getClient($id)
    {

        $clients = User::where('users.id', $id)
            ->select([
                'users.id', 'users.name', 'users.email', 'users.phone_number', 'users.address', 'users.ctrl_auth', 'activate'])->first();

        // $name = User::where('id', $id)->value("name");
        // $clientactivate = GlobalSetting::where('name', $name)->first();
        // if ($clientactivate) {
        //     $clients->setting = $clientactivate->setting['activate'];
        // } else {
        //     $clients->setting = '1';
        // }
        return response()->json($clients, 200);

    }

    public function getdtsites()
    {
        $user_info = User::where('role', 'Client admin')->where('master_role', 1)->get();
        return view('super_admin.installedsites', compact('user_info'));
    }

    public function loadsites()
    {
        $id = (!empty($_GET["id"])) ? ($_GET["id"]) : ('');

        if ($id == 'All' || $id = "") {
            $getTableData = DB::table('sites')
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
                ->join('users', 'sites.client_id', '=', 'users.id')
                ->select([
                    'sites.id', 'sites.serial_number as SerialNo', 'sites.name as siteName', 'details.updated_at as installdate',
                    'details.updated_at as lastupdate', 'sites.phone_number as simNo', 'users.name as client',
                    'locations.long', 'locations.lat',
                ])
                ->get();

        } else {
            $name = (!empty($_GET["name"])) ? ($_GET["name"]) : ('');
            $id = User::where('name', $name)->value('id');
            $getTableData = DB::table('sites')->where('sites.client_id', '=', $id)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
                ->join('users', 'sites.client_id', '=', 'users.id')
                ->select([
                    'sites.id', 'sites.serial_number as SerialNo', 'sites.name as siteName', 'details.updated_at as installdate',
                    'details.updated_at as lastupdate', 'sites.phone_number as simNo', 'users.name as client',
                    'locations.long', 'locations.lat',
                ])
                ->get();

        }
        return DataTables::of($getTableData)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<a class="edit" data-toggle="modal" data-target="#modal-edit-site" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-info"></i></a>
                    &nbsp; &nbsp;
                    <a class="delete" data-toggle="modal"  data-name="' . $row->siteName . '" data-target="#modal-delete-api" data-id="' . $row->id . '" data-original-title="delete"><span class="fa fa-trash  text-danger"></span></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function deletesites(Request $request)
    {
        $idreq = $request->ids;
        $ids = explode(',', $idreq);
        foreach ($ids as $id) {
            # code...
            $this->delete($id);
        }

        return response()->json(['status' => true, 'message' => "Site deleted successfully."]);
    }

    public function deletesite(Request $request)
    {
        $id = $request->ids;
        $this->delete($id);
        return response()->json(['status' => true, 'message' => "Site deleted successfully."]);
    }

    public function getSite($id)
    {
        $getSite = Site::find($id);

        return response()->json($getSite, 200);
    }

    public function editSite(Request $request)
    {
        $editSite = Site::findOrfail($request->id);
        // $editSite->site_number               = $request->site_number;
        $editSite->name = $request->name;

        if (Auth::user()->role == "Super admin") {
            $number = explode('-', $request->number);
            $phone_number = $number[1];
            $editSite->ctrl_enable = $request->enable;
            $editSite->phone_number = $phone_number;
        }
        $editSite->save();

        if ($editSite) {
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

    public function delete($id)
    {
        $site = Site::findOrFail($id);
        $serial_number = $site->serial_number;
        if ($site) {
            $site->delete();
        }

        $alarmstatus = Alarmstatus::where('site_id', $id)->first();
        if ($alarmstatus) {
            $alarmstatus->delete();
        }
        $analogsetting = AnalogSetting::where('serial_number', $serial_number)->first();
        if ($analogsetting) {
            $analogsetting->delete();
        }
        $analogunit = AnalogUnit::where('serial_number', $serial_number)->first();
        if ($analogunit) {
            $analogunit->delete();
        }
        $analogvalue = AnalogValue::where('serial_number', $serial_number)->first();
        if ($analogvalue) {
            $analogvalue->delete();
        }
        $channelconfig = ChannelConfig::where('serial_number', $serial_number)->first();
        if ($channelconfig) {
            $channelconfig->delete();
        }
        $control = Control::where('site_id', $id)->first();
        if ($control) {
            $control->delete();
        }

        $ctrlfb = CtrlFeedback::where('serial_number', $serial_number)->first();
        if ($ctrlfb) {
            $ctrlfb->delete();
        }
        $details = Detail::where('serial_number', $serial_number)->first();
        if ($details) {
            $details->delete();
        }
        $device = Device::where('serial_number', $serial_number)->first();
        if ($device) {
            $device->delete();
        }
        try {
            //code...
            DownLiveSite::where('serial_number', $serial_number)->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
        $label = Label::where('serial_number', $serial_number)->first();
        if ($label) {
            $label->delete();
        }
        $location = Location::where('serial_number', $serial_number)->first();
        if ($location) {
            $location->delete();
        }
        $power = Power::where('serial_number', $serial_number)->first();
        if ($power) {
            $power->delete();
        }
        $sitestatus = SiteStatus::where('serial_number', $serial_number)->first();
        if ($sitestatus) {
            $sitestatus->delete();
        }
        try {
            //code...
            SiteReport::where('site_id', $id)->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function clientSites($id)
    {
        $client = User::where('id', $id)->first();
        $users = User::where('owner_id', $id)->where('role', 'BU admin')->get();
        $sitesum = Site::where('client_id', $id)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')->count();
        $liveSite = Site::where('client_id', $id)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('DT_status', '1')->count();
        $downSite = Site::where('client_id', $id)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('DT_status', '!=', '1')->count();
        $userinfo['info'] = $users;
        $userinfo['sum'] = count($users);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;

        return view('super_admin.client_details', compact('userinfo', 'users', 'client'));
    }
    public function loadsite($clientid)
    {
        $id = (!empty($_GET["id"])) ? ($_GET["id"]) : ('');

        if ($id == 'All' || $id = "") {
            $getTableData = DB::table('sites')->where('sites.client_id', '=', $clientid)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select([
                    'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                    'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                ])
                ->get();

        } else {
            $name = (!empty($_GET["name"])) ? ($_GET["name"]) : ('');
            $id = User::where('name', $name)->value('id');
            $getTableData = DB::table('sites')->where('sites.user_id', '=', $id)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select([
                    'sites.site_number', 'sites.serial_number', 'sites.name', 'details.a1', 'details.a2', 'site_status.DT_status',
                    'site_status.alarm_status', 'site_status.Up_A', 'site_status.Up_B', 'site_status.Up_C', 'site_status.Up_D', 'down_live_sites.*',
                ])
                ->get();

        }
        return DataTables::of($getTableData)
            ->addIndexColumn()
            ->addColumn('action', 'action')
            ->rawColumns(['action'])
            ->make(true);
    }

    public function siteSummary(Request $request)
    {
        $clientid = $request->clientid;
        $id = $request->id;
        $summarydata = array();
        $index = 0;
        if ($id == "All") {
            $user_info = User::where('role', 'BU admin')->where('owner_id', $clientid)->where('master_role', 1)
                ->get();
            foreach ($user_info as $key => $value) {
                $sitesum = Site::where('sites.user_id', $value->id)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')->count();
                $liveSite = SiteStatus::where('user_id', $value->id)->where('DT_status', '1')->count();
                $downSite = SiteStatus::where('user_id', $value->id)
                    ->where('DT_status', '!=', '1')->count();
                $summarydata[$index] = $value;
                $summarydata[$index]->name = $value->name;
                $summarydata[$index]->sitesum = $sitesum;
                $summarydata[$index]->livesite = $liveSite;
                $summarydata[$index]->downsite = $downSite;
                $index++;
            }
        } else {
            $user_info = User::where('id', $id)->first();
            $sitesum = Site::where('sites.user_id', $id)
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')->count();
            $liveSite = SiteStatus::where('user_id', $id)->where('DT_status', '1')->count();
            $downSite = SiteStatus::where('user_id', $id)
                ->where('DT_status', '!=', '1')->count();
            $summarydata[$index] = $user_info;
            $summarydata[$index]->name = $user_info->name;
            $summarydata[$index]->sitesum = $sitesum;
            $summarydata[$index]->livesite = $liveSite;
            $summarydata[$index]->downsite = $downSite;

        }
        return Response::json($summarydata);
    }
    public function clientSummary(Request $request)
    {
        $id = $request->id;
        $summarydata = array();
        $index = 0;
        if ($id == "All") {
            $user_info = User::where('role', 'Client admin')
				->where('master_role', 1)
                ->get();
            foreach ($user_info as $key => $value) {
                $sitesum = Site::where('sites.client_id', $value->id)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')->count();
                $liveSite = Site::where('sites.client_id', $value->id)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('DT_status', '1')->count();
                $downSite = Site::where('sites.client_id', $value->id)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '1')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('DT_status', '!=', '1')->count();
                $summarydata[$index] = $value;
                $summarydata[$index]->id = $value->id;
                $summarydata[$index]->name = $value->name;
                $summarydata[$index]->sitesum = $sitesum;
                $summarydata[$index]->livesite = $liveSite;
                $summarydata[$index]->downsite = $downSite;
                $index++;
            }
        } else {
            $user_info = User::where('id', $id)->first();
            $sitesum = Site::where('sites.client_id', $id)->count();
            $liveSite = Site::where('sites.client_id', $id)
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('DT_status', '1')->count();
            $downSite = Site::where('sites.client_id', $id)
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('DT_status', '!=', '1')->count();
            $summarydata[$index] = $user_info;
            $summarydata[$index]->id = $user_info->id;
            $summarydata[$index]->name = $user_info->name;
            $summarydata[$index]->sitesum = $sitesum;
            $summarydata[$index]->livesite = $liveSite;
            $summarydata[$index]->downsite = $downSite;

        }
        return Response::json($summarydata);
    }
    public function loadHV(Request $request)
    {
        $id = $request->id;

        if ($id == 'All' || $id = "") {
            $masterid = Dashboards::where("id", Auth::user()->dashboard_id)->value("master_id");
            $getTableData = DB::table('sites')
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->where('details.d1', '!=', 'N')

                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->where('devices.device_category_id', '2')

                ->select([
                    'sites.id as siteid', 'sites.site_number', 'sites.serial_number', 'sites.name', 'details.*',
                ])
                ->get();

        } else {
            $masterid = Dashboards::where("id", $id)->value("master_id");
            $getTableData = DB::table('sites')->where('sites.client_id', '=', $masterid)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->where('details.d1', '!=', 'N')

                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->where('devices.device_category_id', '2')

                ->select([
                    'sites.id as siteid', 'sites.site_number', 'sites.serial_number', 'sites.name', 'details.*',
                ])
                ->get();
        }
        return DataTables::of($getTableData)
            ->addIndexColumn()
            ->addColumn('action', 'actionInj')
            ->rawColumns(['action'])
            ->make(true);

    }

    public function clientHVSummary(Request $request)
    {
        $id = $request->id;
        $summarydata = array();
        $index = 0;
        if ($id == "All") {
            $user_info = User::where('role', 'Client admin')
                ->join('sites', 'sites.client_id', '=', 'users.id')
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '2')->select([
                'users.*',
            ])->get();
            foreach ($user_info as $key => $value) {
                $sitesum = Site::where('sites.client_id', $value->id)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '2')->count();
                $liveSite = Site::where('sites.client_id', $value->id)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '2')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('DT_status', '2')->count();
                $downSite = Site::where('sites.client_id', $value->id)
                    ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                    ->where('device_category_id', '2')
                    ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                    ->where('DT_status', '!=', '2')->count();
                $summarydata[$index] = $value;
                $summarydata[$index]->id = $value->id;
                $summarydata[$index]->name = $value->name;
                $summarydata[$index]->sitesum = $sitesum;
                $summarydata[$index]->livesite = $liveSite;
                $summarydata[$index]->downsite = $downSite;
                $index++;
            }
        } else {
            $user_info = User::where('id', $id)->first();
            $sitesum = Site::where('sites.client_id', $id)->count();
            $liveSite = Site::where('sites.client_id', $id)
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('DT_status', '1')->count();
            $downSite = Site::where('sites.client_id', $id)
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('DT_status', '!=', '1')->count();
            $summarydata[$index] = $user_info;
            $summarydata[$index]->id = $user_info->id;
            $summarydata[$index]->name = $user_info->name;
            $summarydata[$index]->sitesum = $sitesum;
            $summarydata[$index]->livesite = $liveSite;
            $summarydata[$index]->downsite = $downSite;

        }
        return Response::json($summarydata);
    }
    public function sitePreview($id)
    {

        $user_info = User::where('owner_id', $id)->where('role', 'BU admin')->get();
        $sitesum = Site::where('client_id', $id)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')->count();
        $liveSite = Site::where('client_id', $id)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('DT_status', '1')->count();
        $downSite = Site::where('client_id', $id)
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->where('DT_status', '!=', '1')->count();
        $userinfo['info'] = $user_info;
        $userinfo['sum'] = count($user_info);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        return Response::json($userinfo);
    }

    public function dashboardSite()
    {

        $user_info = User::where('role', 'Client admin')->where('master_role', 1)->get();
        $sitesum = Site::join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')->count();
        $activated = SiteStatus::where('DT_status', '1')->orWhere('DT_status', '0')
            ->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '1')->count();
        $current = Carbon::now();
        $getTimelapse = $current->subDays(10);
        $nottransmit = Detail::whereDate('details.updated_at', '<=', $getTimelapse)
            ->join('devices', 'devices.serial_number', '=', 'details.serial_number')
            ->where('device_category_id', '1')->count();
        $liveSite = SiteStatus::where('DT_status', '1')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '1')->count();
        $downSite = SiteStatus::where('DT_status', '0')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '1')->count() + SiteStatus::where('DT_status', 'N')
            ->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '1')->count();

        $current = Carbon::now();
        $getTimelapse = $current->subDays(30);
        $fault = DB::table('site_reports')->where('site_reports.status', 'Active')->whereDate('site_reports.created_at', '<=', $getTimelapse)
            ->Where("site_reports.alarm", "!=", "SITE DOWN")
            ->join('sites', 'sites.id', '=', 'site_reports.site_id')
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('devices.device_category_id', '1')
            ->select(['site_reports.site_id', 'sites.user_id', 'sites.client_id', 'sites.name as SiteName', 'site_reports.alarm as alarm', 'site_reports.duration', 'site_reports.created_at'])
            ->get()->unique('SiteName')->count();
        $userinfo['client'] = $user_info;
        $userinfo['sum'] = count($user_info);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        $userinfo['active'] = $activated;
        $userinfo['notactive'] = $nottransmit;
        $userinfo['faultsite'] = $fault;
        return Response::json($userinfo);
    }

    public function dashboardHVSite()
    {

        $user_info = User::where('role', 'Client admin')->get();
        $sitesum = Site::join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '2')->count();
        $activated = SiteStatus::where('DT_status', '1')->orWhere('DT_status', '0')
            ->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count();
        $notactivated = SiteStatus::where('DT_status', 'N')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count();
        $liveSite = SiteStatus::where('DT_status', '1')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count();
        $downSite = SiteStatus::where('DT_status', '0')->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count() + SiteStatus::where('DT_status', 'N')
            ->join('devices', 'devices.serial_number', '=', 'site_status.serial_number')
            ->where('device_category_id', '2')->count();
        $userinfo['client'] = $user_info;
        $userinfo['sum'] = count($user_info);
        $userinfo['site_sum'] = $sitesum;
        $userinfo['livesite'] = $liveSite;
        $userinfo['downsite'] = $downSite;
        $userinfo['active'] = $activated;
        $userinfo['notactive'] = $notactivated;
        return Response::json($userinfo);
    }
    public function viewNotTransmit()
    {
        return view('super_admin.nottranmitsites');
    }
    public function NotTransmit(Request $request)
    {
        $daycnt = $request->value;
        $nottransmit = array();
        $index = 0;
        $current = Carbon::now();
        $getTimelapse = $current->subDays($daycnt);
        $sitestatus = Detail::whereDate('details.updated_at', '<=', $getTimelapse)
            ->join('sites', 'sites.serial_number', '=', 'details.serial_number')
            ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
            ->join('devices', 'devices.serial_number', '=', 'details.serial_number')
			->where('devices.device_category_id', '1')
            ->join('locations', 'sites.serial_number', '=', 'locations.serial_number')
            ->select([
                'sites.user_id', 'sites.client_id', 'sites.name as SiteName', 'sites.serial_number as SerialNo',
                 'details.updated_at as lastupdate', 'site_status.DT_status as Status',  'locations.long as long', 'locations.lat as lat'
            ])->get();
        foreach ($sitestatus as $key => $value) {
            $nottransmit[$index] = $value;
            $bu = User::where('id', $value->user_id)->value('name');
            $client = User::where('id', $value->client_id)->value('name');
            $nottransmit[$index]->BU = $bu;
            $nottransmit[$index]->Client = $client;
            $index++;
        }
        return DataTables::of($nottransmit)
            ->addIndexColumn()
            ->make(true);
    }

    public function viewFaultsites()
    {
        return view('super_admin.faultsites');
    }
    public function getFaultySites(Request $request)
    {
        $daycnt = $request->value;
        $faultsite = array();
        $index = 0;
        $current = Carbon::now();
        $getTimelapse = $current->subDays($daycnt);
        // dd($getTimelapse);
        $getReport = DB::table('site_reports')->where('site_reports.status', 'Active')->whereDate('site_reports.created_at', '<=', $getTimelapse)
            ->Where("alarm", "!=", "SITE DOWN")
            ->join('sites', 'sites.id', '=', 'site_reports.site_id')
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
			->where('devices.device_category_id', '1')
            ->join('locations', 'locations.serial_number', '=', 'devices.serial_number')
            ->select([
                'sites.user_id', 'sites.client_id', 'sites.name as SiteName', 'site_reports.alarm as alarm',
				'site_reports.duration','site_reports.created_at', 
				'locations.long as long', 'locations.lat as lat'
            ])
            ->get()
            ->unique('alarm');
        foreach ($getReport as $key => $value) {
            $faultsite[$index] = $value;
            $bu = User::where('id', $value->user_id)->value('name');
            $client = User::where('id', $value->client_id)->value('name');
            $faultsite[$index]->BU = $bu;
            $faultsite[$index]->Client = $client;
            $index++;
        }

        return DataTables::of($faultsite)
            ->addIndexColumn()
            ->make(true);
    }

    public function DownSiteAnalysis()
    {
        $summarydata = array();
        $index = 0;
        $user_info = User::where('role', 'BU admin')->where('owner_id', Auth::user()->id)
            ->get();
        foreach ($user_info as $key => $value) {
            $getReport = DB::table('sites')->where('user_id', $value->id)
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('status', 'Active')
                ->where('alarm', 'SITE DOWN')
                ->whereDate('site_reports.updated_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('site_reports.updated_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select([
                    'sites.id', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                ])
                ->orderBy('site_reports.duration', 'DESC')
                ->get();
            $summarydata[$index] = $value;
            $summarydata[$index]->name = $value->name;
            $summarydata[$index]->report = $getReport;
            $index++;
        }

        return Response::json($summarydata);
    }

    public function getAccountSetting($id)
    {
        $getAccountPassword = User::find(Auth::user()->id);

        return response()->json($getAccountPassword, 200);
    }
    public function editAccountSetting(Request $request)
    {

        $newpass = $request->newpassword;
        $confirmpass = $request->confirmpassword;
        if ($newpass != $confirmpass) {
            return response()->json([
                "status" => 500,
                "message" => "Passwords not matched",
            ]);

        }
        $editAccountPassword = User::find(Auth::user()->id);

        $number = explode('-', $request->number);
        $phone_number = $number[1];
        $editAccountPassword->phone_number = $phone_number;
        $editAccountPassword->address = $request->address;

        if ($newpass != "") {
            $editAccountPassword->password = Hash::make($request->newpassword);
        }

        $editAccountPassword->save();

        if ($editAccountPassword) {
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

    //global settings

    public function globalCommunicationsetting(Request $request)
    {
        $settingemail = $request->enablemail;
        $settingsms = $request->enablesms;
        $settingvalue = array('sms' => $settingsms, 'email' => $settingemail);

        $communicationsetting = GlobalSetting::where('name', 'globalcommunication')->first();
        if ($communicationsetting) {
            $communicationsetting->setting = $settingvalue;
            $communicationsetting->save();
        } else {
            $communicationsetting = GlobalSetting::create([
                'name' => "globalcommunication",
                'setting' => $settingvalue,

            ]);
            $communicationsetting->save();
        }

    }
    public function getglobalCommunicationsetting()
    {
        $communicationsetting = GlobalSetting::where('name', 'globalcommunication')->first();
        if ($communicationsetting) {
            return response()->json(["status" => 200, $communicationsetting->setting]);
        } else {
            return response()->json(["status" => 204, "message" => "no setting"]);
        }

    }

    public function clientactivate(Request $request)
    {
        $name = $request->name;
        $setting = $request->clientenable;
        $settingvalue = array('activate' => $setting);

        $clientactivate = GlobalSetting::where('name', $name)->first();
        if ($clientactivate) {
            $clientactivate->setting = $settingvalue;
            $clientactivate->save();
        } else {
            $clientactivate = GlobalSetting::create([
                'name' => $name,
                'setting' => $settingvalue,

            ]);
            $clientactivate->save();
        }

    }

    public function loaddashboard()
    {
        $user_info = User::select([
            'id', 'name',
        ])
            ->get();

        foreach ($user_info as $key => $value) {
            $dash = Dashboards::create([
                'name' => $value->name,
                'master_id' => $value->id,
            ]);
            $dash->save();
        }

        return response()->json(["status" => 200, "message" => "load complete"]);

    }

    public function attachdashboardtouser()
    {
        $dash = Dashboards::get();

        foreach ($dash as $key => $value) {
            $user_info = User::where("id", $value->master_id)
                ->first();
            $user_info->dashboard_id = $value->id;
            $user_info->save();
        }

        return response()->json(["status" => 200, "message" => "user update complete"]);

    }

    public function generateLogin()
    {
        $alluser = User::where('role', 'Client admin')->where('master_role', '1')->get();
        return view('super_admin.loginreport', compact('alluser'));
    }
    //report generate
	
    public function loginReport(Request $request)
    {
		$start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
        $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
        $section = (!empty($_GET["selectut"])) ? ($_GET["selectut"]) : ('');
        $loginActivities = LoginActivities::where('login_count', '>', 0)->orderBy('id', 'DESC')
        ->where("ip_address","!=", "41.184.45.226")
        ->whereDate('login_activities.updated_at', '>=', date($start_date) . ' 00:00:00')
        ->whereDate('login_activities.updated_at', '<=', date($end_date) . ' 23:59:59')
        ->join('users', 'login_activities.email', '=', 'users.email')
        ->where('users.owner_id',$section)
        ->where('users.role',"BU admin")
        ->select(['login_activities.*',
        ])
        ->get();
		foreach ($loginActivities as $key => $value) {
			if($value->updated_at <  date($start_date) . ' 00:00:00'){
				unset($loginActivities[$key]);
				return;
			}
			$contains = Str::contains($value->email, 'susejgroup.net');
			if($contains) {
				unset($loginActivities[$key]);
			}
		}
		
        return datatables()->of($loginActivities)->make(true);
    }

    public function notificationReport(Request $request)
    {
        $status = (!empty($_GET["status"])) ? ($_GET["status"]) : ('');
        $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
        $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
        $section = (!empty($_GET["selectut"])) ? ($_GET["selectut"]) : ('');
        if ($status == 'All' && $section == 'All') {
            $getReport = DB::table('sites')->where('client_id', $section)
            ->join('users', 'users.id', '=', 'sites.user_id')
            ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
            ->where('device_category_id', '1')
            ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
            ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
            ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
            ->select([
                'sites.id','users.name as bu', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
            ])
            ->orderBy('site_reports.created_at', 'DESC')
            ->orderBy('site_reports.alarm', 'ASC')
            ->get();
        } else if ($status == 'All' && $section != 'All') {
            $getReport = DB::table('sites')
                ->where('client_id', $section)
                ->join('users', 'users.id', '=', 'sites.user_id')
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
                ->select([
                    'sites.id','users.name as bu', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                ])
                ->orderBy('site_reports.created_at', 'DESC')
               
                ->get();
        } else if ($status != 'All' && $section != 'All') {
            $getReport = DB::table('sites')
                ->where('client_id', $section)
                ->join('users', 'users.id', '=', 'sites.user_id')
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('site_reports.status', '=', $status)
                ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
            // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                ->select([
                    'sites.id','users.name as bu', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                ])
                ->orderBy('site_reports.created_at', 'DESC')
          
                ->get();
        } else if ($status != 'All' && $section == 'All') {
            $getReport = DB::table('sites')->where('client_id', $section)
                ->join('users', 'users.id', '=', 'sites.user_id')
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('site_reports.status', '=', $status)
                ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
            // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                ->select([
                    'sites.id','users.name as bu', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                ])
                ->orderBy('site_reports.created_at', 'DESC')
           
                ->get();
        } else {
            $getReport = DB::table('sites')->where('client_id', $section)
                ->join('users', 'users.id', '=', 'sites.user_id')
                ->join('devices', 'devices.serial_number', '=', 'sites.serial_number')
                ->where('device_category_id', '1')
                ->join('site_reports', 'sites.id', '=', 'site_reports.site_id')
                ->where('site_reports.status', '=', $status)
                ->whereDate('site_reports.created_at', '>=', date($start_date) . ' 00:00:00')
                ->whereDate('site_reports.created_at', '<=', date($end_date) . ' 23:59:59')
            // ->whereRaw('site_reports.updated_at >= DATE_ADD(site_reports.created_at, INTERVAL 10 MINUTE)')
                ->select([
                    'sites.id','users.name as bu', 'sites.name', 'site_reports.created_at AS date', 'site_reports.alarm', 'site_reports.duration', 'site_reports.status',
                ])
                ->orderBy('site_reports.created_at', 'DESC')
               
                ->get();
        }
        return datatables()->of($getReport)->make(true);
    }

}