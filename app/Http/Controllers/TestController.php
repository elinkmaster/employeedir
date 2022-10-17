<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        $today = now();
        $hire_date = Carbon::parse('2022-07-22');

        if($hire_date->year != $today->year)
        {
            $hire_date->year = $today->year;
            $hire_date->month = 0;
        }
        $hire_date->day = 1;
        dd($hire_date->diffInMonths($today));

    }
}
