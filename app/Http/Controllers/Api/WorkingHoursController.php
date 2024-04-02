<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkingHour;
use Illuminate\Http\Request;

class WorkingHoursController extends Controller
{
    public function workingHours(Request $request)
    {
            WorkingHour::create([
                'date'=>$request->date,
                'start_hour'=>$request->start_hour,
                'end_hour'=>$request->end_hour,
                'closed'=>'no'
            ]);
        return response()->json([
            'status'=>true,
            'message'=>'working hour created'
        ]);
    }

    public function closedDays(Request $request)
    {
        WorkingHour::create([
            'date'=>$request->date,
            'closed'=>'yes'
        ]);
        return response()->json([
            'status'=>true,
            'message'=>'closed day created'
        ]);
    }
}
