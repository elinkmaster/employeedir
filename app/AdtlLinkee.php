<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdtlLinkee extends Model
{
    protected $table = 'adtl_linkees';

    protected $fillable = [
        'adtl_linker',
        'adtl_linkee',
        'adtl_row',
        'adtl_added_by',
        'adtl_date_added',
        'adtl_status'
    ];

    public $timestamps = false;
}
