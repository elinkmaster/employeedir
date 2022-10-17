<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper {

    public static function getDifferentMonths($date){
        $today = now();
        $hire_date = Carbon::parse($date);

        if($hire_date->year != $today->year)
        {
            $hire_date->year = $today->year;
            $hire_date->month = 0;
        }
        $hire_date->day = 1;

        return $hire_date->diffInMonths($today);
    }
}
