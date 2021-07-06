<?php
namespace App\Helpers;

use App\Communication;
use App\GlobalSetting;
use App\Libraries\Firebase;
use App\Site;
use App\SiteReport;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Str;
class HelperClass
{

    public function sendMail($emailAddress, $subject, $sitename, $alarm)
    {
        $mail = new PHPMailer(true);
        try {
            /* Set the mail sender. */
            $mail->setFrom("donotreply@susejgroup.net", 'Susej IoT');

            /* Add a recipient. */
            $mail->addAddress($emailAddress);

            /* Set the subject. */
            $mail->Subject = $subject;

            /* Set the mail message body. */
            $mail->Body = 'Urgent attention is needed at ' . $sitename . '!<br><br>
            ' . $alarm . ' is down            <br><br>
            Thanks.
            <br><br>';

            /* Finally send the mail. */
            $mail->send();
        } catch (Exception $e) {
            /* PHPMailer exception. */
            echo $e->errorMessage();
        } catch (\Exception $e) {
            /* PHP exception (note the backslash to select the global namespace Exception class). */
            echo $e->getMessage();
        }
    }
    public function sendCtrlSMS($phoneNumber, $message, $provider)
    {
        $client = new Client();

        if ($provider == "infobip") {
            $method = 'GET';
            $url = "https://knjz1.api.infobip.com/sms/1/text/query?username=SusejNigeria2018&password=Susejnigeria123@" . '&to=234' . $phoneNumber . '&text=' . $message;
        } else {
            $method = 'POST';
            $url = "https://termii.com/api/sms/send?to=234" . $phoneNumber . "&from=Susej&sms=" . $message . "&type=plain&channel=generic&api_key=YXN4c0RxeW96ZkxMcHNqa010cUc=";
        }
        \Log::info($url);
        $request = $client->request($method, $url)->getBody();
        $response = $request->getContents();
        \Log::info($response);
        return $response;
    }
    public function sendSMS($sitename, $alarm, $phoneNumber)
    {
        //sending sms
        $communicationsetting = GlobalSetting::where('name', 'globalcommunication')->value("setting");
        $sms = $communicationsetting['sms'];
        if ($sms == "1") {
            $message = 'Urgent attention is needed at ' . $sitename . '! ' . $alarm . ' is down. Thanks.';
            $client = new Client();

            $infobip_url = env('INFO_BIP_URL') . '&to=' . $phoneNumber . '&text=' . $message;
            $response = $client->request('GET', $infobip_url);
        }

        // $infobip_url = env('INFO_BIP_URL');
        // $response = $client->request('GET', $message, $infobip_url);

    }

    public function sendEmail($emailAddress, $subject, $sitename, $alarm)
    {
        $communicationsetting = GlobalSetting::where('name', 'globalcommunication')->value("setting");
        $email = $communicationsetting['email'];
        if ($email == "1") {
            $companyEmail = "donotreply@susejgroup.net";
            $to = $emailAddress;
            $from = "From: Susej IOT <" . $companyEmail . ">";
            $header = $from . "\r\n" . "Content-Type: text/html; charset=utf-8";
            // $subject = ''
            $sub = $subject;
            $msgBody = '
        <br><br>';
            if ($sub == 'Alarm Notification Resolved') {
                $msgBody1 = '
            Thanks.
            <br><br>
            Have a lovely working day !
            <br><br>';
            } else if ($sub == 'CB Tripped') {
                $msgBody1 = '
            Urgent attention is needed at ' . $sitename . '!
            <br><br>
            The' . $alarm . ' Control Circuit Breaker as tripped.
            <br><br>
            Please confirm if there is no major fault on the line before CB closure attempt.
            <br><br>
            Thanks.
            <br><br>';
            } else {
                $msgBody1 = '
            Urgent attention is needed at ' . $sitename . '!
            <br><br>
            ' . $alarm . ' is down
            <br><br>
            Thanks.
            <br><br>';
            }
            $allMsgBody = $msgBody . $msgBody1 . 'IOT Monitoring Admin';
            $path = "-f " . $companyEmail;
            mail($to, $sub, $allMsgBody, $header, $path);
        }
    }

