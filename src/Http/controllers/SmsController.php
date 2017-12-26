<?php

namespace App\Http\Controllers;

use baklysystems\smsmisr\Sms;

use Illuminate\Http\Request;

class SmsController extends Controller
{
    /**
     * send_sms method takes request in POST
     * request has three attributes
     * $request->sms (the message you need to send) [string][required]
     * $request->mobiles (the mobile numbers in Egypt) [array][required]
     * $request->encode (encode) [integer][optional]
     */
    public function send_sms(Request $request){
        //you can handle request here


        //send method returns response of the message sent or not
        $response = Sms::send($request->sms, $request->moblies, $request->encode);

        //deal with response here
    }
}
