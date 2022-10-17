<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CementingExpectations extends Model
{
    protected $table = 'setting_expectations';
    
    protected $fillable = [
        'se_link_id', 'se_com_id', 'se_focus', 'se_skill', 'se_when_use', 'se_how_use', 'se_why_use', 'se_expectations', 'se_timeframe', 'se_comments'
    ];    
}
