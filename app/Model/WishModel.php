<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WishModel extends Model
{
    protected $table    = 'wishlist';
    public $timestamps  = true;
    public $primaryKey  = 'wishlist_id';
}
