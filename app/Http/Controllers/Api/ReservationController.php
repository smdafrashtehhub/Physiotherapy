<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmationCodeRequest;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\SubmiteInformationRequest;
use App\Jobs\SmsWaitingReservation;
use App\Models\OtpCode;
use App\Models\Reservation;
use App\Models\User;
use App\Models\WaitingReservation;
use App\Models\WokingWeek;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;
use function response;

class ReservationController extends Controller
{

    //------------------------------------ checkdate ----------------------------------------

    public function checkDate()
    {
        if (WorkingHour::all()->count())
            return response()->json([
                'status' => true,
                'work_hours' => WorkingHour::where('closed', 'no')->select('date', 'start_hour', 'end_hour')->get(),
                'closed_days' => WorkingHour::where('closed', 'yes')->select('date')->get(),
                'reservation' => Reservation::select('date', 'time')->get()
            ]);
        else {
            return response()->json([
                'status' => true,
                'message1' => 'table is empty'
            ]);
        }
//        else {
//            $newDate=new Date('1402-12-17');
//            $gregorianDate=Jalalian::fromFormat('Y-m-d', $date);
//            $dayOfWeek = $gregorianDate->getDayOfWeek();
//            $gregorianDate = Jalalian::fromFormat('Y-m-d', $request->date)->toCarbon()->toDateString();
//            $dayOfWeek = Carbon::createFromFormat('Y-m-d', $gregorianDate)->dayName;
//            $workingweek = WokingWeek::where('day_of_week', $dayOfWeek)->get();
//            return response()->json([
//                'status' => true,
//                'message1' => $workingweek,
//                'message2' => Reservation::where('date', $request->date)->get()
//            ]);
//        }
    }

//------------------------------------ checktime ----------------------------------------

    public function checkTime(SendSmsRequest $request)
    {
//        $reservation_count=Reservation::where(['time'=> $request->time,'date'=>$request->date])->count();
//        if ($reservation_count) {
//
////            return response()->json([
////                'status' => true,
////                'message' => '.نوبت انتخابی شما تکمیل است. در صورت تمایل میتوانید در صف انتظار بمانید'
////            ]);
//        } else {
        $sms = new SendSmsController;
        $sms->sendCode($request->phone_number);
//        }

    }

//------------------------------------ sendotpcode ----------------------------------------

    public function sendOtpCode(Request $request)
    {

    }

//------------------------------------ confirmationcode ----------------------------------------

    public function confirmationCode(ConfirmationCodeRequest $request)
    {
        $otpcode = OtpCode::where('phone_number', $request->phone_number)->first();
        if ($request->otpcode == $otpcode->otp_code) {
            $otpcode->delete();
            if (Reservation::where(['date' => $request->date, 'time' => $request->time])->count()) {
                WaitingReservation::create([
                    'phone_number' => $request->phone_number,
                    'date' => $request->date,
                    'time' => $request->time,
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'شما در صف انتظار قرار گرفتید'
                ]);
            }
            if (!User::where('phone_number', $request->phone_number)->count())
                return response()->json([
                    'status' => false,
                    'message' => 'اطلاعات شما ثبت نشده است'
                ]);
            else {
                return response()->json([
                    'status' => true,
                    'message' => 'اطلاعات شما ثبت شده است'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'کد یکبار مصرف را درست وارد نکرده اید.دوباره تلاش کنید'
            ]);
        }
    }

//------------------------------------ submit_information ----------------------------------------

    public function submitInformation(SubmiteInformationRequest $request)
    {
        User::create([
            'name' => $request->name,
            'family' => $request->family,
//'user_name' =>$request->user_name,
            'national_code' => $request->national_code,
            'address' => $request->address,
            'age' => $request->age,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'disease_record' => $request->disease_record,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
    }

//------------------------------------ reservation ----------------------------------------

    public function reservation(ReservationRequest $request)
    {
        if(WorkingHour::where([
            'date'=>$request->date,
            'closed'=>'yes'
        ])->count())
            return response()->json([
                'status' => false,
                'message' => 'clinik in closed in this day'
            ]);
        $count_reservation = Reservation::where([
            'user_id' => $request->user_id,
            'date' => $request->date,
        ])->count();
        if ($count_reservation)
            return response()->json([
                'status' => false,
                'message' => 'شما در این روز رزرو کرده اید!'
            ]);
        Reservation::create([
            'time' => $request->time,
            'date' => $request->date,
            'payment_status' => $request->payment_status == 'آنلاین' ? 'online' : 'cash',
            'referral_status' => $request->referral_status == 'مراجعه کرده' ? 'yes' : 'no',
            'user_id' => $request->user_id
        ]);
        return response()->json([
            'status' => true,
            'message' => 'رزرو با موفقیت انجام شد'
        ]);
    }

//------------------------------------ delete_reservation ----------------------------------------

    public function deleteReservation(Request $request, User $user)
    {
        $user->reservations->where('date', $request->date)->first()->delete();
        if (WaitingReservation::all()->count()) {
            $waiting_phones = WaitingReservation::where([
                'date' => $request->date,
                'time' => $request->time,
            ])->pluck('phone_number');
            WaitingReservation::where([
                'date' => $request->date,
                'time' => $request->time,
            ])->delete();
            foreach ($waiting_phones as $waiting_phone) {
                SmsWaitingReservation::dispatch($waiting_phone, $request->time, $request->date);

            }
        }
    }
}
