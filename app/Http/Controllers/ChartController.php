<?php

namespace App\Http\Controllers;

use App\Detail;
use App\Site;
use App\SiteStatus;
use DB;
use Illuminate\Http\Request;
use Response;

class ChartController extends Controller
{

    public function chart()
    {
        return view('chart');
    }
    public function chartapi($serial_number)
    {
        $userinfo['detail'] = Site::where('serial_number', $serial_number)->first();
        $userinfo['status'] = SiteStatus::where('serial_number', $serial_number)->value('DT_status');
        $userinfo['analog_values'] = Detail::select('a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13', 'a14', 'a15', 'a16', 'a17', 'a18', 'a19', 'a20', 'a21', 'a22', 'a23', 'a24')
            ->where('serial_number', $serial_number)->orderBy('updated_at', 'desc')->first();
        $userinfo['digital_values'] = Detail::select('d1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8', 'd9', 'd10', 'd11', 'd12', 'd13', 'd14', 'd15', 'd16', 'd17', 'd18', 'd19', 'd20', 'd21', 'd22', 'd23', 'd24')
            ->where('serial_number', $serial_number)->orderBy('updated_at', 'desc')->first();
        return Response::json($userinfo);
    }

    public function viewINJChart(Request $request)
    {
        $id = $request->id;
        $getData = DB::table('sites')->where('sites.id', '=', $id)->first();
        // dd($getData->serial_number);
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
        return view('injection_chart', compact('getData', 'getDetails', 'getLabel', 'getStatus'));
    }
    public function loaddata(Request $request)
    {
        $id = $request->id;
        $getData = DB::table('sites')->where('sites.id', '=', $id)->first();

        $getDetails = DB::table('details')->where('details.serial_number', '=', $getData->serial_number)
            ->select([
                'details.d1', 'details.d2', 'details.d3', 'details.d4',
                'details.d5', 'details.d6', 'details.d7', 'details.d8',
                'details.d9', 'details.d10', 'details.d11', 'details.d12',
            ])
            ->first();
        $getStatus = DB::table('site_status')->where('site_status.serial_number', '=', $getData->serial_number)
            ->select([
                'site_status.DT_status',
            ])
            ->first();
        return response()->json([
            'feeders' => $getData->uprisers,
            'getDetails' => $getDetails,
            'getStatus' => $getStatus,

        ]);

    }

}