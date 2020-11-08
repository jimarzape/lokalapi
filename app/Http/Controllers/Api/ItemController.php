<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\ProductModel;
use App\Model\StockModel;
use App\Model\ItemRating;
use App\Model\Cart;
use App\User;

class ItemController extends Controller
{
    public function __construct()
    {

    }

    public function search(Request $request)
    {
    	try
    	{
    		$data = ProductModel::generic()->search($request->keyword)->take(30)->get();
    		return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    }

    public function item_brand(Request $request)
    {
    	try
    	{
    		// dd($request->all());
    		$data = ProductModel::generic()->bybrand($request->brand_identifier)->get();
    		return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    }

    public function new_arrivals()
    {
        try
        {
            // dd($request->all());
            $data = ProductModel::generic()->where('products.new_arrival','true')->get();
            return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function list_random(Request $request)
    {
        try
        {
            $pageNumber = intval($request->pageNumber);
            $limit      = 10;
            $offset     = ($limit * $pageNumber) - $limit;
            $data       = ProductModel::generic()->inRandomOrder()->skip($offset)->take($limit)->get();
            return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function variant(Request $request)
    {
        try
        {
            $data = StockModel::where('product_identifier', $request->product_identifier)->where('stocks_quantity','>',0)->get();
            return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function rate(Request $request)
    {
        // ItemRating
        // Cart
        $user = User::where('userToken', $request->user_token)->first();
        if(is_null($user))
        {
            return 'ERROR';
        }
        else
        {
            $product = ProductModel::where('product_identifier', $request->product_identifier)->first();
            if(is_null($product))
            {
                return 'ERROR';
            }
            else
            {
                $exists = ItemRating::where('cart_id', $request->cart_id)->exists();

                if($exists)
                {
                    return 'ERROR : ALREADY RATED';
                }
                else
                {
                    $rate                       = new ItemRating;
                    $rate->user_token           = $request->user_token;
                    $rate->product_identifier   = $request->product_identifier;
                    $rate->rating               = $request->rating;
                    $rate->comment              = $request->comment;
                    $rate->product_id           = $product->product_id;
                    $rate->user_id              = $user->userId;
                    $rate->cart_id              = $request->cart_id;
                    $rate->rating_date          = date('Y-m-d H:i:s');
                    $rate->save();

                    $cart                       = new Cart;
                    $cart->exists               = true;
                    $cart->cart_id              = $request->cart_id;
                    $cart->delivery_status      = 8; //means reviewed
                    $cart->save();

                    return 'SUCCESS';
                }   
                
            }
        }
    }
}
