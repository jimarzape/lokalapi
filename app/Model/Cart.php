<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table    = 'cart';
    public $timestamps  = true;
    public $primaryKey  = 'cart_id';


    public function scopebyuser($query, $userId)
    {
    	return $query->leftjoin('products','products.itemId','cart.cartItemId')
    				 ->leftjoin('users','users.userId','cart.cartUserId')
    				 ->where('cart.cartIsRemoved','false')
    				 ->where('users.userId', $userId);
    }
}
