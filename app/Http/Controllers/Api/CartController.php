<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Cart;
use DB;

class CartController extends Controller
{
    public function __construct()
    {

    }


    public function index(Request $request)
    {
    	try
    	{
    		// $data = Cart::select(DB::raw('SUM(cart.quantity * products.itemPrice) as amountDue'))->byuser($request->userId)->get();

            $data = Cart::leftjoin('products','products.product_identifier','cart.product_identifier')
                        ->leftjoin('brands','brands.brand_id','products.brand_id')
                        ->leftjoin('stocks', function($join){
                            $join->on('stocks.product_id','products.product_id');
                            $join->on('stocks.stocks_size', 'cart.size');
                        })
                        ->leftjoin('sellers','sellers.id','products.seller_id')
                        ->where('cart.remove','false')
                        ->where('cart.cart_paid','false')
                        // ->where('stocks.stocks_size',stocks_size)
                        ->where('cart.userToken', $request->userToken)
                        ->selectRaw(
                            '*, (cart.quantity * stocks.stocks_weight) as subotal_weight, (cart.quantity * stocks.stocks_price) as subtotal'
                        )
                        // ->groupBy('cart_id')
                        ->orderBy('cart.cart_time_stamp')
                        ->get();

    		return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    }

    public function remove(Request $request)
    {
        try
        {
            // $update['remove'] = 'true';
            $cart = Cart::where('userToken', $request->userToken)
                        ->where('product_identifier', $request->product_identifier)
                        ->where('remove','false')
                        ->where('size', $request->size)
                        ->first();
            if(is_null($cart))
            {
                return intval(0);
            }
            else
            {
                $update             = new Cart;
                $update->exists     = true;
                $update->cart_id    = $cart->cart_id;
                $update->remove     = 'true';
                $update->save();

                return intval(1);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }
}
