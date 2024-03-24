<?php

namespace App\Jobs;

use App\Http\Controllers\Api\SendSmsController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsWaitingReservation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $phone_number,$time,$date;
    public function __construct($waiting_phone,$time,$date)
    {
        $this->phone_number=$waiting_phone;
        $this->time=$time;
        $this->date=$date;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $sms = new SendSmsController;
        $sms->sendSmsWaiting($this->phone_number,$this->time,$this->date);

        //؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟
        return response()->json([
            'status' => true,
            'message' => 'پیام رزرو مجدد در صف ارسال قرار گرفت'
        ]);
    }
}
