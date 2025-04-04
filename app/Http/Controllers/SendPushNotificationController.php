<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FCMToken;

class SendPushNotificationController extends Controller
{    
    /**
    * Functionality to send notification.
    * 
    */
    public function sendNotification($user_id, $message)
    {
        $title = \Config::get('app.name');
        $api_key = \Config::get('app.server_key');
        $url = "https://fcm.googleapis.com/fcm/send";

        $msg = array(
            'body'  => $message,
            'title' => $title,
            'icon'  => 'myicon',//Default Icon
            'sound' => 'mySound'//Default sound
        );

        // for Android
        $FCMTokenData = FCMToken::where('user_id',$user_id)->where('token','!=',null)->get();
        if (!$FCMTokenData->isEmpty()) {
            foreach ($FCMTokenData as $key => $value) {                
                $fields = array('to'=>$value->token,'notification'=>$msg);
                $headers = array(
                    'Authorization: key='.$api_key,
                    'Content-Type: application/json'
                );

                #Send Reponse To FireBase Server
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec ($ch);
                curl_close ( $ch );
                $result = json_decode($result,true);
                $result['message'] = $message;
                $value->message = json_encode($result);
                $value->save();
            }
        }

        // for IOS
        $FCMTokenData = FCMToken::where('user_id',$user_id)->where('apns_id','!=',null)->get();
        if (!$FCMTokenData->isEmpty()) {
            foreach ($FCMTokenData as $key => $value) {
                $fields = array('to'=>$value->apns_id,'notification'=>$msg);
                $headers = array(
                    'Authorization: key='.$api_key,
                    'Content-Type: application/json'
                );

                #Send Reponse To FireBase Server
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec ($ch);
                curl_close ( $ch );
                $result = json_decode($result,true);
                $result['message'] = $message;
                $value->message = json_encode($result);
                $value->save();
            }            
        }
    }
}
