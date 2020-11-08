<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table    = 'orders';
    public $timestamps  = true;
    public $primaryKey  = 'id';
}
