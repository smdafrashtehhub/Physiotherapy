<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\WokingWeek;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use function response;

class ReservationController extends Controller
{
    public function checkdate($date)
    {
        $workinghour = WorkingHour::where('date', $date)->get();
        if ($workinghour->count())
            return response()->json([
                'status' => true,
                'message1' => $workinghour,
                'message2' => Reservation::where('date',$date)->get()
            ]);
        else{
//            $newDate=new Date('1402-12-17');
//            $gregorianDate=Jalalian::fromFormat('Y-m-d', $date);
//            $dayOfWeek = $gregorianDate->getDayOfWeek();
            $gregorianDate=Jalalian::fromFormat('Y-m-d', $date)->toCarbon()->toDateString() ;
            $dayOfWeek = Carbon::createFromFormat('Y-m-d', $gregorianDate)->dayName;
            $workingweek=WokingWeek::where('day_of_week',$dayOfWeek)->get();
            return response()->json([
                'status' => true,
                'message1' => $workingweek,
                'message2' => Reservation::where('date',$date)->get()
            ]);
        }
    }
}
