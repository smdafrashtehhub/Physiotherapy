<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use Melipayamak\MelipayamakApi;
use function response;

class SendSmsController extends Controller
{
    public function mainSms($phone_number,$text)
    {
        $username = '09901950098';
        $password = 'QY5!H';
        $api = new MelipayamakApi($username,$password);
        $sms = $api->sms();
        $to = $phone_number;
        $from = '50002710050098';
        $response = $sms->send($to,$from,$text);
    }

    public function sendCode($phone_number)
    {
        OtpCode::where('phone_number',$phone_number)->delete();
        $otpcode=random_int(10000,999999);
        $text = " کد تایید شما $otpcode میباشد.
        لغو 11";
        $this->mainSms($phone_number,$text);
        OtpCode::create([
           'otp_code'=>$otpcode,
           'phone_number'=> $phone_number
        ]);
        return response()->json([
            'status' => true,
            'message' => 'کد یکبار مصرف با موفقیت ارسال شد'
        ]);
    }

    public function sendSmsWaiting($phone_number,$time,$date)
    {

        $text = "نوبت دکتر فیزیوتراپ درتاریخ $date و ساعت $time باز شده است.شما میتوانید این نوبت را رزرو کنید.با تشکر
         لغو 11";
        $this->mainSms($phone_number,$text);
    }
}
