<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AddressList extends Model
{
    protected $table    = 'address_list';
    public $timestamps  = false;
    public $primaryKey  = 'id';

    public function scopedata($query, $province)
    {
    	$query->where('province','LIKE','%'.$province.'%');
    }

    public function scopebrgy($query, $city)
    {
    	return $query->where('city', $city);
    }

    public function scopemunicipality($query, $province)
    {
        return $query->where('province', $province);
    }
}
