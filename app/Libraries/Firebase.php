<?php

namespace App\Libraries;

use GuzzleHttp\Client;
use Response;
class Firebase {


	/**
	 * sending push message to single user by firebase reg id
	 *
	 * @param $to
	 * @param $message
	 *
	 * @return mixed
	 */
	public function send( $to, $notification, $data) {
		  $fields = array(
            'to' => $to,
            'data' => $data,
            'notification' => $notification,
        );
		$resp = $this->sendPushNotification($fields);
	//	$resp = $this->sendPushNotifications($to , $notification, $data);
	    return $resp;
	}


	/**
	 * Sending message to a topic by topic name
	 *
	 * @param $to
	 * @param $message
	 *
	 * @return mixed
	 */
	public function sendToTopic( $to, $notification, $data ) {
		$fields = array(
			'to'   => '/topics/' . $to,
			'data' => $data,
			'notification' => $notification,
		);
		return $this->sendPushNotification( $fields );
	}


	/**
	 * Sending push message to multiple users by firebase registration ids
	 *
	 * @param $registration_ids
	 * @param $message
	 *
	 * @return mixed
	 */
	public function sendMultiple($registration_ids, $notification, $data)
    {
		
        $fields = array(
            'to' => $registration_ids,
            'notification' => $notification,
            'data' => $data,
        );
		$resp = $this->sendPushNotification($fields);
	    return $resp;
    }

	/**
	 * POST request to firebase servers
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	private function sendPushNotification($fields)
    {
		\Log::info("using fields");
        $push_notification_key = env('FCM_LEGACY_KEY');    
        $url = "https://fcm.googleapis.com/fcm/send";            
        $header = array("authorization: key=" . $push_notification_key . "",
            "content-type: application/json"
        ); 
		$ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);    
        // close handle to release resources
        curl_close($ch);
        return $result;

    }
	
	function sendPushNotifications($fcm_token ,$notification,$data)
    {
		$title= $notification->title;
        $message=$notification->body;
        $push_notification_key = env('FCM_LEGACY_KEY');    
        $url = "https://fcm.googleapis.com/fcm/send";            
        $header = array("authorization: key=" . $push_notification_key . "",
            "content-type: application/json"
        );    
 		 $postdata = '{
            "to" : "' . $fcm_token . '",
                "notification" : {
                    "title":"' . $title . '",
                    "text" : "' . $message . '"
                },
            "data" : {
                "title":"' . $title . '",
                "description" : "' . $message . '",
                "text" : "' . $message . '",
              }
        }';
        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);    
        // close handle to release resources
        curl_close($ch);
        return $result;
    }
}