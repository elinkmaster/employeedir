<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveCreditArchive extends Model
{
    protected $fillable = [
      'employee_id',
      'credit',
      'type',
      'month',
      'year',
      'leave_id',
      'status',  
    ];

    protected $hidden = [
        'updated_at',
        'delete_at'
    ];
}
