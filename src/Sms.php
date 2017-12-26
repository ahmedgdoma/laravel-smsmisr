<?php

namespace baklysystems\smsmisr;

//use App\Http\Controllers\Controller;

use baklysystems\smsmisr\models\Systemconf;
use Illuminate\Http\Request;

class Sms
{

        protected $link;
        protected $username;
        protected $password;
        protected $ownerMobiles; //string csv for multiple
        public $language = 1;
        public $sender;


        /*****
         *   $SMS=new Sms();
         *    $responses=$SMS->send($request->sms,'2'.$request->mobiles,3);
         *
         */
        public function __construct()
    {
        $this->username = config( 'SMS_USERNAME' );
        $this->password = config( 'SMS_PASSWORD' );
        $this->sender = config( 'SMS_SENDER' );
        $this->link = config( 'SMS_LINK' );
        $this->ownerMobiles = config( 'OWNER_MOBILES' );
    }

        private function baseLink( $lang = 1, $sender = null )
    {
        if ( $lang ) {
            $this->language = $lang;
        }
        if ( $sender ) {
            $this->sender = $sender;
        }
        return $this->link . "username=" . $this->username
            . "&password=" . $this->password
            . "&language=" . $this->language
            . "&sender=" . $this->sender;
    }

        private function messageEncoding( $sms, $encode = 1 )
    {
        if ( $encode == 1 ) {
            $message = urlencode( $sms );
        } else {
            $message = bin2hex( mb_convert_encoding( $sms, 'UCS-2', 'auto' ) );
        }

        return $message;
    }


        private function sendAction( $sms, $mobiles, $encode = 1 )
    {
        $link=$this->baseLink( $encode )
            . "&mobile=" . urlencode( $mobiles )
            . "&message=" . $this->messageEncoding( $sms, $encode );

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $link );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec( $ch );
        if(curl_errno($ch)){
            echo curl_error($ch);die;
        }
        curl_close( $ch );
        return $output;
    }

        private function updateCredit( $newCredit )
    {
        $conf = Systemconf::where('mobile', config('OWNER_MOBILES'))->get();
        if(!$conf){
            $conf = new Systemconf();
            $conf->mobile = config('OWNER_MOBLILES');
            $oldCredit = $newCredit;
        }else{
            $oldCredit = $conf->smsCredit;
        }
        $conf->smsCredit = $newCredit;
        $conf->save();
        return $oldCredit;


    }

        private function lowCreditNotification( $oldCredit, $newCredit ){
        //echo "got here";
        if($oldCredit){
            if ( $oldCredit >= 100 && $newCredit < 100 ) {
                // $this->send('sms masr is now under 100 EGP', $this->ownerMobiles);
            }
        }
    }

        private function parseResponse( $response ){
        $errorCode=substr( $response, 0, 4 );
        $credit=0;
        if( $errorCode == 1901 ){
            $after_credit = substr( last( explode(',', $response ) ), 7 );
            $credit=(int)$after_credit;
        }
        $message=$this->errorTranslate( $errorCode );
        return ['message'=>$message,'credit'=>$credit];
    }

        private function errorTranslate( $errorCode )
    {
        switch ( $errorCode ) {
            case 1901:
                $m = 'Success, Message Submitted Successfully';
                break;
            case 1902:
                $m = 'Invalid URL Error, This means that one of the parameters was not provided';
                break;
            case 1903:
                $m = 'Invalid value in username or password field';
                break;
            case 1904:
                $m = 'Invalid value in "sender" field';
                break;
            case 1905:
                $m = 'Invalid value in "mobile" field';
                break;
            case 1906:
                $m = 'Insufficient Credit';
                break;
            case 1907:
                $m = 'SMS Misr Server under update';
                break;
            default:
                $m=$errorCode;
                break;
        }
        return $m;
    }

        public function send( $sms, $mobiles, $encode = 1 )
    {
        if ( is_array( $mobiles ) ) {
            $mobiles = join( ',', $mobiles );
        }
        $response = $this->sendAction( $sms, $mobiles, $encode );
        $response = $this->parseResponse( $response );
        $oldCredit= $this->updateCredit( $response['credit'] );
        $this->lowCreditNotification( $oldCredit, $response['credit'] );
        return $response['message'] ." Your Credit is ".$response['credit'];
    }


}
