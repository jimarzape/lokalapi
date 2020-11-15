<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Cart;
use App\Model\Seller;
use App\User;

class DeliveryFeeChecker extends Controller
{
    public function mr_speedy(Request $request)
    {
    	try
    	{
    		$curl = curl_init();
	        curl_setopt($curl, CURLOPT_URL, 'https://robotapitest.mrspeedy.ph/api/business/1.1/calculate-order');
	        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	        curl_setopt($curl, CURLOPT_HTTPHEADER, ['X-DV-Auth-Token: 6C1B10F6EF77A03340E73C4B264772864B73FE19']);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	        $data = [
	            'matter' => 'TShirts',
	            'total_weight_kg' => $request->weight,
	            'points' => [
	                [
	                    'address' => $request->from_address,
	                ],
	                [
	                    'address' => $request->address,
	                ],
	            ],
	        ];

	        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
	        $result = curl_exec($curl);
	        if (!$result) {
	            throw new \Exception(curl_error($curl), curl_errno($curl));
	        }
	        return $result;
    	}
    	catch(\Exception $e)
    	{
    		return 'SERVER ERROR';
    	}
    	
    }

    /*
	* @param userToken
    */
    public function ninjavan(Request $request)
    {
    	try
    	{
    		$response['shipping_fee'] 	= 0;
    		$response['fee_brk'] 		= array();
    		$user 			= User::where('userToken', $request->userToken)->first();
    		if(!is_null($user))
    		{
    			$_carts = Cart::leftjoin('products','products.product_id','cart.product_id')
    						  ->leftjoin('stocks',function($join){
    						  		$join->on('stocks.stocks_size','cart.size');
    						  		$join->on('stocks.product_id','products.product_id');
    						  })
	    					  ->where('cart.userToken', $request->userToken)
	    					  ->where('remove','false')
	    					  ->where('cart_paid','false')
	    					  ->get()->toArray();

    			$data = array();
    			foreach($_carts as $cart)
    			{
    					$data[$cart['seller_id']][] = $cart;
    			}

    			foreach($data as $key => $items)
    			{
    				$collect 	= collect($items);
    				$weight 	= $collect->sum('stocks_weight');
    				$kg 		= is_null($weight) ? 0 : ($weight / 1000);
    				$seller 	= Seller::where('sellers.id', $key)->first();
    				if(!is_null($seller))
    				{
    					$is_province 	= true;
    					$user_province 	= $user->userProvince =='METRO-MANILA' ? false : true;
    					if($seller->province == 1339 || $seller->province == 1374 || $seller->province == 1375 || $seller->province == 1376) //manila rate
    					{
    						$is_province = false;
    					}
    					$dr_manila 		= 0;
    					$dr_province 	= 0;
    					if($kg <= 1)
    					{
    						$dr_manila 		= 100;
    						$dr_province 	= 150;
    					}
    					else if($kg > 1 && $kg <= 3)
    					{
    						$dr_manila 		= 140;
    						$dr_province 	= 220;
    					}
    					else if($kg > 3)
    					{
    						$dr_manila 		= 50 * $kg;
    						$dr_province 	= 80 * $kg;
    					}

    					

    					if(!$is_province && !$user_province)
    					{
    						$response['shipping_fee'] += $dr_manila;
    						array_push($response['fee_brk'], $dr_manila);
    					}
    					else
    					{
    						$response['shipping_fee'] += $dr_province;
    						array_push($response['fee_brk'], $dr_province);
    					}
    				}
    			}

    			return response()->json($response);
    		}
    		else
    		{
    			return 'ERROR:USER NOT FOUND';
    		}
    	}
    	catch(\Exception $e)
    	{
    		return 'SERVER ERROR';
    	}
    }
}
