<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDependents extends Model
{
    protected $table = 'employee_dependents';
    
    protected $fillable = [
        'employee_num', 'dependent', 'bday'
    ];    
}
