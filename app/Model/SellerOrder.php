<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerOrder extends Model
{
    protected $table    = 'seller_order';
    public $timestamps  = true;
    public $primaryKey  = 'seller_order_id';
}
