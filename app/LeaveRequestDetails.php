<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Valuestore\Valuestore;
use Illuminate\Support\Facades\DB;

class LeaveRequestDetails extends Model
{
    protected $table = 'leave_request_details';
    
    protected $fillable = [
        'leave_id', 'date', 'length', 'pay_type'
    ];    
}
