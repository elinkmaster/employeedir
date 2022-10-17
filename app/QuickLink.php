<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickLink extends Model
{
    protected $table = 'quick_link';
    
    protected $fillable = [
        'rf_lnk_id', 'rf_focus', 'rf_comments'
    ];    
}
