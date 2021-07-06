<?php

namespace App\Http\Controllers;

use App\Libraries\Firebase;
use App\PushNotifier;
use Illuminate\Http\Request;

class PushNotifierController extends Controller
{

    public function notify()
    {
        $request = json_decode(\request()->getContent());
        $receiver = $request->receiver_user;
        $notification = $request->notification;
        $data = $request->data;
        $push_type = $request->push_type;

        try {

            $firebase = new Firebase();
            $response = '';
            if ($push_type === 'topic') {
                $response = $firebase->sendToTopic('global', $notification, $data);
            } else if ($push_type === 'individual') {
                $regId = $receiver ?? '';
                $response = $firebase->send($regId, $notification, $data);
                return response()->json([
                    'response' => $response,
                ]);
            }

        } catch (\Exception $ex) {
            return response()->json([
                'error' => true,
                'message' => $ex->getMessage(),
            ]);
        }

    }

    public function alarmnotify($receiver_user, $title, $message)
    {
       
        $receiver = $receiver_user;
        $title = $title;
        $body = $body;
        $app = app();
        $notification = $app->make('stdClass');
        $notification->title = $title;
        $notification->body = $body;
        $app = app();
        $data = $app->make('stdClass');
        $data->title = $title;
        $data->body = $body;
        try {

            $firebase = new Firebase();
            $response = '';
            $regId = $receiver ?? '';
            $response = $firebase->send($regId, $notification, $data);
            return response()->json([
                'response' => $response,
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'error' => true,
                'message' => $ex->getMessage(),
            ]);
        }

    }
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PushNotifier  $pushNotifier
     * @return \Illuminate\Http\Response
     */
    public function show(PushNotifier $pushNotifier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PushNotifier  $pushNotifier
     * @return \Illuminate\Http\Response
     */
    public function edit(PushNotifier $pushNotifier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PushNotifier  $pushNotifier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PushNotifier $pushNotifier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PushNotifier  $pushNotifier
     * @return \Illuminate\Http\Response
     */
    public function destroy(PushNotifier $pushNotifier)
    {
        //
    }
}