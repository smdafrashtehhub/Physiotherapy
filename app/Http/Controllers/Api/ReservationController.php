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
use Illuminate\Support\Facades\DB;
use MNDCo\IranianHoliday\IranianHoliday;
use Morilog\Jalali\Jalalian;
use function response;

class ReservationController extends Controller
{
    public function visited(Reservation $reservation,Request $request)
    {
        $reservation->update([
            'visited_status'=>$request->status
        ]);
    }
    public function delete(Reservation $reservation)
    {
        $reservation->delete();
        return response()->json([
            'status'=>true,
            'message'=>'reservation delete successfully'
        ]);
    }
    public function table()
    {
        $reservation = DB::table('reservations')
            ->join('users', 'reservations.user_id', '=', 'users.id')
            ->select('reservations.time', 'reservations.payment_status','reservations.visited_status', 'users.first_name','users.gender','users.last_name','users.phone_number')
            ->where('date', Jalalian::now()->format('Y/m/d'))
            ->get();
        return response()->json([
            'status' => true,
            'message1' =>$reservation
        ]);    }

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
        $otpcode = OtpCode::where('user_id', $request->user_id)->first();
        if ($request->otpcode == $otpcode->otp_code) {
            $otpcode->delete();

            if ($this->holidays($request->date)) {
                return response()->json([
                    'status' => false,
                    'message' => 'clinic in closed in this day'
                ]);
            }

            if (Reservation::where([
                'date' => $request->date,
                'time' => $request->time
            ])->count()) {
                if (WaitingReservation::where([
                    'user_id' => $request->user_id,
                    'date' => $request->date,
                    'time' => $request->time
                ])->count()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'before that You were waiting in line'
                    ]);
                } else {
                    WaitingReservation::create([
                        'user_id' => $request->user_id,
                        'date' => $request->date,
                        'time' => $request->time,
                    ]);
                    return response()->json([
                        'status' => true,
                        'message' => 'now You are waiting in line'
                    ]);
                }
            } else {
                if (Reservation::where([
                    'user_id' => $request->user_id,
                    'date' => $request->date,
                ])->count()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have a reservation on this day!'
                    ]);
                } else {
                    if (User::find($request->user_id)->national_code)
                        return response()->json([
                            'status' => true,
                            'message' => 'Your information has been registered'
                        ]);
                    else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Your information has not been registered'
                        ]);
                    }
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'You have not entered the one-time code correctly. Please try again'
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

        Reservation::create([
            'time' => $request->time,
            'date' => $request->date,
            'payment_status' => $request->payment_status == 'آنلاین' ? 'online' : 'cash',
            'user_id' => $request->user_id
        ]);
        return response()->json([
            'status' => true,
            'message' => 'The reservation was made successfully'
        ]);
    }

//------------------------------------ delete_reservation ----------------------------------------

    public function deleteReservation(Request $request, User $user)
    {
        $user->update([
            'card_number' => $request->card_number
        ]);
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

//------------------------------------ holidays ----------------------------------------
    public function holidays($date)
    {
        $clinic_holidays = WorkingHour::where([
            'date' => $date,
            'closed' => 'yes'
        ])->count();
        $holiday = new IranianHoliday();
        $official_holidays = $holiday->checkIsHoliday($date);
        if ($clinic_holidays || $official_holidays) {
            return true;
        }
        return false;
    }
}
