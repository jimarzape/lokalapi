<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $table    = 'sellers';
    public $timestamps  = true;
    public $primaryKey  = 'id';

    public function scopedata($query, $id)
    {
    	return $query->leftjoin('refprovince','refprovince.provCode = sellers.province')
    		  		 ->leftjoin('refcitymun','refcitymun.citymunCode','sellers.city')
    		  		 ->leftjoin('refbrgy','refbrgy.brgyCode','sellers.brgy')
    		  		 ->where('sellers.id', $id);
    }
}
