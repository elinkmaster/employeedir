<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveCredits extends Model
{
    protected $table = 'leave_credits';
    
    /*
     
    type column:
    1 = Add Current Credit
    2 = Add Previous Year Remaining Credit (will expire after 6 months)
    3 = Leave Conversion - Maximum of 5 days from the previous leave credits - Paid on May Payroll
    5 = Use Credit 

    */
    
    protected $fillable = [
        'employee_id', 'credit', 'type', 'month', 'year', 'leave_id'
    ];    
}
