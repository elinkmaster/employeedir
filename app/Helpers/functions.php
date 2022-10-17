<?php

function monthDay($prod_date)
{
    if (isset($prod_date)) {
        $dt = Carbon::parse($prod_date);
        return $dt->format('M d');
    } else {
        return "";
    } 
}