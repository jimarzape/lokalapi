<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\OrderModel;
use App\Model\CancelOrder;
use App\Model\Cart;
use App\Model\SellerOrder;

class OrderController extends Controller
{
    public function order_no_gen()
    {
    	return order_number();
    }

    public function cancel_order_gen_no()
    {
    	return cancel_number();
    }

    public function status(Request $request)
    {
    	$order = OrderModel::where('order_number', $request->order_number)->first();
    	if(is_null($order))
    	{
    		return 'ERROR'; //should have a better return error, update it if the app is ready for imporvements
    	}
    	$data[]['delivery_status'] = $order->delivery_status;
    	return response()->json($data);
    }

    public function cancel_order(Request $request)
    {
    	$response['data']['code'] 			= 'error';
    	$response['data']['cancel_number'] 	= null;
		try
		{
			$order = OrderModel::where('order_number', $request->order_number)->first();
			if(!is_null($order))
			{
				$cn_number 					= cancel_number();
				$can 						= new CancelOrder;
				$can->order_id 				= $order->id;
				$can->user_token 			= $request->user_token;
				$can->order_number 			= $request->order_number;
				$can->cancellation_number 	= $cn_number;
				$can->cancellation_date 	= date_now();
				$can->save();

				$update_order 					= new OrderModel;
				$update_order->exists 			= true;
				$update_order->id 				= $order->id;
				$update_order->delivery_status 	= 5;
				$update_order->save();

				$cart['delivery_status'] = 5;
				Cart::where('cart_order_number', $request->order_number)->update($cart);

				$seller['seller_delivery_status'] = 5;
				SellerOrder::where('order_id', $order->id)->update($seller);

				$response['data']['code'] 			= 'success';
	    		$response['data']['cancel_number'] 	= $cn_number;
    		}

		}
		catch(\Exception $e)
		{
			// return "ERROR";
			// return $e->getMessage();
		}

		return response()->json($response);
		
    }

    public function cancel_list(Request $request)
    {
    	$cancel = CancelOrder::leftjoin('orders','orders.id','cancellation.order_id')
    						 ->leftjoin('cart','cart.order_id','cancellation.order_id')
    						 ->leftjoin('payment_methods','payment_methods.id','orders.order_payment_type')
    }
}
