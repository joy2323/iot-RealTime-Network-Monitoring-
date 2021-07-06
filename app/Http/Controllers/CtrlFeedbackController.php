<?php

namespace App\Http\Controllers;

use App\Control;
use App\ControlLogs;
use App\CtrlFeedback;
use App\Helpers\HelperClass;
use App\Site;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;

class CtrlFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function controlfeedback($serialnumber)
    {

        $feedback = CtrlFeedback::where('ctrl_feedback.serial_number', $serialnumber)
            ->join('sites', 'sites.serial_number', '=', 'ctrl_feedback.serial_number')
            ->join('controls', 'sites.id', '=', 'controls.site_id')
            ->select(['ctrl_feedback.*', 'controls.*',
            ])->first();
        return response()->json($feedback);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $site = Site::where('serial_number', $request->serialnumber)->first();
        if (Auth::user()->role == "Client admin") {
            $user = $site->client_id;
        } else if (Auth::user()->role == "BU admin") {
            $user = $site->user_id;
        } else if (Auth::user()->role == "UT admin") {
            $user = $site->ut_id;
        }
        if (Auth::user()->id != $user) {
            return redirect('/Unauthorized');
        }

        $site_id = $site->id;
        $uprisers = $site->uprisers;
        $phonenumber = $site->phone_number;
        $site_name = $site->name;
        $countsall = Control::where('site_id', $site_id)->count() > 0;
        $commandsent = '';
        $siteEnable = $site->ctrl_enable;
        if ($countsall) {
            $storeControl = Control::where('site_id', $site_id)->first();
            if ($siteEnable == '1') {
                if ($request->CM == "true") {
                    $storeControl->c1 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'MAIN:ON, ';
                } else if ($request->CM == "false") {

                    $storeControl->c1 = '0';
                    $commandsent = $commandsent . 'MAIN:OFF, ';
                }
                if ($request->CA == "true" && $uprisers >= 1) {
                    $storeControl->c2 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'UP_A:ON, ';
                } else if ($request->CA == "false" && $uprisers >= 1) {
                    $storeControl->c2 = '0';
                    $commandsent = $commandsent . 'UP_A:OFF, ';
                }
                if ($request->CB == "true" && $uprisers >= 2) {
                    $storeControl->c3 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'UP_B:ON, ';
                } else if ($request->CB == "false" && $uprisers >= 2) {
                    $storeControl->c3 = '0';
                    $commandsent = $commandsent . 'UP_B:OFF, ';
                }
                if ($request->CC == "true" && $uprisers >= 3) {
                    $storeControl->c4 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'UP_C:ON, ';
                } else if ($request->CC == "false" && $uprisers >= 3) {
                    $storeControl->c4 = '0';
                    $commandsent = $commandsent . 'UP_C:OFF, ';
                }
                if ($request->CD == "true" && $uprisers >= 4) {
                    $storeControl->c5 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'UP_D:ON, ';
                } else if ($request->CD == "false" && $uprisers >= 4) {
                    $storeControl->c5 = '0';
                    $commandsent = $commandsent . 'UP_D:OFF, ';
                }

            } else if ($siteEnable == '2') {
                if ($request->CM == "true") {
                    $storeControl->c1 = '1';
                    $storeControl->ctrl_chk = 1;
                    $commandsent = $commandsent . 'MAIN:ON, ';
                } else if ($request->CM == "false") {

                    $storeControl->c1 = '0';
                    $commandsent = $commandsent . 'MAIN:OFF, ';
                }

                $storeControl->c2 = 'N';

                $storeControl->c3 = 'N';

                $storeControl->c4 = 'N';

                $storeControl->c5 = 'N';

            }
            $storeControl->save();

        }
        $controldata = array('c1' => $storeControl->c1, 'c2' => $storeControl->c2, 'c3' => $storeControl->c3, 'c4' => $storeControl->c4, 'c5' => $storeControl->c5);
        $helperClass = new HelperClass();
        $response = $helperClass->sendCtrlSMS($phonenumber, json_encode($controldata),  "termii");
        \Log::info($controldata);
        $logs = ControlLogs::create([
            'email' => Auth::user()->email,
            'user' => Auth::user()->name,
            'sitename' => $site_name,
            'command' => $commandsent,
            'ip_address' => \Request::ip(),
        ]);
        $logs->save();
        return response()->json([
            'message' => 'Successful',
            'status' => 200,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CtrlFeedback  $ctrlFeedback
     * @return \Illuminate\Http\Response
     */
    public function edit(CtrlFeedback $ctrlFeedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CtrlFeedback  $ctrlFeedback
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CtrlFeedback $ctrlFeedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CtrlFeedback  $ctrlFeedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(CtrlFeedback $ctrlFeedback)
    {
        //
    }
}