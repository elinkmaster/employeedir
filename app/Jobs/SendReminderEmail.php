<?php

namespace App\Jobs;

use App\Mail\ProbitionaryEmailNotificationA;
use App\Mail\ProbitionaryEmailNotificationB;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $employees = User::where('is_regular', 0)->whereNull('deleted_at')->where('id', '<>', 1)->get();
        foreach($employees as $employee)
        {
            if($employee->id == 3681 || $employee->id == 3689){

                $hiredDate = Carbon::parse($employee->hired_date)->format('Y-m-d');
                // convert string date to object carbon
                $objectDate = Carbon::createFromFormat('Y-m-d', $hiredDate);
                $todayDate = now()->format('Y-m-d');
                
                $data = [
                    'emp_id' => $employee->id,
                    'emp_name' => $employee->fullname(),
                    'date_hired' => $employee->hired_date,
                ];

                $supervisors = User::select('email', 'email2', 'first_name', 'last_name')->get();
                $supervisorEmail = '';
                foreach($supervisors as $supervisor)
                {
                    if(strtoupper($supervisor->fullname()) == strtoupper($employee->supervisor_name)){
                        $supervisorEmail = $supervisor->email ?? $supervisor->email2;
                        break;
                    }
                }

                // if($todayDate == $objectDate->addMonths(3)->format('Y-m-d'))
                // {
                        $data['date'] =$objectDate->addMonths(3)->format('Y-m-d');
                        return Mail::to($supervisorEmail)->cc($employee->email ?? $employee->email2)->queue(new ProbitionaryEmailNotificationA($data));
                // }elseif($todayDate == $objectDate->addMonths(5)->format('Y-m-d')){
                //         $data['date'] =$objectDate->addMonths(5)->format('Y-m-d');
                //         Mail::to($supervisorEmail)->cc($employee->email ?? $employee->email2)->queue(new ProbitionaryEmailNotificationB($data));
                // }
            }

        }
    }
}
