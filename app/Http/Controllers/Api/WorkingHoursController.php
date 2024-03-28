<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkingHour;
use Illuminate\Http\Request;

class WorkingHoursController extends Controller
{
    public function workingHours(Request $request)
    {
        foreach ($request->start_hour as $key=>$value)
        {
            WorkingHour::create([
                'date'=>$request->date,
                'start_hour'=>$value,
                'end_hour'=>$request->end_hour[$key],
                'closed'=>'no'
            ]);
        }
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
