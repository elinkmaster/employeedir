<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkingMaster extends Model
{
    protected $table = 'linking_master';
    
    protected $fillable = [
        'lnk_date', 'lnk_linker', 'lnk_linkee', 'lnk_type', 'lnk_acknw'
    ];    
}
