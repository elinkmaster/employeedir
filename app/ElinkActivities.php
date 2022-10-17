<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElinkActivities extends Model
{
    //
    protected $table = "elink_activities";

    public function scopeThisMonth($query){
    	return $query->whereRaw('MONTH(activity_date) =' . date('n') . " AND YEAR(activity_date) = " .date('Y') . ' OR MONTH(activity_date) = ' . date("n", strtotime("first day of previous month")) . " AND YEAR(activity_date) = " . date("Y", strtotime("first day of previous month")));
    }
}
