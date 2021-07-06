<?php

namespace App\Http\Controllers;

use App\ApiKey;
use App\ApiKeyAccessEvent;
use App\Helpers\HelperClass;
use App\Site;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;

class ApiController extends Controller
{

    const MESSAGE_ERROR_INVALID_NAME_FORMAT = 'Invalid name.  Must be a lowercase alphabetic characters, numbers and hyphens less than 255 characters long.';
    const MESSAGE_ERROR_NAME_ALREADY_USED = 'Name is unavailable.';

    public function ListApiKey(Request $request)
    {
        $keys = ApiKey::orderBy('name')->get();
        $rows = array();
        $index = 0;
        foreach ($keys as $key => $value) {
            # code...
            $status = $value->active ? 'Active' : 'Deactivated';
            $status = $value->trashed() ? 'Deleted' : $status;
            $user = User::where('id', $value->user_id)->value('name');
            $statusDate = $value->deleted_at ?: $value->updated_at;
            $requests = ApiKeyAccessEvent::where('api_key_id', $value->id)->count();

            $rows[$index] = $value;
            $rows[$index]->user = $user;
            $rows[$index]->key = $value->key;
            $rows[$index]->status = $status;
            $rows[$index]->date = date($statusDate->format('Y-m-d'));
            $rows[$index]->request = $requests;
            $index++;

        }

        if (request()->ajax()) {
            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a class="view" data-toggle="modal"  data-name="' . $row->name . '" data-target="#modal-view-api" data-id="' . $row->id . '"
                     data-placement="top" title="View" ><i class="fa fa-eye text-success"></i></a>
                     &nbsp; &nbsp;
                     <a class="delete"  title="Delete" data-name="' . $row->name . '"  data-toggle="modal" data-target="#modal-delete-api" data-id="' . $row->id . '" data-placement="top"><i class="fa fa-trash text-danger"></i></a>
                     &nbsp; &nbsp;
                     </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('super_admin.listapis');
    }

    public function keyDetails(Request $request)
    {

        $keyid = $request->id;

        $key = ApiKey::where('id', $keyid)->first();
        $rows = array();
        $status = $key->active ? 'Active' : 'Deactivated';
        $status = $key->trashed() ? 'Deleted' : $status;
        $user = User::where('id', $key->user_id)->value('name');
        $rows[0] = $key;
        $rows[0]->user = $user;
        $rows[0]->status = $status;

        return Response::json($rows);

    }
    public function DeleteApiKey(Request $request)
    {
        $id = $request->id;
        $name = ApiKey::where('id', $id)->value('name');
        $key = ApiKey::where('name', $name)->first();
        $key->delete();
        return response()->json([
            "Message" => 'Deleted key: ' . $name,
        ]);

    }
    public function getAllClient()
    {
        $client = User::where('role', 'Client admin')
            ->select(['users.name', 'users.id'])->get();
        foreach ($client as $key => $value) {
            $keys = ApiKey::where('name', $value->name)->where('deleted_at', null)->first();
            if ($keys) {
                unset($client[$key]);
            }
        }
        return Response::json($client);
    }
    public function DeactivateApiKey(Request $request)
    {
        $name = $request->name;
        $key = ApiKey::where('name', $name)->first();

        if (!$key->active) {
            return response()->json([

                "Message" => 'Key "' . $name . '" is already inactive',
            ]);

        }

        $key->active = 0;
        $key->save();

        return response()->json([
            "status" => $key->active,
            "Name" => $key->name,
            "Message" => "Deactivated key: " . $name,
        ]);

    }

    public function ActivateApiKey(Request $request)
    {
        $name = $request->name;
        $error = $this->validateName($name);
        $key = ApiKey::where('name', $name)->first();

        if ($key->active) {
            return response()->json([

                "Message" => 'Key "' . $name . '" is already active',
            ]);
        }

        $key->active = 1;
        $key->save();
        return response()->json([

            "status" => $key->active,
            "Name" => $key->name,
            "Message" => "Activated key: " . $name,
        ]);

    }
    public function generateKey(Request $request)
    {
        // dd($request);
        $name = $request->name;
        $user_id = $request->id;

        $apiKey = new ApiKey;

        $apiKey->user_id = $user_id;
        $apiKey->name = $name;
        $apiKey->key = ApiKey::generate();
        $apiKey->save();

        return response()->json([
            "Key" => $apiKey->key,
            "Name" => $apiKey->name,
            "Message" => "API key created",
        ]);
    }
    public function regenerateKey(Request $request)
    {
        $name = $request->name;
        $key_id = $request->id;
        $apiKey = ApiKey::where('name', $name)->first();
        if ($apiKey) {
            $apiKey->key = ApiKey::generate();
            $apiKey->save();
        }
        return response()->json([
            "Name" => $apiKey->name,
            "Key" => $apiKey->key,
            "Message" => "API key recreated",
        ]);
    }

    protected function validateName($name)
    {
        if (!ApiKey::isValidName($name)) {
            return self::MESSAGE_ERROR_INVALID_NAME_FORMAT;
        }
        if (ApiKey::nameExists($name)) {
            return self::MESSAGE_ERROR_NAME_ALREADY_USED;
        }
        return null;
    }

    public function getClientSite(Request $request)
    {

        $clientKey = $request->access_key;
        $user_id = ApiKey::getByKey($clientKey)->user_id;
        $client_name = User::where('id', $user_id)->value("name");
        if (Str::contains(strtoupper($client_name), 'ENUGU')) {
            $site = Site::where('client_id', $user_id)
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('site_status.DT_status', '!=', 'N')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->select([
                    'sites.id as Site-ID', 'bu_table.name as District', 'ut_table.name as Service_center',
                    'sites.trans_id as Transformer_ID', 'sites.trans_code AS Transformer_code',
                    'site_number AS Site-No', 'sites.name AS Site-Name',
                    'uprisers as no-of-uprisers', 'site_status.DT_status AS DT-status',
                ])

                ->paginate(50);

        } else if (Str::contains(strtoupper($client_name), 'IKEJA')) {
            $site = Site::where('client_id', $user_id)
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->where('site_status.DT_status', '!=', 'N')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->select([
                    'sites.id as Site-ID', 'bu_table.name as BU-name', 'ut_table.name as UT-name',
                    'site_number AS Site-No', 'sites.name AS Site-Name',
                    'uprisers as no-of-uprisers', 'site_status.DT_status AS DT-status',
                ])

                ->paginate(50);

        }

        if ($site) {
            return Response::json([
                "Status" => "200 Ok",
                "Message" => "Data retrieved",
                "Response" => $site,

            ]);
        } else {
            return Response::json([
                "error" => "404",
                "Message" => "incorrect parameter",
            ], 404);
        }

    }

    public function getAllClientSitesDetails(Request $request)
    {
        $clientKey = $request->access_key;
        $user_id = ApiKey::getByKey($clientKey)->user_id;
        $client_name = User::where('id', $user_id)->value("name");

        if (Str::contains(strtoupper($client_name), 'ENUGU')) {
            $site_info = Site::where('client_id', $user_id)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select(['sites.id as Site-ID', 'bu_table.name as District', 'ut_table.name as Service_center', 'site_number AS Site-No',
                    'sites.name AS Site-name', 'sites.trans_id as Transformer_ID', 'sites.trans_code AS Transformer_code',
                    'site_status.DT_status AS DT-status', 'uprisers as no-of-uprisers',
                    'site_status.Up_AStatus AS UpriserA-status',
                    'details.d1 AS UpriserA-phaseA', 'details.d2 AS UpriserA-phaseB', 'details.d3 AS UpriserA-phaseC',
                    'site_status.Up_BStatus AS UpriserB-status',
                    'details.d4 AS UpriserB-phaseA', 'details.d5 AS UpriserB-phaseB', 'details.d6 AS UpriserB-phaseC',
                    'site_status.Up_CStatus AS UpriserC-status',
                    'details.d7 AS  UpriserC-phaseA', 'details.d8 AS  UpriserC-phaseB', 'details.d9 AS  UpriserC-phaseC',
                    'site_status.Up_DStatus AS UpriserD-status',
                    'details.d10 AS  UpriserD-phaseA', 'details.d11 AS  UpriserD-phaseB', 'details.d12 AS UpriserD-phaseC',
                    'down_live_sites.up_time AS Today-Uptime', 'details.a4 AS Volt-phaseA', 'details.a5 AS Volt-phaseB', 'details.a6 AS Volt-phaseC',
                    'details.a7 AS Current-phaseA', 'details.a8 AS Current-phaseB', 'details.a9 AS Current-phaseC',
                    'down_live_sites.power AS Power', 'down_live_sites.energy AS Energy', 'details.updated_at AS Update-time',
                ])
                ->paginate(50);
//
        } else if (Str::contains(strtoupper($client_name), 'IKEJA')) {
            $site_info = Site::where('client_id', $user_id)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select(['sites.id as Site-ID', 'bu_table.name as BU-name', 'ut_table.name as UT-name', 'site_number AS Site-No',
                    'sites.name AS Site-name', 'uprisers as no-of-uprisers', 'site_status.DT_status AS DT-status', 'down_live_sites.up_time AS Today-Uptime',
                    'details.a4 AS Volt-phaseA', 'details.a5 AS Volt-phaseB', 'details.a6 AS Volt-phaseC',
                    'details.a7 AS Current-phaseA', 'details.a8 AS Current-phaseB', 'details.a9 AS Current-phaseC',
                    'down_live_sites.power AS Power', 'down_live_sites.energy AS Energy',
                    'INJstation as Injection-station', 'feeder as Feeder', 'HV_status as HV-status', 'details.updated_at AS Update-time',
                ])

                ->paginate(50);
        }

        if ($site_info) {
            return Response::json([
                "Status" => "200 Ok",
                "Message" => "Data retrieved",
                "Response" => $site_info,

            ]);
        } else {
            return Response::json([
                "error" => "404",
                "Message" => "incorrect parameter",
            ], 404);
        }
    }

    public function getSiteDetailswithid(Request $request)
    {

        $clientKey = $request->access_key;
        $user_id = ApiKey::getByKey($clientKey)->user_id;
        $site_id = $request->siteid;
        $client_name = User::where('id', $user_id)->value("name");

        if (Str::contains(strtoupper($client_name), 'ENUGU')) {
            $site_info = Site::where('sites.id', '=', $site_id)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select(['sites.id as Site-ID', 'bu_table.name as District', 'ut_table.name as Service_center', 'site_number AS Site-No',
                    'sites.name AS Site-name', 'sites.trans_id as Transformer_ID', 'sites.trans_code AS Transformer_code',
                    'site_status.DT_status AS DT-status', 'uprisers as no-of-uprisers',
                    'site_status.Up_AStatus AS UpriserA-status',
                    'details.d1 AS UpriserA-phaseA', 'details.d2 AS UpriserA-phaseB', 'details.d3 AS UpriserA-phaseC',
                    'site_status.Up_BStatus AS UpriserB-status',
                    'details.d4 AS UpriserB-phaseA', 'details.d5 AS UpriserB-phaseB', 'details.d6 AS UpriserB-phaseC',
                    'site_status.Up_CStatus AS UpriserC-status',
                    'details.d7 AS  UpriserC-phaseA', 'details.d8 AS  UpriserC-phaseB', 'details.d9 AS  UpriserC-phaseC',
                    'site_status.Up_DStatus AS UpriserD-status',
                    'details.d10 AS  UpriserD-phaseA', 'details.d11 AS  UpriserD-phaseB', 'details.d12 AS UpriserD-phaseC',
                    'down_live_sites.up_time AS Today-Uptime', 'details.a4 AS Volt-phaseA', 'details.a5 AS Volt-phaseB', 'details.a6 AS Volt-phaseC',
                    'details.a7 AS Current-phaseA', 'details.a8 AS Current-phaseB', 'details.a9 AS Current-phaseC',
                    'down_live_sites.power AS Power', 'down_live_sites.energy AS Energy', 'details.updated_at AS Update-time',
                ])
                ->first();

        } else if (Str::contains(strtoupper($client_name), 'IKEJA')) {
            $site_info = Site::where('sites.id', '=', $site_id)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select(['sites.id as Site-ID', 'bu_table.name as BU-name', 'ut_table.name as UT-name', 'site_number AS Site-No',
                    'sites.name AS Site-name', 'uprisers as no-of-uprisers', 'site_status.DT_status AS DT-status', 'down_live_sites.up_time AS Today-Uptime',
                    'details.a4 AS Volt-phaseA', 'details.a5 AS Volt-phaseB', 'details.a6 AS Volt-phaseC',
                    'details.a7 AS Current-phaseA', 'details.a8 AS Current-phaseB', 'details.a9 AS Current-phaseC',
                    'down_live_sites.power AS Power', 'down_live_sites.energy AS Energy',
                    'INJstation as Injection-station', 'feeder as Feeder', 'HV_status as HV-status', 'details.updated_at AS Update-time',
                ])
                ->first();
        }

        if ($site_info) {
            return Response::json([
                "Status" => "200 Ok",
                "Message" => "Data retrieved",
                "Response" => $site_info,

            ]);
        } else {
            return Response::json([
                "error" => "404",
                "Message" => "incorrect parameter",
            ], 404);
        }
    }

    public function getSiteDetailswithname(Request $request)
    {

        $clientKey = $request->access_key;
        $user_id = ApiKey::getByKey($clientKey)->user_id;
        $site_name = $request->sitename;
        $client_name = User::where('id', $user_id)->value("name");

        if (Str::contains(strtoupper($client_name), 'ENUGU')) {
            $site_info = Site::where('sites.name', '=', $site_name)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select(['sites.id as Site-ID', 'bu_table.name as District', 'ut_table.name as Service_center', 'site_number AS Site-No',
                    'sites.name AS Site-name', 'sites.trans_id as Transformer_ID', 'sites.trans_code AS Transformer_code',
                    'site_status.DT_status AS DT-status', 'uprisers as no-of-uprisers',
                    'site_status.Up_AStatus AS UpriserA-status',
                    'details.d1 AS UpriserA-phaseA', 'details.d2 AS UpriserA-phaseB', 'details.d3 AS UpriserA-phaseC',
                    'site_status.Up_BStatus AS UpriserB-status',
                    'details.d4 AS UpriserB-phaseA', 'details.d5 AS UpriserB-phaseB', 'details.d6 AS UpriserB-phaseC',
                    'site_status.Up_CStatus AS UpriserC-status',
                    'details.d7 AS  UpriserC-phaseA', 'details.d8 AS  UpriserC-phaseB', 'details.d9 AS  UpriserC-phaseC',
                    'site_status.Up_DStatus AS UpriserD-status',
                    'details.d10 AS  UpriserD-phaseA', 'details.d11 AS  UpriserD-phaseB', 'details.d12 AS UpriserD-phaseC',
                    'down_live_sites.up_time AS Today-Uptime', 'details.a4 AS Volt-phaseA', 'details.a5 AS Volt-phaseB', 'details.a6 AS Volt-phaseC',
                    'details.a7 AS Current-phaseA', 'details.a8 AS Current-phaseB', 'details.a9 AS Current-phaseC',
                    'down_live_sites.power AS Power', 'down_live_sites.energy AS Energy', 'details.updated_at AS Update-time',
                ])
                ->first();
        } else if (Str::contains(strtoupper($client_name), 'IKEJA')) {
            $site_info = Site::where('sites.name', '=', $site_name)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select(['sites.id as Site-ID', 'bu_table.name as BU-name', 'ut_table.name as UT-name', 'site_number AS Site-No',
                    'sites.name AS Site-name', 'uprisers as no-of-uprisers', 'site_status.DT_status AS DT-status', 'down_live_sites.up_time AS Today-Uptime',
                    'details.a4 AS Volt-phaseA', 'details.a5 AS Volt-phaseB', 'details.a6 AS Volt-phaseC',
                    'details.a7 AS Current-phaseA', 'details.a8 AS Current-phaseB', 'details.a9 AS Current-phaseC',
                    'down_live_sites.power AS Power', 'down_live_sites.energy AS Energy',
                    'INJstation as Injection-station', 'feeder as Feeder', 'HV_status as HV-status', 'details.updated_at AS Update-time',
                ])
                ->first();
        }

        if ($site_info) {
            return Response::json([
                "Status" => "200 Ok",
                "Message" => "Data retrieved",
                "Response" => $site_info,

            ]);
        } else {
            return Response::json([
                "Status" => "error",
                "Message" => "incorrect parameter",
            ], 404);
        }
    }
    public function getSiteDetailswithsitenum(Request $request)
    {

        $clientKey = $request->access_key;
        $user_id = ApiKey::getByKey($clientKey)->user_id;
        $sitenum = $request->sitenum;
        $client_name = User::where('id', $user_id)->value("name");

        if (Str::contains(strtoupper($client_name), 'ENUGU')) {
            $site_info = Site::where('sites.site_number', '=', $sitenum)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select(['sites.id as Site-ID', 'bu_table.name as District', 'ut_table.name as Service_center', 'site_number AS Site-No',
                    'sites.name AS Site-name', 'sites.trans_id as Transformer_ID', 'sites.trans_code AS Transformer_code',
                    'site_status.DT_status AS DT-status', 'uprisers as no-of-uprisers',
                    'site_status.Up_AStatus AS UpriserA-status',
                    'details.d1 AS UpriserA-phaseA', 'details.d2 AS UpriserA-phaseB', 'details.d3 AS UpriserA-phaseC',
                    'site_status.Up_BStatus AS UpriserB-status',
                    'details.d4 AS UpriserB-phaseA', 'details.d5 AS UpriserB-phaseB', 'details.d6 AS UpriserB-phaseC',
                    'site_status.Up_CStatus AS UpriserC-status',
                    'details.d7 AS  UpriserC-phaseA', 'details.d8 AS  UpriserC-phaseB', 'details.d9 AS  UpriserC-phaseC',
                    'site_status.Up_DStatus AS UpriserD-status',
                    'details.d10 AS  UpriserD-phaseA', 'details.d11 AS  UpriserD-phaseB', 'details.d12 AS UpriserD-phaseC',
                    'down_live_sites.up_time AS Today-Uptime', 'details.a4 AS Volt-phaseA', 'details.a5 AS Volt-phaseB', 'details.a6 AS Volt-phaseC',
                    'details.a7 AS Current-phaseA', 'details.a8 AS Current-phaseB', 'details.a9 AS Current-phaseC',
                    'down_live_sites.power AS Power', 'down_live_sites.energy AS Energy', 'details.updated_at AS Update-time',
                ])
                ->first();
        } else if (Str::contains(strtoupper($client_name), 'IKEJA')) {
            $site_info = Site::where('sites.site_number', '=', $sitenum)
                ->join('details', 'sites.serial_number', '=', 'details.serial_number')
                ->join('site_status', 'sites.serial_number', '=', 'site_status.serial_number')
                ->join('users as bu_table', 'bu_table.id', '=', 'sites.user_id')
                ->leftJoin('users as ut_table', 'ut_table.id', '=', 'sites.ut_id')
                ->join('devices', 'sites.serial_number', '=', 'devices.serial_number')
                ->join('down_live_sites', 'sites.serial_number', '=', 'down_live_sites.serial_number')
                ->whereDate('down_live_sites.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->whereDate('down_live_sites.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->select(['sites.id as Site-ID', 'bu_table.name as BU-name', 'ut_table.name as UT-name', 'site_number AS Site-No',
                    'sites.name AS Site-name', 'uprisers as no-of-uprisers', 'site_status.DT_status AS DT-status', 'down_live_sites.up_time AS Today-Uptime',
                    'details.a4 AS Volt-phaseA', 'details.a5 AS Volt-phaseB', 'details.a6 AS Volt-phaseC',
                    'details.a7 AS Current-phaseA', 'details.a8 AS Current-phaseB', 'details.a9 AS Current-phaseC',
                    'down_live_sites.power AS Power', 'down_live_sites.energy AS Energy',
                    'INJstation as Injection-station', 'feeder as Feeder', 'HV_status as HV-status', 'details.updated_at AS Update-time',
                ])
                ->first();
        }

        if ($site_info) {
            return Response::json([
                "Status" => "200 Ok",
                "Message" => "Data retrieved",
                "Response" => $site_info,

            ]);
        } else {
            return Response::json([
                "error" => "404",
                "Message" => "incorrect parameter",
            ], 404);
        }
    }



}