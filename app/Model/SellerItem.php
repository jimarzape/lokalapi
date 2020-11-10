<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerItem extends Model
{
    protected $table    = 'seller_order_item';
    public $timestamps  = true;
    public $primaryKey  = 'eller_order_item_id';
}
