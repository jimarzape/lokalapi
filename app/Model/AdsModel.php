<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdsModel extends Model
{
    protected $table    = 'advertisers';
    public $timestamps  = false;
    public $primaryKey  = 'id';

    public function scopedata($query, $adsType, $adsActive = 'true')
    {
    	$query->where('adsActive', $adsActive)
    		  ->where('adsType', $adsType);
    }
}
