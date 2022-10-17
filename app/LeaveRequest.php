<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Valuestore\Valuestore;
use Illuminate\Support\Facades\DB;

class LeaveRequest extends Model
{
    protected $table = 'leave_request';

    public function employee(){
    	return $this->belongsTo('App\User', 'employee_id');
    }

    public function leave_type(){
    	return $this->belongsTo('App\LeaveType');
    }

    public function pay_type(){
    	return $this->belongsTo('App\PayType');
    }
    
    public function leave_details_from(){
        return $this->hasMany('App\LeaveRequestDetails',"leave_id")->orderBy("date","asc")->take(1);

    }
    
    public function leave_details_to(){
        return $this->hasMany('App\LeaveRequestDetails',"leave_id")->orderBy("date","desc")->take(1);
    }

    public function leave_details(){
        return $this->hasMany('App\LeaveRequestDetails',"leave_id");
    }


    public function scopeUnapproved($query){
        return $query->where('approve_status_id', '=', 0)->orWhereNull('approve_status_id');
    }

    public function recipients(){
        $settings = Valuestore::make(storage_path('app/settings.json'));

        $main_recipients = json_decode($settings->get('leave_email_main_recipients'));
        $business_leaders = json_decode($settings->get('business_leaders'));

        $email_recipients = [];
        $business_leader_emails = [];

        if ($main_recipients != NULL){
            foreach ($main_recipients as $key => $email) {
                array_push($email_recipients, $email->value);
            }
        }
        if ($business_leaders != NULL){
            foreach ($business_leaders as $key => $email) {
                array_push($business_leader_emails, $email->value);
            }
        }

        // GET SUPERVISOR AND MANAGER EMAILS
        $supervisor_recipient = $this->employee->supervisor_email();
        $manager_recipient = $this->employee->manager_email();

        if ($this->employee->isManager() || $this->employee->isBusinessLeader()){
            array_push($email_recipients, $this->employee->generalManager()->email);
        } else if ($this->employee->isSupervisor()) {
            array_push($email_recipients, $this->employee->generalManager()->email);
            array_push($email_recipients, $manager_recipient);
        } else if ($this->employee->isRankAndFile()){
            array_push($email_recipients, $supervisor_recipient);
            array_push($email_recipients, $manager_recipient);
        } 
        return array_values(array_filter(array_unique($email_recipients)));
    }

    public function scopeManagedBy($query, $user){
        $id = $user->id;
        return $query->whereHas('employee', function($q) use ($id){
            $q->where('supervisor_id', '=',$id);
        })->orWhereHas('employee', function($q) use ($id){
            $q->where('manager_id', '=',$id);
        });
    }

    public function scopeMyLeaves($query,$user){
        $id = $user->id;
        return $query->where('employee_id','=',$id);
    }

    public function scopeStatus(){
        if($this->approve_status == 1){
            return "Approved";
        } else if($this->approved_by_signed_date != NULL){
            return "Approved";
        } else if($this->noted_by_signed_date != NULL){
            return "Approved";
        } else if($this->recommending_approval_by_signed_date != NULL){
            return "Recommended";
        } else {
            return "Not yet approved";
        }
    }
    public function getApprovalStatus(){
        if($this->approve_status_id == 0){
            return '<span class="fa fa-refresh"></span> Waiting for response';
        } else if($this->approve_status_id == 1){
            return '<span class="fa fa-check" style="color: green"></span> Approved';
        } else if($this->approve_status_id == 2){
            return '<span class="fa fa-thumbs-down" style="color: darkred"></span> Declined<br>Reason for disapproval: ' . $this->reason_for_disapproval;
        }
        return 'Waiting for response';
    }
    public function scopeLeaveDays(){
        if($this->number_of_days == 0.5){
            return "half day";
        } else if($this->number_of_days % 1 == 0.5){
            if((int)$this->number_of_days == 1){
                return (int)$this->number_of_days . ' day and a half days';
            }
            return (int)$this->number_of_days . ' days and a half days';
        } else if((int)$this->number_of_days == 1){
            return (int)$this->number_of_days . ' day';
        }
        else {
             return (int)$this->number_of_days . ' days';
        }
    }

    public function scopeIsForRecommend(){
        //return (Auth::user()->id == $this->employee->supervisor_id) && ($this->recommending_approval_by_signed_date == NULL && $this->approve_status_id != 2) || Auth::user()->isAdmin();
        return Auth::user()->id == $this->employee->supervisor_id && $this->recommending_approval_by_signed_date == NULL && $this->approve_status_id != 2;
    }

    public function scopeIsForApproval(){
        //return (Auth::user()->id == $this->employee->manager_id) && ($this->approved_by_signed_date == NULL && $this->approve_status_id != 2) || Auth::user()->isAdmin();
        return Auth::user()->id == $this->employee->manager_id && $this->approved_by_signed_date == NULL && $this->approve_status_id != 2;
    }

    public function scopeIsForNoted(){
        //return Auth::user()->isHR() && ($this->noted_by_signed_date == NULL && $this->approve_status_id != 2) || Auth::user()->isAdmin();
        return Auth::user()->isHR() && ($this->noted_by_signed_date == NULL && $this->approve_status_id != 2);
    }

    public function scopeCanBeDeclined(){
        return ($this->isForRecommend() || $this->isForApproval() || $this->isForNoted()) && $this->approve_status_id != 2;
    }
    
    public function getEmployeeName($id){
        return DB::table('employee_info')->where('id', $id)->get();
    }

    public static function getBlockedDates($dept){
        if(Auth::user()->isManager())
            DB::select("
                SELECT 
                    d.date AS cwd
                FROM
                    leave_request_details AS d
                        LEFT JOIN
                    leave_request AS l ON l.id = d.leave_id
                        LEFT JOIN
                    employee_info AS e ON l.employee_id = e.id
                WHERE
                    (e.team_name = '$dept' or e.usertype = 3 )
                        AND d.date >= CURDATE()
                ORDER BY d.date ASC
            ");
        else
            return DB::select("
                SELECT 
                    d.date AS cwd
                FROM
                    leave_request_details AS d
                        LEFT JOIN
                    leave_request AS l ON l.id = d.leave_id
                        LEFT JOIN
                    employee_info AS e ON l.employee_id = e.id
                WHERE
                    e.team_name = '$dept'
                        AND d.date >= CURDATE()
                ORDER BY d.date ASC
            ");
    }
    
    public static function getCWD(){
        return DB::select("
            SELECT 
                DATE_FORMAT(start_date, '%Y-%m-%d') AS cwd
            FROM
                events
            WHERE
                start_date >= CURDATE();"
        );
    }
    
}