    public function sendNotification($siteId, $alarmName)
    {
        $siteName = Site::where('id', $siteId)->value('name');
        $siteOwner = Site::where('id', $siteId)->value('user_id');
        $subject = "Attention Needed - " . $siteName;
        $comm = Communication::where('user_id', $siteOwner)->get();
        foreach ($comm as $getComm) {

            $enable = $getComm->email_enable;
            $phoneNumber = $getComm->sms_mobile_number;
            $smsEnable = $getComm->sms_enable;
            $timeCheck = $getComm->schedule_time;
            $emailAddress = $getComm->email_address;

            if ($timeCheck == 0 && $enable == 1) {

                $this->sendEmail($emailAddress, $subject, $siteName, $alarmName);
            }

            if ($timeCheck == 0 && $smsEnable == 1) {
                $this->sendSMS($siteName, $alarmName, $phoneNumber);
            }

        }
        $siteUt = Site::where('id', $siteId)->value('ut_id');
        if ($siteUt != null) {
            $comm = Communication::where('user_id', $siteUt)->get();
            foreach ($comm as $getComm) {
                $emailAddress = $getComm->email_address;
                $enable = $getComm->email_enable;
                $phoneNumber = $getComm->sms_mobile_number;
                $smsEnable = $getComm->sms_enable;
                $timeCheck = $getComm->schedule_time;
                if ($timeCheck == 0 && $enable == 1) {
                    $this->sendEmail($emailAddress, $subject, $siteName, $alarmName);
                }
                if ($timeCheck == 0 && $smsEnable == 1) {
                    $this->sendSMS($siteName, $alarmName, $phoneNumber);
                }
            }
        }

        $siteClient = Site::where('id', $siteId)->value('client_id');
        $comm = Communication::where('user_id', $siteClient)->get();
        foreach ($comm as $getComm) {
            $emailAddress = $getComm->email_address;
            $enable = $getComm->email_enable;
            $phoneNumber = $getComm->sms_mobile_number;
            $smsEnable = $getComm->sms_enable;
            $timeCheck = $getComm->schedule_time;
            if ($timeCheck == 0 && $enable == 1) {
                $this->sendEmail($emailAddress, $subject, $siteName, $alarmName);
            }
            if ($timeCheck == 0 && $smsEnable == 1) {
                $this->sendSMS($siteName, $alarmName, $phoneNumber);
            }
        }
        $siteUser = Site::where('id', $siteId)->value('siteuser_id');
        if ($siteUser != null) {
            $comm = Communication::where('user_id', $siteUser)->get();
            foreach ($comm as $getComm) {
                $emailAddress = $getComm->email_address;
                $enable = $getComm->email_enable;
                $phoneNumber = $getComm->sms_mobile_number;
                $smsEnable = $getComm->sms_enable;
                $timeCheck = $getComm->schedule_time;
                if ($timeCheck == 0 && $enable == 1) {
                    $this->sendEmail($emailAddress, $subject, $siteName, $alarmName);
                }
                if ($timeCheck == 0 && $smsEnable == 1) {
                    $this->sendSMS($siteName, $alarmName, $phoneNumber);
                }
            }

        }
    }

