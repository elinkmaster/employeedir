<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempDetails extends Model
{
    protected $table = 'temp_emp_info';
    
    protected $fillable = [
        'changedate', 'o_current_address', 'n_current_aaddress', 'o_contact_num', 'n_contact_num', 'o_emergency', 'n_emergency', 'o_rel', 'n_rel'
    ];    
}
