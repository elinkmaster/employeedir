<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDepartment extends Model
{
    use SoftDeletes;
    protected $table = "employee_department";
    public $timestamps = false;

    public function manager(){
    	return $this->belongsTo('App\User', 'manager_id');
    }
    public function division(){
    	return $this->belongsTo('App\ElinkDivision', 'division_id');
    }
    public function account(){
    	return $this->belongsTo('App\ElinkAccount', 'account_id');
    }
}
