<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\EmployeeInfoDetails;
use App\EmployeeDepartment;
use App\Mail\ProbitionaryEmailNotificationA;
use App\Mail\ProbitionaryEmailNotificationB;


class UtilsController extends Controller{
    
    public function patchDeptCode(){
        $Emp = new User();
        
        $object = $Emp
            ->whereRaw("team_name is not NULL")
            ->whereRaw("dept_code is NULL")
            ->activeEmployees()
            ->get();
        
        foreach($object as $e):
            $user = User::find($e->id);
            $obj = EmployeeDepartment::where("department_name",$e->team_name)->first();
            $user->dept_code = $obj->department_code;
            $user->save();
        endforeach;
        
        return "done";
    }
    
    public function notifyProbis_2_5(){
        $emp = new User();
       
        $obj = $emp
            ->where('employee_info.id', '<>', 1)
            ->leftJoin('employee_info_details','employee_info.id','=','employee_info_details.employee_id')
            ->activeEmployees()
            ->whereRaw("employee_info.hired_date is not NULL")
            ->whereRaw("DATEDIFF(now(),employee_info.hired_date) >= 75")
            ->whereRaw("DATEDIFF(now(),employee_info.hired_date) <= 95")
            ->where('employee_info.is_regular',"<>",1)
            ->where('employee_info_details.noti_2mos',0)
            ->orderBy('hired_date', 'ASC')
            ->get();
        
        //return $obj;
        $main = [];
        foreach($obj as $e):
            $sup    = User::find($e->supervisor_id);
            $mngr   = User::find($e->manager_id);
            $date   = date_create($e->hired_date);
            date_add($date, date_interval_create_from_date_string('90 days'));
            $emails = [];
            
            array_push($emails,isset($sup->email) ? $sup->email : "hrd@elink.com.ph");
            array_push($emails,isset($mngr->email) ? $mngr->email : "hrd@elink.com.ph");
            array_push($emails,"ivybarria@elink.com.ph");
            $arr = [
                'emp_id'        => $e->id,
                'emp_name'      => $e->first_name.' '.$e->last_name,
                'supervisor'    => isset($sup->email) ? $sup->email : "hrd@elink.com.ph",
                'manager'       => isset($mngr->email) ? $mngr->email : "hrd@elink.com.ph",
                'date_hired'    => date('F d, Y',strtotime($e->hired_date)),
                'date'          => date_format($date, 'F d, Y'),
                "hr"            => "hrd@elink.com.ph",
                "ver"           => "1.008",
                "mail_status"   => 0
            ];
            $arr['mail_status'] = Mail::to($emails)->queue(new ProbitionaryEmailNotificationA($arr));
            array_push($main,$arr);
        endforeach;
        
        return $main;
   }
   
   public function verifyProbis_2_5(){
        $emp = new User();
       
        $obj = $emp
            ->where('employee_info.id', '<>', 1)
            ->leftJoin('employee_info_details','employee_info.id','=','employee_info_details.employee_id')
            ->activeEmployees()
            ->whereRaw("employee_info.hired_date is not NULL")
            ->whereRaw("DATEDIFF(now(),employee_info.hired_date) >= 75")
            ->whereRaw("DATEDIFF(now(),employee_info.hired_date) <= 95")
           ->where('employee_info.is_regular',"<>",1)
            ->where('employee_info_details.noti_2mos',0)
            ->orderBy('hired_date', 'ASC')
            ->get();
        $main = [];
        foreach($obj as $e):
            $sup    = User::find($e->supervisor_id);
            $mngr   = User::find($e->manager_id);
            $date   = date_create($e->hired_date);
            date_add($date, date_interval_create_from_date_string('90 days'));
            $arr = [
                'emp_id'        => $e->id,
                'emp_name'      => $e->first_name.' '.$e->last_name,
                'supervisor'    => isset($sup->email) ? $sup->email : "hrd@elink.com.ph",
                'manager'       => isset($mngr->email) ? $mngr->email : "hrd@elink.com.ph",
                'date_hired'    => date('F d, Y',strtotime($e->hired_date)),
                'date'          => date_format($date, 'F d, Y'),
                "hr"            => "hrd@elink.com.ph",
                "ver"           => "1.008"
            ];
            array_push($main,$arr);
        endforeach;
        
        return $main;
   }
   
