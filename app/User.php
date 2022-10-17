<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon; 
use Illuminate\Support\Facades\DB;
use App\LeaveRequest;
use Spatie\Valuestore\Valuestore;


class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    public $table = "employee_info";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    #####################################################
    //              RELATIONSHIPS

    public function supervisor()
    {
        return $this->belongsTo('App\User', 'supervisor_id');
    }
    public function account(){
        return $this->belongsTo('App\ElinkAccount', 'account_id');
    }
    public function manager(){
        return $this->belongsTo('App\User', 'manager_id');
    }
    
    public function details(){
        return $this->belongsTo('App\EmployeeInfoDetails','employee_id');
    }

    #####################################################
    /*                  SCOPES                         */
    public function scopeAllExceptSuperAdmin($query){
        return $this->where('id', '<>', '1');
    }


    public function scopeBusinessLeaders($query){
        return $query->whereIn('usertype', [2,3])->orWhere('supervisor_name', '=', $this->generalManager());
    }

    public function scopeRankAndFile($query){
        return $query->where('usertype', '=', 1);
    }
    
    public function scopeLeaveRequestCount(){
        return LeaveRequest::whereHas('employee', function($query){
            $query->where('supervisor_id', '=', $this->id);
        })->orWhereHas('employee', function($query){
            $query->where('manager_id', '=', $this->id);
        })->whereNull('approve_status_id')->count();
    }

    public function scopeFindByCustomName($query, $custom_name){
        $s_name =  explode(',', $custom_name);
        $s_name = array_filter(array_map('trim', $s_name));
        
        $matched_users = $query->whereIn('last_name', $s_name)->whereIn('first_name', $s_name);
        
        return $matched_users;
    }

    public function scopeSeparatedEmployees($query){
        return $query->onlyTrashed()->orWhere('status', '=', '2');
    }

    public function scopeActiveEmployees($query){
        return $query->whereNull('deleted_at')->where('status', '!=', '2');
    }

    public function scopeFindByEmail($query, $email){
       
        return $query->where('email', '=',$email)->orWhere('email2','=',$email)->orWhere('email3', '=', $email);
    }

    #####################################################
    public function generalManager(){
        $settings = Valuestore::make(storage_path('app/settings.json'));
      //  return User::where("email", "=", 'ferdinandpasion@elink.com.ph')->first();
	$manager = DB::table('employee_info')->where('email', 'ferdinandpasion@elink.com.ph')->first();
	return $manager->last_name .', '. $manager->first_name;
    }

    public function supervisor_email(){
        $supervisor_email = '';
        if($this->supervisor != NULL){
            $supervisor_email = $this->supervisor->email;
        } else {
            $supervisors = User::findByCustomName($this->supervisor_name)->get();
            if ($supervisors->count() > 0){
                $supervisor_email = $supervisors->first()->email;
            }
        }
        return $supervisor_email;
    }

    public function manager_email(){
        $manager_email = '';
        if($this->manager != NULL){
            $manager_email = $this->manager->email;
        } else {
            $managers = User::findByCustomName($this->manager_name)->get();
            if ($managers->count() > 0){
                $manager_email = $managers->first()->email;
            }
        }
        return $manager_email;
    }

    public function fullname()
    {
        return $this->last_name .', '. $this->first_name;
    }

    public function fullname2()
    {
        return $this->first_name .' '. $this->last_name;
    }

    public function active_state(){
        return $this->trashed() || $this->status == 2 ? "(inactive)" : '';
    }

    public function reactivate(){
        $this->restore();
        $this->status = 1;
        return $this->save();
    }

    public function prettyBirthDate()
    {
        if(isset($this->birth_date)){
            $dt = Carbon::parse($this->birth_date);
            return $dt->toFormattedDateString();
        }else{
            return "--";
        }
        
    }
    public function prettydatehired()
    {
        if(isset($this->hired_date)){
            $dt = Carbon::parse($this->hired_date);
            return $dt->toFormattedDateString();
        } else {
            return "--";
        }
    }
    public function prettyproddate()
    {
        if(isset($this->prod_date)){
            $dt = Carbon::parse($this->prod_date);
            return $dt->toFormattedDateString();
        } else {
            return "--";
        }
    }
    public function prodDate()
    {
        if (isset($this->prod_date)) {
            $dt = Carbon::parse($this->prod_date);
            return $dt->format('m/d/Y');
        } else {
            return "";
        } 
    }
    public function birthDate()
    {
        if (isset($this->birth_date)) {
            $dt = Carbon::parse($this->birth_date);
            return $dt->format('m/d/Y');
        }else{
            return "";
        }
    }
    public function dateHired()
    {
        if (isset($this->hired_date)) {
            $dt = Carbon::parse($this->hired_date);
            return $dt->format('m/d/Y');
        } else {
            return "";
        }
    }
   
    public function status(){
        switch ($this->status) {
            case 1:
                return "Active"; 
            case 2:
                return "Inactive";
            default:
                return "";
        }
    }
    public function isActive(){
        return $this->status == 1 && !$this->trashed();
    }

    public function gender(){
        switch ($this->gender) {
            case 1:
                return "Male"; 
            case 2:
                return "Female"; 
            case 3:
                return "Other"; 
            case 4:
                return "Prefer not to say"; 

            default:
                return "--";
        }
    }

    public function isRankAndFile(){
        return $this->usertype == 1 || !$this->isBusinessLeader();
    }
    
    public function isSupervisor(){
        $this->usertype == 2;
    }

    public function isManager(){
        $this->usertype == 3;
    }

    public function isBusinessLeader(){
        return $this->usertype == 2 && $this->supervisor_name == $this->generalManager();
    } 

    public function isAdmin(){
        return $this->is_admin == 1;
    }
    public function isHR(){
        return $this->is_hr == 1;
    }
    public function isERP(){
        return $this->is_erp == 1;
    }
    public function isRA(){
        return $this->is_ra == 1;
    }

    public function isDeleted(){
        return $this->status == 2 || $this->trashed();
    }
    public function getLinkees(){
        	$main_names = DB::select("
	        SELECT 
        	    id, first_name, last_name, email
	        FROM
	            elink_employee_directory.employee_info AS ei,
        	    adtl_linkees AS al
	        WHERE
        	    ((supervisor_id = $this->id
	         	OR manager_id = $this->id)
	                AND status = 1
        	        AND deleted_at IS NULL)
	                OR (al.adtl_linker = $this->id
        	        AND ei.id = al.adtl_linkee)
                
	        GROUP BY ei.id,ei.first_name,ei.last_name,ei.email
	        ORDER BY last_name ASC;
    		");

	return $main_names;
	}
}
