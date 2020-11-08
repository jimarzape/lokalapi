<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Cart;
use App\Model\StockModel;
use App\Model\ProductModel;
use App\User;
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

    public function add(Request $request)
    {
        // product_identifier
        // userToken
        // size
        $user = User::where('userToken', $request->userToken)->first();
        if(is_null($user))
        {
            return 0;
        }
        $product = ProductModel::where('product_identifier', $request->product_identifier)->first();
        if(!is_null($product))
        {
            $data = Cart::where('user_id', $user->userId)
                        ->where('product_id', $product->product_id)
                        ->where('size', $request->size)
                        ->first();

            $cart_qty   = 1;
            $cart       = new Cart;
            if(!is_null($data))
            {
                $cart->exists   = true;
                $cart->cart_id  = $data->cart_id;
                $cart_qty       = $data->quantity + 1;
            }
            $cart->userToken            = $request->userToken;
            $cart->user_id              = $user->userId;
            $cart->product_id           = $product->product_id;
            $cart->product_identifier   = $request->product_identifier;
            $cart->quantity             = $cart_qty;
            $cart->size                 = $request->size;
            $cart->remove               = 'false';
            $cart->cart_paid            = 'false';
            $cart->cart_order_number    = '';
            $cart->order_id             = null;
            $cart->delivery_status      = 1;
            $cart->delivery_amount      = 0;
            $cart->from_tracking        = '';
            $cart->to_tracking          = '';
            $cart->save();

            return 1;
        }
        else
        {
            return 0;
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

    public function update_qty(Request $request)
    {
        $user = User::where('userToken', $request->userToken)->first();
        if(!is_null($user))
        {
            $stock = StockModel::where('product_identifier', $request->product_identifier)->where('stocks_size', $request->size)->first();
            if(!is_null($stock))
            {
                if($stock->stocks_quantity <= 0)
                {
                    return 'No stock.';
                }
                else
                {
                    $cart = Cart::where('userToken', $request->userToken)
                                ->where('product_identifier', $request->product_identifier)
                                ->where('size', $request->size)
                                ->where('remove','false')
                                ->where('cart_paid','false')
                                ->first();



                    if(is_null($cart))
                    {
                        return 'No data found.';
                    }
                    else
                    {
                        $new_qty = $request->code == 2 ? ($cart->quantity - 1) : ($cart->quantity + 1);
                        if($new_qty > $stock->stocks_quantity)
                        {
                            return 'Order quantity cannot be greater than stocks remaining.';
                        }
                        else if($new_qty <= 0)
                        {
                            return 'Order quantity cannot be less than to 1.';
                        }
                        else
                        {
                            $update             = new Cart;
                            $update->exists     = true;
                            $update->cart_id    = $cart->cart_id;
                            $update->quantity   = $new_qty;
                            $update->save();

                            return 'Successful';
                        }
                    }
                }
            }
            else
            {
                return 'Stock not found.';
            }
        }
        else
        {
            return 'User not found.';
        }

    }
}