    public function sendScheduleNotification($alarms)
    {
        $alarmName = $alarms->alarm;
        $alarmId = $alarms->id;
        $siteId = $alarms->site_id;
        $isSent = $alarms->stop_message;
        $siteName = Site::where('id', $siteId)->value('name');
        $siteOwner = Site::where('id', $siteId)->value('user_id');
        $subject = "Attention Needed - " . $siteName;
        $starttime = $alarms->created_at;
        $start = date($starttime);
        $today = date('Y-m-d H:i:s');
        $starts = Carbon::parse($start);
        $now = Carbon::parse($today);
        $interval = $starts->diffInRealHours($now);
        $comm = Communication::where('user_id', $siteOwner)->get();
        // \Log::info($interval);

        foreach ($comm as $getComm) {
            $enable = $getComm->email_enable;
            $smsEnable = $getComm->sms_enable;
            $timeCheck = $getComm->schedule_time;
            if ($timeCheck >= $interval && $enable == 1 && $isSent < $interval) {
                $emailAddress = $getComm->email_address;
                $this->sendEmail($emailAddress, $subject, $siteName, $alarmName);
                $siteReport = SiteReport::where('id', $alarmId)->get();
                $siteReport->stop_message = floor($timeCheck);
                $siteReport->save();
            }
            if ($timeCheck >= $interval && $smsEnable == 1 && $isSent < $interval) {
                $phoneNumber = $getComm->sms_mobile_number;
                $this->sendSMS($siteName, $alarmName, $phoneNumber);
                $siteReport = SiteReport::where('id', $alarmId)->get();
                $siteReport->stop_message = floor($timeCheck);
                $siteReport->save();
            }

        }
        $siteUt = Site::where('id', $siteId)->value('ut_id');
        if ($siteUt != null) {
            $comm = Communication::where('user_id', $siteUt)->get();
            foreach ($comm as $getComm) {
                $enable = $getComm->email_enable;
                $phoneNumber = $getComm->sms_mobile_number;
                $smsEnable = $getComm->sms_enable;
                $timeCheck = $getComm->schedule_time;
                if ($timeCheck >= $interval && $enable == 1 && $isSent < $interval) {
                    $emailAddress = $getComm->email_address;
                    $this->sendEmail($emailAddress, $subject, $siteName, $alarmName);
                    $siteReport = SiteReport::where('id', $alarmId)->get();
                    $siteReport->stop_message = floor($timeCheck);
                    $siteReport->save();
                }
                if ($timeCheck >= $interval && $smsEnable == 1 && $isSent < $interval) {
                    $phoneNumber = $getComm->sms_mobile_number;
                    $this->sendSMS($siteName, $alarmName, $phoneNumber);
                    $siteReport = SiteReport::where('id', $alarmId)->get();
                    $siteReport->stop_message = floor($timeCheck);
                    $siteReport->save();
                }
            }
        }

        $siteClient = Site::where('id', $siteId)->value('client_id');
        $comm = Communication::where('user_id', $siteClient)->get();
        foreach ($comm as $getComm) {
            $enable = $getComm->email_enable;
            $phoneNumber = $getComm->sms_mobile_number;
            $smsEnable = $getComm->sms_enable;
            $timeCheck = $getComm->schedule_time;
            if ($timeCheck >= $interval && $enable == 1 && $isSent < $interval) {
                $emailAddress = $getComm->email_address;
                $this->sendEmail($emailAddress, $subject, $siteName, $alarmName);
                $siteReport = SiteReport::where('id', $alarmId)->get();
                $siteReport->stop_message = floor($timeCheck);
                $siteReport->save();
            }
            if ($timeCheck >= $interval && $smsEnable == 1 && $isSent < $interval) {
                $phoneNumber = $getComm->sms_mobile_number;
                $this->sendSMS($siteName, $alarmName, $phoneNumber);
                $siteReport = SiteReport::where('id', $alarmId)->get();
                $siteReport->stop_message = floor($timeCheck);
                $siteReport->save();
            }
        }
        $siteUser = Site::where('id', $siteId)->value('siteuser_id');
        if ($siteUser != null) {
            $comm = Communication::where('user_id', $siteUser)->get();
            foreach ($comm as $getComm) {
                $enable = $getComm->email_enable;
                $phoneNumber = $getComm->sms_mobile_number;
                $smsEnable = $getComm->sms_enable;
                $timeCheck = $getComm->schedule_time;
                if ($timeCheck >= $interval && $enable == 1 && $isSent < $interval) {
                    $emailAddress = $getComm->email_address;
                    $this->sendEmail($emailAddress, $subject, $siteName, $alarmName);
                    $siteReport = SiteReport::where('id', $alarmId)->get();
                    $siteReport->stop_message = floor($timeCheck);
                    $siteReport->save();
                }
                if ($timeCheck >= $interval && $smsEnable == 1 && $isSent < $interval) {
                    $phoneNumber = $getComm->sms_mobile_number;
                    $this->sendSMS($siteName, $alarmName, $phoneNumber);
                    $siteReport = SiteReport::where('id', $alarmId)->get();
                    $siteReport->stop_message = floor($timeCheck);
                    $siteReport->save();
                }
            }

        }
    }

    public function alarmsnotify($receiver_users, $siteId, $getLabel)
    {
        $siteName = Site::where('id', $siteId)->value('name');
		
		foreach ($receiver_users as $regid) {
			 $receiver = $regid->fcm_token;
			if( Str::contains($getLabel, 'Down')){
				$title = "Site Update: " . $siteName;
				$body =  'Site is down. Thanks.';
			}else if(Str::contains($getLabel, 'Live')){
				$title ="Site Update: " . $siteName;
				$body = 'Site is back on Thanks.';
			}else{
				$title = "Attention Needed at - " . $siteName;
				$body = 'Alarm occur on ' . $getLabel . ' Thanks.';
			}
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
				//\Log::info(response()->json($response));
			} catch (\Exception $ex) {
				\Log::info( response()->json([
					'error' => true,
					'message' => $ex->getMessage(),
				]));
			}
		}
		return response()->json("Notification sent");

    }
	
	
    public function alarmnotify($receiver_user, $siteId, $getLabel)
    {
        $siteName = Site::where('id', $siteId)->value('name');
        $receiver = $receiver_user;
      
		if( Str::contains($getLabel, 'Down')){
			$title = "Site Update: " . $siteName;
			$body =  'Site is down. Thanks.';
		}else if(Str::contains($getLabel, 'Live')){
			$title ="Site Update: " . $siteName;
			$body = 'Site is back on Thanks.';
		}else{
			$title = "Attention Needed at - " . $siteName;
        	$body = 'Alarm occur on ' . $getLabel . ' Thanks.';
		}
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
            return response()->json($response);
        } catch (\Exception $ex) {
            return response()->json([
                'error' => true,
                'message' => $ex->getMessage(),
            ]);
        }

    }
}