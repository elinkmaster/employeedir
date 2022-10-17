<?php

namespace App\Http\Controllers;

use App\Mail\ProbitionaryEmailNotificationA;
use App\Mail\ProbitionaryEmailNotificationB;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailReminderController extends Controller
{
    public function index()
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
                    'emp_name' => strtoupper($employee->fullname()),
                    'date_hired' => Carbon::parse($employee->hired_date)->format('Y-m-d H:m'),
                ];

                $supervisors = User::select('email', 'email2', 'first_name', 'last_name')->get();
                $supervisorEmail = '';
                foreach($supervisors as $supervisor)
                {
                    if($supervisor->fullname() == $employee->supervisor_name || strtoupper($supervisor->fullname()) == strtoupper($employee->supervisor_name)){
                        $supervisorEmail = $supervisor->email ?? $supervisor->email2;
                    }else{
                        $supervisorEmail= 'hrd@elink.com.ph';
                    }
                }
    
                if($todayDate == $objectDate->addMonths(3)->subDay(10)->format('Y-m-d'))
                {
                        $data['date'] =$objectDate->addMonths(3)->subDay(1)->format('Y-m-d');
                        Mail::to($supervisorEmail)->cc($employee->email ?? $employee->email2)->send(new ProbitionaryEmailNotificationA($data));
                }elseif($todayDate == $objectDate->addMonths(5)->subDay(10)->format('Y-m-d')){
                        $data['date'] =$objectDate->addMonths(5)->subDay(1)->format('Y-m-d');
                       Mail::to($supervisorEmail)->cc($employee->email ?? $employee->email2)->send(new ProbitionaryEmailNotificationB($data));
                }
	    }
        }
    }

    public function remindTeamLeader()
    {   
        $todayDate = now();
        $leaves =  LeaveRequest::where('status',1)->whereYear('created_at', '=', $todayDate->year)->where('approve_status_id',NULL)->orWhere('approve_status_id',0)->get();

        foreach($leaves as $leave)
        {
            $employee = DB::table('employee_info')->find($leave->employee_id);
            $supervisor = DB::table('employee_info')->where(DB::raw('concat(last_name,", ", first_name)'), 'LIKE', "%$employee->supervisor_name%")->first();
            $manager = DB::table('employee_info')->where(DB::raw('concat(last_name,", ", first_name)'), 'LIKE', "%$employee->manager_name%")->first();

            $recipients = [
                'hrd@elink.com.ph',
                $supervisor->email,
                $manager->email,
            ];

            $fileDate = Carbon::parse($leave->date_filed);
            if($fileDate->addDays(5)->format('Y-m-d') == $todayDate->format('Y-m-d')){
                $leave_obj = ['leave' => $leave, 'details' => LeaveRequestDetails::where("leave_id",$leave->id)->get()];
                Mail::to($recipients)->send(new LeaveNotification($leave_obj));
            }
        }
    }
}
