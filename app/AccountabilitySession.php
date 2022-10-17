<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountabilitySession extends Model
{
    protected $table = 'accblty_conv';
    
    protected $fillable = [
        'ac_link_id', 'ac_com_id', 'ac_focus', 'ac_skill', 'ac_when_use', 'ac_why_use', 'ac_expectations', 'ac_expectation_date', 'ac_comments', 'ac_feedback'
    ];    
}
