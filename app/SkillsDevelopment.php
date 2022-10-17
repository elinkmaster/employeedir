<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkillsDevelopment extends Model
{
    protected $table = 'sda';
    
    protected $fillable = [
        'sda_lnk_id', 'sda_com_id', 'sda_type', 'sda_date_call', 'sda_call_sel', 'sda_www_u_said', 'sda_www_i_said', 
        'sda_wcm_u_said', 'sda_wcm_i_said', 'sda_take_away', 'sda_timeframe', 'sda_comments', 'sda_feedback'
    ];    
}