    public function verifyProbis_5_5(){
       $emp = new User();
       
       $obj = $emp
            ->select(DB::raw('*, DATEDIFF(now(),employee_info.hired_date) as num_days'))
            ->where('employee_info.id', '<>', 1)
            ->leftJoin('employee_info_details','employee_info.id','=','employee_info_details.employee_id')
            ->whereRaw("employee_info.hired_date is not NULL")
            ->whereRaw("DATEDIFF(now(),employee_info.hired_date) >= 135")
            ->whereRaw("DATEDIFF(now(),employee_info.hired_date) <= 200")
            ->where('employee_info.is_regular',"<>",1)
            ->where('employee_info_details.noti_5mos',0)
            ->orderBy('hired_date', 'ASC')
            ->activeEmployees()
            ->get();
       
        $main = [];
        foreach($obj as $e):
            $sup    = User::find($e->supervisor_id);
            $mngr   = User::find($e->manager_id);
            $date   = date_create($e->hired_date);
            date_add($date, date_interval_create_from_date_string('150 days'));
            $arr = [
                'emp_id'        => $e->id,
                'emp_name'      => $e->first_name.' '.$e->last_name,
                'supervisor'    => isset($sup->email) ? $sup->email : "hrd@elink.com.ph",
                'manager'       => isset($mngr->email) ? $mngr->email : "hrd@elink.com.ph",
                'date_hired'    => date('F d, Y',strtotime($e->hired_date)),
                'date'          => date_format($date, 'F d, Y'),
                "hr"            => "hrd@elink.com.ph",
                "ver"           => "4.5.1.003",
                "num_days"      => $e->num_days
            ];
            array_push($main,$arr);
        endforeach;
        
        return $main;
   }
   
    public function notifyProbis_5_5(){
        $emp = new User();
       
        $obj = $emp
            ->where('employee_info.id', '<>', 1)
            ->leftJoin('employee_info_details','employee_info.id','=','employee_info_details.employee_id')
            ->activeEmployees()
            ->whereRaw("employee_info.hired_date is not NULL")
            ->whereRaw("DATEDIFF(now(),employee_info.hired_date) >= 135")
            ->whereRaw("DATEDIFF(now(),employee_info.hired_date) <= 200")
            ->where('employee_info.is_regular',"<>",1)
            ->where('employee_info_details.noti_5mos',0)
            ->orderBy('hired_date', 'ASC')
            ->get();
        
        //return $obj;
        $main = [];
        foreach($obj as $e):
            $sup    = User::find($e->supervisor_id);
            $mngr   = User::find($e->manager_id);
            $date   = date_create($e->hired_date);
            date_add($date, date_interval_create_from_date_string('150 days'));
            $emails = [];
            
            array_push($emails,isset($sup->email) ? $sup->email : "hrd@elink.com.ph");
            array_push($emails,isset($mngr->email) ? $mngr->email : "hrd@elink.com.ph");
            array_push($emails,"ivybarria@elink.com.ph");
            $arr = [
                'emp_id'        => $e->id,
                'emp_name'      => $e->first_name.' '.$e->last_name,
                'supervisor'    => isset($sup->email) ? $sup->email : "hrd@elink.com.ph",
                'manager'       => isset($mngr->email) ? $mngr->email : "hrd@elink.com.ph",
                'date_hired'    => date('F d, Y',strtotime($e->hired_date)),
                'date'          => date_format($date, 'F d, Y'),
                "hr"            => "hrd@elink.com.ph",
                "ver"           => "1.008",
                "mail_status"   => 0
            ];
            $arr['mail_status'] = Mail::to($emails)->queue(new ProbitionaryEmailNotificationB($arr));
            array_push($main,$arr);
        endforeach;
        
        return $main;
   }
   
   public function stopNotify_2_5(Request $req){
       if(Auth::user()->id == 3246 || Auth::user()->id == 2810 ){
           return ['message' => "Access Granted", 'status' => EmployeeInfoDetails::where('employee_id',$req->id)->update(['noti_2mos' => 1])];
       }else{
           return ['message' => "Access Denied", 'status' => 0];
       }
   }
   
    public function stopNotify_5_5(Request $req){
       if(Auth::user()->id == 3246 || Auth::user()->id == 2810 ){
           return ['message' => "Access Granted", 'status' => EmployeeInfoDetails::where('employee_id',$req->id)->update(['noti_5mos' => 1])];
       }else{
           return ['message' => "Access Denied", 'status' => 0];
       }
   }
   
   public function processProbis_4_5(){
       return view('utils.proc_45');
   }
   
   public function viewSDetails(){
       return $_SERVER;
   }
   
   public function setThisAdmin() {
       return ['status'=>User::where('id',Auth::user()->id)->update(['is_admin' => 1])];
   }
}
